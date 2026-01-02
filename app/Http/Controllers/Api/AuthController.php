<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\Role;

class AuthController extends Controller
{
    /* =======================
     * REGISTER
     * ======================= */
    public function register(Request $request)
    {
    // 1️⃣ Validate request
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|string|min:6|confirmed',
        'role'       => 'required|in:tenant,landlord,agent,admin',
    ]);

    // 2️⃣ Create user
    $user = User::create([
        'name'       => $validated['first_name'] . ' ' . $validated['last_name'],
        'first_name' => $validated['first_name'],
        'last_name'  => $validated['last_name'],
        'email'      => $validated['email'],
        'password'   => Hash::make($validated['password']),
        'status'     => 'active',
    ]);

    // 3️⃣ Ensure role exists, then assign to user
    $allowedRoles = ['tenant', 'agent', 'landlord', 'admin'];
    if (!in_array($validated['role'], $allowedRoles)) {
        return response()->json(['message' => 'Invalid role.'], 422);
    }
    $role = Role::firstOrCreate(['name' => $validated['role']]);

    // 4️⃣ Determine pivot status
    // Pending for admin; approved for tenant, landlord, and agent
    $pivotStatus = in_array($validated['role'], ['admin'])
        ? 'pending'
        : 'approved';

    $user->roles()->attach($role->id, [
        'status' => $pivotStatus
    ]);

    // 5️⃣ Generate token only if role is approved
    $token = $pivotStatus === 'approved'
        ? $user->createToken('api-token')->plainTextToken
        : null;

    // 6️⃣ Return JSON response
    return response()->json([
        'message' => 'Registration successful',
        'user'    => [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $role->name,
            'role_status' => $pivotStatus,
        ],
        'token' => $token,
    ], 201);
    }


    /* =======================
     * LOGIN
     * ======================= */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Check if user has any approved role
        $hasApprovedRole = $user->roles()
            ->wherePivot('status', 'approved')
            ->exists();

        if (! $hasApprovedRole) {
            throw ValidationException::withMessages([
                'email' => ['Your account is not approved yet. Please wait for admin approval.'],
            ]);
        }

        // Revoke old tokens and generate new token
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->map(function($role) {
                    return [
                        'name'   => $role->name,
                        'status' => $role->pivot->status
                    ];
                })
            ],
            'token' => $token,
        ]);
    }



    /* =======================
     * LOGOUT
     * ======================= */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /* =======================
     * AUTH USER
     * ======================= */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /* =======================
     * FORGOT PASSWORD
     * ======================= */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent'])
            : response()->json(['message' => 'Unable to send reset link'], 400);
    }

    /* =======================
     * RESET PASSWORD
     * ======================= */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful'])
            : response()->json(['message' => 'Invalid token'], 400);
    }
}
