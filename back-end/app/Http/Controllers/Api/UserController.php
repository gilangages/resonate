<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Support\Facades\Storage; // Tidak dipakai lagi untuk avatar

class UserController extends Controller
{
    /**
     * Get current logged in user.
     */
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update current logged in user.
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // 1. Simpan tema jika ada
        if ($request->has('card_theme')) {
            $user->card_theme = $validated['card_theme'];
        }

        // 2. Update Nama
        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        // 3. Update Password
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // 4. LOGIKA BARU: Update Avatar ke Cloudinary ☁️
        if ($request->hasFile('avatar')) {
            try {
                // 1. Konfigurasi Manual (BYPASS FILE CONFIG)
                // Kita ambil langsung dari env, jadi tidak peduli config cache error/nggak.
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => [
                        'secure' => true,
                    ],
                ]);

                // 2. Upload Pakai SDK Asli
                $file = $request->file('avatar')->getRealPath();
                $upload = (new UploadApi())->upload($file, [
                    'folder' => 'avatars', // Nama folder di cloudinary
                    'transformation' => [ // Opsional: Langsung crop jadi kotak
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'fill',
                    ],
                ]);

                // 3. Ambil URL Hasil Upload
                $secureUrl = $upload['secure_url'];

                // 4. Update Database
                $user->avatar = $secureUrl;

            } catch (\Exception $e) {
                // Tampilkan error asli jika masih gagal
                return response()->json([
                    'message' => 'Upload failed via Native SDK',
                    'debug_error' => $e->getMessage(),
                    'line' => $e->getLine(),
                ], 500);
            }
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Remove the current user account permanently.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // 🛡️ PROTEKSI: Admin tidak boleh hapus diri sendiri
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Admin account cannot be deleted for safety reasons.',
            ], 403);
        }

        DB::transaction(function () use ($user) {
            // Note: Kita tidak menghapus gambar di Cloudinary saat akun dihapus
            // untuk menjaga performa delete agar cepat.
            // Gambar di Cloudinary akan tetap ada (bisa dibersihkan manual nanti).

            // 1. Hapus Semua Token (Force Logout)
            $user->tokens()->delete();

            // 2. Hapus User
            $user->delete();
        });

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}
