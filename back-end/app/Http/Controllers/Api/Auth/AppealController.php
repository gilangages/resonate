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
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reason' => 'required|string|min:10',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->is_banned) {
            return response()->json(['message' => 'Akun ini tidak sedang dibanned.'], 400);
        }

        // Cari semua admin
        $admins = User::where('role', 'admin')->get();

        // Kirim notifikasi ke semua admin
        Notification::send($admins, new UserAppealNotification($user->email, $request->reason));

        return response()->json(['message' => 'Permintaan banding telah dikirim ke Admin.']);
    }
}
