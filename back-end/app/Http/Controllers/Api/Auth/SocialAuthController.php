<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // 1. Mengarahkan user ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // 2. Menerima balasan dari Google
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cari user berdasarkan google_id ATAU email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            // Jika user belum ada, buat baru
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null, // Tidak butuh password
                ]);
            } else {
                // Jika user ada tapi belum punya google_id (dulu daftar manual), update datanya
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?: $googleUser->getAvatar(), // Pakai foto google jika belum ada
                    ]);
                }
            }

            // Buat Token Sanctum (Login)
            $token = $user->createToken('auth_token')->plainTextToken;

            // KUNCI PENTING UNTUK SPA:
            // Jangan return JSON, tapi REDIRECT ke Frontend membawa token di URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect("{$frontendUrl}/auth/callback?token={$token}&name={$user->name}");

        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL') . '/login?error=Gagal login dengan Google');
        }
    }
}
