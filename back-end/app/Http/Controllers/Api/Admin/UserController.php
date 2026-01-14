<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\User;
use App\Notifications\NoteDeletedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 1. Lihat semua user
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::select('id', 'name', 'email', 'avatar', 'role', 'created_at', 'is_banned');

        // Fitur Pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        // Paginate 10 item per halaman
        $users = $query->latest()->paginate(10);

        return response()->json($users);
    }

    /**
     * 2. Lihat semua Note (Dengan Search & Paginate)
     */
    public function indexNotes(Request $request): JsonResponse
    {
        $query = Note::with('user:id,name,email');

        if ($search = $request->input('search')) {
            $query->where('content', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $notes = $query->latest()->paginate(10);

        return response()->json($notes);
    }

    /**
     * 3. Hapus user yang nakal
     */
    // File: app/Http/Controllers/Api/Admin/UserController.php

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === 1 || $user->role === 'admin') {
            return response()->json(['message' => 'Tidak bisa menghapus admin.'], 403);
        }

        // UPDATE: Jangan delete(), tapi set banned
        $user->update([
            'is_banned' => true,
            'ban_reason' => 'Akun Anda dinonaktifkan karena melanggar pedoman komunitas.',
        ]);

        // Opsional: Cabut token agar user yang sedang login langsung logout
        $user->tokens()->delete();

        return response()->json(['message' => 'User berhasil diblokir.']);
    }

    /**
     * 4. Hapus Note yang melanggar aturan
     */
    public function destroyNote(Request $request, $id): JsonResponse
    {
        $note = Note::with('user')->findOrFail($id);

        $reason = $request->input('reason', 'Melanggar Pedoman Komunitas');
        // Kirim Notifikasi ke Pemilik Note
        if ($note->user) {
            $note->user->notify(new NoteDeletedNotification($note->content, $reason));
        }
        $note->delete();

        return response()->json(['message' => 'Catatan dihapus dan user telah diberitahu.']);
    }

    /**
     * 5. Memulihkan Akun (Unban)
     */
    public function restore($id): JsonResponse
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_banned' => false,
            'ban_reason' => null,
        ]);

        return response()->json(['message' => 'Akun user berhasil dipulihkan.']);
    }
}
