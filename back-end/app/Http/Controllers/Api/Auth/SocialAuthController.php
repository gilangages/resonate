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
    // ... namespace dan use imports tetap sama ...

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $avatar = $googleUser->getAvatar();
            if ($avatar) {
                // Regex: Cari "=s" diikuti angka, opsional "-c", di akhir string
                $avatar = preg_replace('/=s\d+(-c)?$/', '=s1024', $avatar);
            }

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            // 1. CEK STATUS BANNED
            if ($user && $user->is_banned) {
                $message = "Akun Anda telah dibekukan oleh Admin.";
                return redirect(env('FRONTEND_URL') . "/login?status=banned&email={$user->email}&message=" . urlencode($message));
            }

            if (!$user) {
                // CREATE USER BARU
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $avatar, // Ambil foto dari Google
                    'password' => null,
                ]);
            } else {
                // UPDATE USER LAMA (Sinkronisasi Data)
                // Kita update avatar setiap kali login agar selalu fresh dari Google
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $avatar,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect("{$frontendUrl}/auth/callback?token={$token}&name={$user->name}");

        } catch (\Exception $e) {
            // Log error jika perlu: \Log::error($e->getMessage());
            return redirect(env('FRONTEND_URL') . '/login?error=Gagal login dengan Google');
        }
    }
}
