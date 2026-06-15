<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    private const TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->last_activity_at) {
            $lastActivity = $user->last_activity_at;
            $minutesInactive = now()->diffInMinutes($lastActivity);

            if ($minutesInactive > self::TIMEOUT_MINUTES) {
                // Revoke current token
                $user->currentAccessToken()->delete();

                return response()->json([
                    'message' => 'Session expired due to inactivity. Please log in again.',
                ], 401);
            }
        }

        // Update last activity
        if ($user) {
            $user->update(['last_activity_at' => now()]);
        }

        return $next($request);
    }
}
