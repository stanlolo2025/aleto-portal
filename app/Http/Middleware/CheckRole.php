<?php

namespace App\Http\Middleware;

use App\Services\Contracts\AuditLoggerInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!in_array($user->role, $roles)) {
            // Log the access denied attempt
            $this->auditLogger->log('access_denied', $user->id, [
                'description' => "Access denied: {$user->username} attempted {$request->method()} {$request->path()}",
                'metadata' => [
                    'attempted_operation' => $request->method() . ' ' . $request->path(),
                    'user_role' => $user->role,
                    'required_roles' => $roles,
                ],
            ]);

            return response()->json([
                'message' => 'Insufficient permissions for the requested action.',
            ], 403);
        }

        return $next($request);
    }
}
