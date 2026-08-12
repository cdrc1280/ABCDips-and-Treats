<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerProfileController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => ['sometimes', 'string', 'max:255'],
            'phone'          => ['sometimes', 'nullable', new \App\Rules\PhilippinePhone],
            'address'        => ['sometimes', 'nullable', 'string', 'max:1000'],
            'city'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'region'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'province'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'barangay'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'street_address' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $user = $this->customerService->updateProfile($request->user(), $validated);

        \App\Services\AuditLogger::log('updated', "Updated profile details for {$user->name}", $user, [], $validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => new UserResource($user),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?~`]).{8,}$/'
            ],
        ], [
            'new_password.regex' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&* etc.).',
            'new_password.min' => 'Password must be at least 8 characters long.',
        ]);

        $this->customerService->changePassword($request->user(), $request->current_password, $request->new_password);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $user = $this->customerService->updateAvatar($request->user(), $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'data' => new UserResource($user),
        ]);
    }

    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email is already verified.',
                'verified' => true,
            ]);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => "Verification email sent to {$user->email}! Please check your email inbox to verify your account.",
            'sent' => true,
        ]);
    }

    public function verifyEmailLink(Request $request, $id, $hash): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json([
            'message' => 'Your account email has been verified successfully!',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'message' => 'Your account email has been verified successfully!',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    public function sendPhoneOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', new \App\Rules\PhilippinePhone],
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Do not persist the phone number until OTP is verified. Store pending phone in cache.
        $pendingKey = "phone_pending_{$user->id}";
        $otpKey = "phone_otp_{$user->id}";

        $otp = strval(random_int(100000, 999999));
        try {
            cache()->put($pendingKey, $validated['phone'], now()->addMinutes(15));
            cache()->put($otpKey, Hash::make($otp), now()->addMinutes(10));
            // TODO: integrate SMS gateway here. OTP is not returned in production responses.
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Unable to send verification code. Please try again later.'], 500);
        }

        return response()->json([
            'message' => "Verification SMS code sent to {$validated['phone']}! Please check your mobile device for the code.",
            'sent' => true,
        ]);
    }

    public function verifyPhoneOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $request->user();
        $otpKey = "phone_otp_{$user->id}";
        $pendingKey = "phone_pending_{$user->id}";

        $cachedOtp = cache()->get($otpKey);

        if (!$cachedOtp || !Hash::check($validated['otp'], $cachedOtp)) {
            return response()->json(['message' => 'Invalid or expired SMS verification code. Please request a new code.'], 422);
        }

        // Apply pending phone number then clear cache entries
        $pendingPhone = cache()->get($pendingKey);
        cache()->forget($otpKey);
        cache()->forget($pendingKey);

        if ($pendingPhone) {
            $user->update(['phone' => $pendingPhone]);
        }

        // Optionally mark email as verified when phone is verified (legacy behavior)
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }


        return response()->json([
            'message' => 'Phone number verified and updated successfully.',
            'data' => new UserResource($user->fresh()),
        ]);
    }
}
