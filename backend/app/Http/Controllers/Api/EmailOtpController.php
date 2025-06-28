<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class EmailOtpController extends Controller
{

    public function sendOtp(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $otp = rand(100000, 999999);

        try {
            $otpRecord = EmailOtp::updateOrCreate(
                ['email' => $email],
                [
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(5)
                ]
            );


            Mail::to($email)->send(new SendOtpMail($otp));

            return response()->json(['message' => 'OTP telah dikirim ke email'], 200);
        } catch (\Exception $e) {

            Log::error('Error sending OTP: ' . $e->getMessage());

            return response()->json(['message' => 'Gagal mengirim OTP'], 500);
        }
    }



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');

        $otpRecord = EmailOtp::where('email', $email)->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP belum dikirim atau tidak ditemukan'], 404);
        }

        if ($otpRecord->otp != $otp) {
            return response()->json(['message' => 'OTP salah'], 400);
        }

        if ($otpRecord->expires_at < now()) {
            return response()->json(['message' => 'OTP sudah kadaluarsa'], 400);
        }


        $otpRecord->delete();

        return response()->json(['message' => 'OTP berhasil diverifikasi'], 200);
    }
}
