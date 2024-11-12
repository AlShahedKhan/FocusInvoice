<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        // Log the request type to confirm the content type is correct
        Log::info('Request Type:', [$request->header('Content-Type')]);

        Log::info(  $request->all());

        // Log each field individually to see what data (if any) is being received
        Log::info('First Name:', [$request->input('first_name')]);
        Log::info('Last Name:', [$request->input('last_name')]);
        Log::info('Email:', [$request->input('email')]);
        Log::info('Phone Number:', [$request->input('phone_number')]);
        Log::info('Date of Birth:', [$request->input('date_of_birth')]);
        Log::info('Profile Picture:', [$request->file('profile_picture')]);

        // Retrieve the authenticated user
        $user = $request->user();

        // Validate input
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // optional image validation
        ]);

        // Update user details
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->date_of_birth = $request->input('date_of_birth');

        // Handle profile picture upload if present
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }

        // Save changes
        $user->save();

        return response()->json(['message' => 'Profile updated successfully.'], 200);
    }
}
