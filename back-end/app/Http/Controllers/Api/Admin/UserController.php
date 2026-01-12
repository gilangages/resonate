<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * 1. Lihat semua user
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'avatar', 'role', 'created_at')
            ->latest()
            ->get();

        return response()->json(['data' => $users]);
    }

    /**
     * 2. Hapus user yang nakal
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Proteksi ID 1 (Biasanya Super Admin pertama)
        if ($user->id === 1) {
            return response()->json(['message' => 'Akun Super Admin utama tidak boleh dihapus!'], 403);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Tidak bisa menghapus akun admin.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    /**
     * 3. Lihat semua Note dari semua user (BARU)
     * Admin butuh ini untuk melihat mana yang perlu dihapus
     */
    public function indexNotes(): JsonResponse
    {
        // Kita gunakan 'with' agar tahu siapa pemilik note tersebut (Eager Loading)
        $notes = Note::with('user:id,name,email')
            ->latest()
            ->get();

        return response()->json(['data' => $notes]);
    }

    /**
     * 4. Hapus Note yang melanggar aturan
     */
    public function destroyNote($id): JsonResponse
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return response()->json(['message' => 'Catatan berhasil dihapus oleh admin.']);
    }
}
