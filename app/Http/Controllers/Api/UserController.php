<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function index(): JsonResponse
    {
        $users = User::orderBy('name')->get(['id', 'name', 'username', 'email', 'role', 'created_at']);
        return response()->json(['data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'role' => 'required|in:admin,auditor,government_official',
            'phone' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        $this->auditLogger->log('user_created', $request->user()->id, [
            'description' => "User created: {$user->username} with role {$user->role}",
            'metadata' => ['user_id' => $user->id, 'role' => $user->role],
        ]);

        return response()->json(['message' => 'User created', 'data' => $user], 201);
    }

    public function changeRole(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'role' => 'required|in:admin,auditor,government_official',
        ]);

        $user = User::findOrFail($id);
        $previousRole = $user->role;

        $user->update(['role' => $request->role]);

        $this->auditLogger->log('role_changed', $request->user()->id, [
            'description' => "Role changed for {$user->username}: {$previousRole} → {$request->role}",
            'metadata' => [
                'affected_user_id' => $user->id,
                'previous_role' => $previousRole,
                'new_role' => $request->role,
            ],
        ]);

        return response()->json(['message' => 'Role updated', 'data' => $user]);
    }
}
