<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function decodeToken(Request $request)
    {
        // Get the token from the request header
        $token = $request->bearerToken(); // Extracts the token from Authorization header

        // Decode and validate the token
        $accessToken = PersonalAccessToken::findToken($token);

        if ($accessToken) {
            // Token is valid
            $user = $accessToken->tokenable; // Get the associated user or model
            return response()->json([
                'message' => 'Token is valid',
                'user' => $user,
                'token_details' => $accessToken,
            ]);
        } else {
            // Token is invalid
            return response()->json(['message' => 'Invalid token'], 401);
        }
    }
}
