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

            // Hapus token user agar logout
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Akun Anda telah dibanned.',
                'status' => 'banned',
            ], 403); // 403 Forbidden
        }

        return $next($request);
    }
}
