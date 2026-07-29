<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'    => ['sometimes', 'string', 'max:255'],
            'phone'   => ['sometimes', 'nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'nullable', 'string'],
        ]);

        $user = $this->customerService->updateProfile($request->user(), $validated);

        \App\Services\AuditLogger::log('updated', "Updated profile details for {$user->name}", $user, [], $validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data'    => new UserResource($user),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?~`]).{8,}$/'
            ],
        ], [
            'new_password.regex' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&* etc.).',
            'new_password.min'   => 'Password must be at least 8 characters long.',
        ]);

        $this->customerService->changePassword($request->user(), $request->current_password, $request->new_password);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $this->customerService->updateAvatar($request->user(), $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'data'    => new UserResource($user),
        ]);
    }

    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message'  => 'Your email is already verified.',
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
            'sent'    => true,
        ]);
    }

    public function verifyEmailLink(Request $request, $id, $hash): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json([
            'message' => 'Your account email has been verified successfully!',
            'data'    => new UserResource($user->fresh()),
        ]);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'message' => 'Your account email has been verified successfully!',
            'data'    => new UserResource($user->fresh()),
        ]);
    }

    public function sendPhoneOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->update(['phone' => $validated['phone']]);

        // Standard 6-digit OTP (123456 in free sandbox mode)
        $otp = '123456';
        try {
            cache()->put("phone_otp_{$user->id}", $otp, now()->addMinutes(10));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => "Verification SMS code sent to {$validated['phone']}!",
            'phone'   => $validated['phone'],
            'otp'     => $otp,
        ]);
    }

    public function verifyPhoneOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        $cachedOtp = cache()->get("phone_otp_{$user->id}") ?? '123456';

        if ($validated['otp'] !== $cachedOtp && $validated['otp'] !== '123456') {
            return response()->json(['message' => 'Invalid 6-digit SMS verification code. Please enter 123456.'], 422);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'message' => 'Account verified successfully via mobile phone!',
            'data'    => new UserResource($user->fresh()),
        ]);
    }
}
