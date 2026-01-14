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

            // Proses Avatar dari Google (High Res)
            $googleAvatar = $googleUser->getAvatar();
            if ($googleAvatar) {
                $googleAvatar = preg_replace('/=s\d+(-c)?$/', '=s1024', $googleAvatar);
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
                    'avatar' => $googleAvatar, // User baru pasti pakai foto Google
                    'password' => null,
                ]);
            } else {
                // UPDATE USER LAMA (Sinkronisasi Data)

                $updateData = [
                    'google_id' => $googleUser->getId(),
                ];

                // LOGIC PENTING:
                // Cek apakah avatar user saat ini adalah URL (dari Google/eksternal) atau File Lokal?
                // Jika avatar saat ini adalah URL valid (atau null), kita update dengan avatar Google yang baru.
                // Jika avatar saat ini TIDAK valid URL (berarti path file lokal 'avatars/...'), JANGAN ditimpa.

                $currentAvatarIsUrl = filter_var($user->avatar, FILTER_VALIDATE_URL);
                $currentAvatarIsNull = is_null($user->avatar);

                if ($currentAvatarIsUrl || $currentAvatarIsNull) {
                    $updateData['avatar'] = $googleAvatar;
                }
                // Else: User punya foto lokal, jangan masukkan 'avatar' ke $updateData

                $user->update($updateData);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect("{$frontendUrl}/auth/callback?token={$token}&name={$user->name}");

        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return redirect(env('FRONTEND_URL') . '/login?error=Gagal login dengan Google');
        }
    }
}
