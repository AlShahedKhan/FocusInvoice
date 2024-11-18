<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthenticateJWT
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Decode JWT token using Firebase
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            Log::info('Decoded Token:', (array) $decoded);

            // Retrieve the user using the `sub` field in the token
            $user = User::find($decoded->sub);

            if (!$user) {
                Log::error('JWT Middleware: User not found', ['id' => $decoded->sub]);
                return response()->json(['error' => 'User not found'], 404);
            }

            // Attach user to the request and ensure Auth::user() works
            // $request->setUserResolver(function () use ($user) {
            //     Auth::login($user); // Log the user into the session
            //     return $user;
            // });
            $request->setUserResolver(function () use ($user) {
                if (!Auth::guard('api')->check()) {
                    Auth::guard('api')->login($user); // Use the api guard to log in the user
                    Log::info('User logged in via middleware with API guard:', ['user' => $user]);
                }
                return $user;
            });



            Log::info('JWT Middleware: User retrieved successfully', ['user' => $user]);
        } catch (\Exception $e) {
            Log::error('JWT Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid token', 'message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
