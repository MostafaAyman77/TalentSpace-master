<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;

class PasswordResetController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate OTP (6 digits)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(30);

        // Store OTP
        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'created_at' => now()]
        );

        // Send OTP to email
        Mail::to($request->email)->send(new SendOtp($otp));

        return response()->json([
            'message' => 'OTP sent to your email',
            'expires_at' => $expiresAt
        ]);
    }

    // Verify OTP and reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // Check if OTP is expired (30 minutes)
        if (now()->diffInMinutes($otpRecord->created_at) > 30) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete OTP record
        $otpRecord->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
