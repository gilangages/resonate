<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // IMPORT PENTING 1
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
                // Upload ke Cloudinary (Folder: resonate/avatars)
                $uploadedFile = Cloudinary::upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'resonate/avatars',
                ]);

                // Ambil URL Aman (HTTPS) dari hasil upload
                $url = $uploadedFile->getSecurePath();

                // Simpan URL tersebut ke database user
                $user->avatar = $url;

            } catch (\Exception $e) {
                // Opsional: Jika upload gagal, kembalikan error (atau biarkan null)
                return response()->json([
                    'message' => 'Upload failed',
                    'debug_error' => $e->getMessage(), // <--- INI KUNCINYA
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
