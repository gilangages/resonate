<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $request->email)->first();

        // 1. Jika user tidak ditemukan sama sekali
        if (!$user) {
            return response()->json(['message' => 'Email tidak terdaftar.'], 401);
        }

        // 2. Jika user ditemukan, tapi password di database NULL (User Google belum buat password)
        if (is_null($user->password) && $user->google_id) {
            return response()->json([
                'message' => 'Akun ini terdaftar melalui Google. Silakan login menggunakan tombol Google atau gunakan fitur Lupa Password untuk membuat password manual.',
            ], 422);
        }

        // 3. Jika user ada, tapi password salah
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password salah.'], 401);
        }

        // 4. CEK STATUS BANNED (Implementation Baru)
        if ($user->is_banned) {
            return response()->json([
                'message' => 'AKUN_DIBEKUKAN', // Kode khusus untuk frontend
                'status' => 'banned',

                'reason' => $user->ban_reason ?? 'Pelanggaran Aturan.',
            ], 403);
        }

        // Jika lolos semua pengecekan di atas, lanjut buat token...
        // $user->tokens()->delete(); biar bisa login di HP dan Laptop secara bersamaan
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user), // <--- Lebih simpel, photo_url otomatis masuk
        ]);
    }
}
