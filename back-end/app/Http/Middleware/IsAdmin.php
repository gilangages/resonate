<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Definisikan variabel $user DI SINI agar bisa dipakai di bawah
        $user = $request->user();

        // 2. Cek apakah user ada DAN role-nya admin (pakai trim untuk keamanan)
        if ($user && trim($user->role ?? '') === 'admin') {
            return $next($request);
        }

        // 3. Siapkan pesan debug
        // Menggunakan operator null coalescing (??) untuk menangani jika $user null
        $detectedRole = $user ? ($user->role ?? 'Empty Role') : 'Guest (Not Logged In)';

        // 4. Return response JSON 403 dengan info debug
        return response()->json([
            'message' => 'Hanya admin yang boleh masuk. Role Anda terdeteksi sebagai: [' . $detectedRole . ']',
        ], 403);
    }
}
