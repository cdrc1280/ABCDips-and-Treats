<?php

namespace App\Http\Controllers\Api;

use App\Mail\EmailOtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * POST /api/otp/email/send
     * Generate and send a 6-digit OTP to the authenticated user's email.
     */
    public function sendEmailOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Rate-limit: max 3 OTPs per 10 minutes
        $recentCount = DB::table('email_otps')
            ->where('email', $user->email)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'message' => 'Too many verification attempts. Please wait 10 minutes before requesting another code.',
            ], 429);
        }

        // Invalidate any existing OTPs for this email
        DB::table('email_otps')
            ->where('email', $user->email)
            ->update(['used' => true]);

        // Generate new 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('email_otps')->insert([
            'email'      => $user->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'used'       => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($user->email)->send(new EmailOtpMail($otp, $user->name ?? 'Valued Customer'));
        } catch (\Throwable $e) {
            \Log::error('OTP email send failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send verification email. Please check your email configuration.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'message' => 'Verification code sent to ' . $user->email . '. Please check your inbox.',
            'success' => true,
        ]);
    }

    /**
     * POST /api/otp/email/verify
     * Verify the 6-digit OTP and mark the user's email as verified.
     */
    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.', 'verified' => true]);
        }

        $record = DB::table('email_otps')
            ->where('email', $user->email)
            ->where('otp', $request->input('otp'))
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired verification code. Please request a new one.',
                'verified' => false,
            ], 422);
        }

        // Mark OTP as used
        DB::table('email_otps')
            ->where('id', $record->id)
            ->update(['used' => true, 'updated_at' => now()]);

        // Mark user as verified
        $user->update(['email_verified_at' => now()]);

        return response()->json([
            'message'  => 'Email verified successfully! You can now proceed with checkout.',
            'verified' => true,
        ]);
    }
}
