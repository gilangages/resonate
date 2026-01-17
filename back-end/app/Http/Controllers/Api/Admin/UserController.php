<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    // 1. Lihat semua user
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')->latest()->get();
        return response()->json(['data' => $users]);
    }

    // 2. Hapus user yang nakal
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Opsional: Cegah menghapus sesama admin
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Tidak bisa menghapus sesama admin.'], 403);
        }

        $user->delete(); // Ini akan otomatis menghapus Note mereka juga (jika cascade di set di DB)

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
