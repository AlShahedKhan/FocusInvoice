<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request, User $user)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string','max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // New validation rule
        ]);

        try {
            $filePath = null; // Initialize file path variable

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete the old profile picture if it exists
                if ($user->profile_picture && Storage::exists($user->profile_picture)) {
                    Storage::delete($user->profile_picture);
                }

                // Store the new profile picture and update the path in the validated data
                $filePath = $request->file('profile_picture')->store('uploads', 'public');

                // Remove 'public/' prefix for storing in the database
                $validatedData['profile_picture'] = str_replace('public/', '', $filePath);
            }

            $user->update($validatedData);

            // Generate the full URL for the file path
            $fileUrl = $filePath ? asset('storage/' . $validatedData['profile_picture']) : null;

            // Return all user fields along with the file path
            return response()->json([
                'message' => 'Profile updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'date_of_birth' => $user->date_of_birth,
                    'profile_picture' => $fileUrl, // URL for the profile picture
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the profile.'], 500);
        }
    }
}
