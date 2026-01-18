<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // 1. Ambil Foto Resolusi Tinggi dari Google
            $googleAvatar = $googleUser->getAvatar();
            if ($googleAvatar) {
                $googleAvatar = preg_replace('/=s\d+(-c)?$/', '=s1024', $googleAvatar);
            }

            // Cari User
            $user = User::where('email', $googleUser->getEmail())->first();

            // 2. Cek Status Banned (Fitur Lama Tetap Ada)
            if ($user && $user->is_banned) {
                $message = "Akun Anda telah dibekukan oleh Admin.";
                return redirect(env('FRONTEND_URL') . "/login?status=banned&email={$user->email}&message=" . urlencode($message));
            }

            if ($user) {
                // --- LOGIKA PINTAR (SMART SYNC) ---

                $updateData = [
                    'google_id' => $googleUser->getId(),
                ];

                // Cek: Apakah foto profil user saat ini berasal dari Cloudinary?
                // (Kita asumsikan URL dari Cloudinary pasti mengandung teks 'cloudinary')
                $isCustomPhoto = $user->avatar && str_contains($user->avatar, 'cloudinary');

                // JIKA BUKAN foto Cloudinary (berarti masih foto Google lama atau kosong),
                // MAKA: Update dengan foto Google terbaru biar sinkron.
                if (!$isCustomPhoto) {
                    $updateData['avatar'] = $googleAvatar;
                }

                // Kalau $isCustomPhoto = true, kita DIAMKAN saja kolom 'avatar'.
                // Foto Cloudinary kamu aman, tidak akan tertimpa.

                $user->update($updateData);

            } else {
                // --- REGISTER (USER BARU) ---
                // User baru WAJIB pakai foto Google sebagai modal awal

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleAvatar,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]);
            }

            // Buat Token & Redirect
            $token = $user->createToken('auth_token')->plainTextToken;
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect("{$frontendUrl}/auth/callback?token={$token}&name=" . urlencode($user->name));

        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL') . '/login?error=Gagal login dengan Google');
        }
    }
}
