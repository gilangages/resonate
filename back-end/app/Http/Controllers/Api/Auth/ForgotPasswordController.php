<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Email tidak ditemukan.'], 404);
            }

            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $resetUrl = $frontendUrl . "/reset-password?token=" . $token . "&email=" . $request->email;

            // Gunakan Mail::to langsung
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));

            // PASTIKAN RESPONSE INI BERHASIL DIKIRIM
            return response()->json([
                'status' => 'success',
                'message' => 'Link reset password telah dikirim ke email Anda.',
            ], 200);

        } catch (\Exception $e) {
            // Log error asli agar kamu bisa baca di laravel.log jika ini gagal
            \Log::error('Gagal kirim email: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
