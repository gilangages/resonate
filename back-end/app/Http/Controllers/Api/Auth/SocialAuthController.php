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

            // 1. CEK STATUS BANNED SEBELUM LOGIN/REGISTER
            // 1. Cek apakah user ada DAN statusnya banned
            if ($user && $user->is_banned) {
                // Jangan buat token! Lempar balik ke login page dengan pesan error
                return redirect(env('FRONTEND_URL') . '/login?error=Akun Anda telah dibekukan oleh Admin.');
            }
            // ---------------------------

            // Jika user belum ada (dan jelas belum banned karena baru), buat baru
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null,
                    // 'is_banned' => false, // Default false
                ]);
            } else {
                // Update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                    ]);
                }
            }

            // Buat Token
            $token = $user->createToken('auth_token')->plainTextToken;

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect("{$frontendUrl}/auth/callback?token={$token}&name={$user->name}");

        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL') . '/login?error=Gagal login dengan Google');
        }
    }
}
