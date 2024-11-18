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
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'invite_code' => ['required', 'string', 'max:255'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'sometimes|boolean'
        ]);

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

        // Create a JWT token payload with unique components
        $payload = [
            'iss' => "focusinvoice",           // Issuer
            'sub' => $user->id,                // Subject (user ID)
            'iat' => time(),                   // Issued at (always changes)
            'exp' => time() + 60 * 60,         // Expiry time (1 hour)
            'is_admin' => $user->is_admin,     // Is admin (true/false)
            'random' => Str::random(124),      // Add extra randomness to ensure the token changes each time
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        // Decode the JWT token (for demonstration purposes)
        try {
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

            // If you want to do anything with the decoded token:
            $decodedData = [
                'issuer' => $decoded->iss,
                'user_id' => $decoded->sub,
                'issued_at' => $decoded->iat,
                'expires_at' => $decoded->exp,
                'is_admin' => $decoded->is_admin, // Include is_admin status
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to decode token: ' . $e->getMessage()], 401);
        }

        return response()->json([
            'token' => $jwt,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60,
            'decoded' => $decodedData, // Optionally include decoded data in response
        ])->cookie('token', $jwt, 60, '/', 'focusinvoice.test', true, true);
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

        $user = Auth::user();

        // Create a JWT token payload with unique components
        $payload = [
            'iss' => "focusinvoice",           // Issuer
            'sub' => $user->id,                // Subject (user ID)
            'iat' => time(),                   // Issued at (always changes)
            'exp' => time() + 60 * 60,         // Expiry time (1 hour)
            'is_admin' => $user->is_admin,     // Is admin (true/false)
            'random' => Str::random(124),       // Add extra randomness to ensure the token changes each time
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        // Decode the JWT token (for demonstration purposes)
        try {
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

            // If you want to do anything with the decoded token:
            $decodedData = [
                'issuer' => $decoded->iss,
                'user_id' => $decoded->sub,
                'issued_at' => $decoded->iat,
                'expires_at' => $decoded->exp,
                'is_admin' => $decoded->is_admin, // Include is_admin status
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to decode token: ' . $e->getMessage()], 401);
        }

        return response()->json([
            'token' => $jwt,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60,  // 1 hour
            'decoded' => $decodedData, // Optionally include decoded data in response
        ])->cookie('token', $jwt, 60, '/', 'focusinvoice.test', true, true);
    }




    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            return response()->json(['message' => 'Logged out successfully'], 200)
                ->cookie('token', '', -1); // Remove the token from the cookie
        }

        return response()->json(['error' => 'Token not provided'], 400);
    }
}
