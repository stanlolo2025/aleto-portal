<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\MfaCode;
use App\Models\User;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // Record login attempt
        LoginAttempt::create([
            'user_id' => $user?->id,
            'username' => $request->username,
            'ip_address' => $request->ip(),
            'successful' => false,
        ]);

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check lockout
        if ($user->isLocked()) {
            $minutes = $user->lockoutMinutesRemaining();
            return response()->json([
                'message' => "Account is locked. Try again in {$minutes} minutes.",
                'locked_until' => $user->locked_until,
            ], 423);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            $user->increment('failed_login_attempts');

            if ($user->failed_login_attempts >= 3) {
                $user->update(['locked_until' => now()->addMinutes(30)]);

                $this->auditLogger->log('account_lockout', $user->id, [
                    'description' => "Account locked after 3 failed attempts for: {$user->username}",
                ]);

                return response()->json([
                    'message' => 'Account locked for 30 minutes due to multiple failed attempts.',
                    'locked_until' => $user->locked_until,
                ], 423);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Admin requires MFA (only when MAIL_MAILER is not 'log')
        if ($user->isAdmin() && config('mail.default') !== 'log') {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            MfaCode::create([
                'user_id' => $user->id,
                'code_hash' => Hash::make($code),
                'resend_count' => 0,
                'expires_at' => now()->addMinutes(5),
            ]);

            // Send MFA code via email
            \Illuminate\Support\Facades\Mail::raw(
                "Your Aleto Clan Portal login code is: {$code}\n\nThis code expires in 5 minutes. Do not share it with anyone.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Aleto Clan Portal - Login Verification Code');
                }
            );

            return response()->json([
                'message' => 'MFA code sent. Please verify.',
                'requires_mfa' => true,
                'user_id' => $user->id,
            ]);
        }

        // Non-admin: login directly
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_activity_at' => now(),
        ]);

        // Update login attempt as successful
        LoginAttempt::where('user_id', $user->id)->latest()->first()?->update(['successful' => true]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $this->auditLogger->log('login', $user->id, [
            'description' => "User logged in: {$user->username}",
        ]);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function verifyMfa(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($request->user_id);
        $mfaCode = MfaCode::where('user_id', $user->id)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$mfaCode || !$mfaCode->isValid($request->code)) {
            return response()->json(['message' => 'Invalid or expired MFA code'], 401);
        }

        $mfaCode->markUsed();

        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_activity_at' => now(),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $this->auditLogger->log('login', $user->id, [
            'description' => "Admin logged in with MFA: {$user->username}",
        ]);

        return response()->json([
            'message' => 'MFA verified. Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function resendMfa(Request $request): JsonResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::findOrFail($request->user_id);
        $mfaCode = MfaCode::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$mfaCode || !$mfaCode->canResend()) {
            return response()->json([
                'message' => 'Maximum resend attempts reached (3). Please try logging in again.',
            ], 429);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $mfaCode->update([
            'code_hash' => Hash::make($code),
            'resend_count' => $mfaCode->resend_count + 1,
            'expires_at' => now()->addMinutes(5),
            'used' => false,
        ]);

        // In production: dispatch(new \App\Jobs\SendMfaCodeJob($user, $code));

        return response()->json(['message' => 'New MFA code sent.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->auditLogger->log('logout', $user->id, [
            'description' => "User logged out: {$user->username}",
        ]);

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }
}
