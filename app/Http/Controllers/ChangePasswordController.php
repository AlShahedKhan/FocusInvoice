<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    /**
     * Update user's password.
     */
    public function updatePassword(Request $request)
    {
        Log::info('Entering show method');
        $user = $request->user();
        Log::info('User details:', ['user' => $user]);

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Validate the incoming request
        $validatedData = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'], // Requires 'new_password_confirmation'
        ]);

        // Verify that the provided current password matches the stored password
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        // Hash and update the new password
        $user->update([
            'password' => Hash::make($validatedData['new_password']),
        ]);

        return response()->json([
            'message' => 'Password changed successfully.',
        ], 200);
    }
}
