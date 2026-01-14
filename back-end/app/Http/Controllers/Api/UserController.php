<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

        // Simpan tema jika ada di request
        if ($request->has('card_theme')) {
            $user->card_theme = $validated['card_theme'];
        }

        // 1. Update Nama
        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        // 2. Update Password (logika lama..)
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // 3. LOGIKA BARU: Update Avatar
        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada (optional, biar server gak penuh)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru ke folder 'avatars' di storage public
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }
        // Cek apakah user mengisi kolom password
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            // Jangan update password jika inputnya kosong
            unset($validated['password']);
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
            ], 403); // 403 Forbidden
        }

        // Gunakan Transaction agar atomik (semua sukses atau semua gagal)
        DB::transaction(function () use ($user) {
            // 1. Hapus Avatar dari Storage (Clean Code: Hindari sampah file)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // 2. Hapus Semua Token (Force Logout dari semua device)
            $user->tokens()->delete();

            // 3. Hapus User
            // Note: Karena di migration 'notes' sudah 'cascadeOnDelete',
            // maka notes akan otomatis terhapus oleh database.
            $user->delete();
        });

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}
