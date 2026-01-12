<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika user login dan statusnya banned
        if (auth()->check() && auth()->user()->is_banned) {

            // Ambil alasan dari database
            $reason = auth()->user()->ban_reason ?? 'Akun Anda dinonaktifkan.';

            // Hapus token user agar logout
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'AKUN_DIBEKUKAN', // Biarkan ini jika frontend lama pakai ini
                'status' => 'banned', // PENTING: Ini yang dicari oleh Test & Frontend baru
                'reason' => $reason, // Kirim alasan agar bisa muncul di popup
            ], 403);
        }

        return $next($request);
    }
}
