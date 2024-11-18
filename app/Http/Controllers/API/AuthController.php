<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\InviteCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'invite_code' => ['required', 'string', 'max:255'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'sometimes|boolean' // Add this to allow registering as admin for testing
        ]);

        // Validate invite code from the database
        $inviteCode = InviteCode::where('code', $request->invite_code)
            ->where('is_used', false)
            ->first();

        if (!$inviteCode) {
            return response()->json(['message' => 'Invalid or already used invite code.'], 400);
        }

        // Create the new user
        $user = User::create([
            'invite_code' => $request->invite_code,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? $request->is_admin : false,
        ]);

        // Mark the invite code as used
        $inviteCode->update([
            'is_used' => true,
            'used_by' => $user->id,
        ]);

        // Create an authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'token_type' => 'Bearer'])
        ->cookie('token', $token, 60, '/', 'focusinvoice.test', true, true);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'token_type' => 'Bearer'])
        ->cookie('token', $token, 60, '/', 'focusinvoice.test', true, true);
        // ->cookie($token,60);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200)
            ->cookie('token', '', -1); // Delete the cookie
    }

}
