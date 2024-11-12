<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request, User $user)
    {
        $user = $request->user();
        try {
            request()->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required', 'date'],
            ]);

            $user->update([
                'first_name' => request('first_name'),
                'last_name' => request('last_name'),
                'email' => request('email'),
                'phone_number' => request('phone_number'),
                'date_of_birth' => request('date_of_birth')
            ]);

            return response()->json(['message' => 'Profile updated successfully.'], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

    }

}
