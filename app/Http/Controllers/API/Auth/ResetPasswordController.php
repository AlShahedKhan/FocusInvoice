<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $otpValidation = $this->otp->validate($request->email, $request->otp);

        if (! $otpValidation->status) {
            return response()->json(['error' => $otpValidation], 401);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $user->tokens()->delete();
        return response()->json(['success' => true], 200);
    }
}

