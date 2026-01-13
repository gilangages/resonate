<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserAppealNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AppealController extends Controller
{
    /**
     * User yang dibanned mengirim permintaan buka blokir
     */
    public function sendAppeal(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'message' => 'required|string|min:10|max:500', // Wajib ada pesan
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah user benar-benar dibanned
        if (!$user->is_banned) {
            return response()->json(['message' => 'Akun ini tidak sedang dibekukan.'], 400);
        }

        // 2. Ambil semua Admin
        $admins = User::where('role', 'admin')->get();

        // 3. Kirim Notifikasi ke Admin (Sertakan Pesan)
        Notification::send($admins, new UserAppealNotification($user->email, $request->message));

        return response()->json([
            'message' => 'Permintaan banding telah dikirim ke Admin.',
        ]);
    }
}
