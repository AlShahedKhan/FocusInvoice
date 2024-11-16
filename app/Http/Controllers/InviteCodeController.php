<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InviteCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteCodeMail;

class InviteCodeController extends Controller
{
    /**
     * Generate an invite code for a new user (Admin only).
     */
    public function generateInviteCode(Request $request)
    {
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Validate email input
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // Generate and store the invite code
        $inviteCode = InviteCode::create([
            'code' => Str::random(10), // Random 10-character invite code
            'created_by' => $user->id,
        ]);

        // Send the invite code via email
        Mail::to($request->email)->send(new InviteCodeMail($inviteCode->code));

        return response()->json(['invite_code' => $inviteCode->code, 'message' => 'Invite code sent successfully!'], 200);
    }
}
