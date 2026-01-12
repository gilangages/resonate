<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NoteController extends Controller
{
    use AuthorizesRequests;
    /**
     * 🌍 GET /api/notes
     * Public: list semua note
     */
    public function index()
    {
        $notes = Note::with('user:id,name') // Ambil id & name dari relasi user
            ->latest()
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'content' => $note->content,
                    'recipient' => $note->recipient,
                    'spotify_track_id' => $note->spotify_track_id,
                    'spotify_track_name' => $note->spotify_track_name,
                    'spotify_artist' => $note->spotify_artist,
                    'spotify_album_image' => $note->spotify_album_image,
                    'spotify_preview_url' => $note->spotify_preview_url,

                    // Logic Penulis & Avatar
                    'author' => $note->is_anonymous ? 'Anonymous' : $note->user->name,

                    // Panggil 'photo_url' yang tadi kita buat di User.php
                    // Jika anonim, pakai gambar default (misal: hantu/kosong)
                    'author_avatar' => $note->is_anonymous
                    ? 'https://api.dicebear.com/9.x/notionists/svg?seed=Anon&backgroundColor=333'
                    : $note->user->photo_url,
                    // Jika user asli, panggil accessor 'photo_url' dari User model

                    'created_at' => $note->created_at,
                ];
            });

        return response()->json($notes);
    }

    /**
     * 🌍 GET /api/notes/{id}
     * Public: detail note
     */
    public function show($id)
    {
        $note = Note::with('user:id,name')->findOrFail($id);

        return response()->json([
            'id' => $note->id,
            'content' => $note->content, // Ubah ini
            'recipient' => $note->recipient,
            'spotify_track_id' => $note->spotify_track_id, // Ubah ini

            // Tambahan data spotify
            'spotify_track_name' => $note->spotify_track_name,
            'spotify_artist' => $note->spotify_artist,
            'spotify_album_image' => $note->spotify_album_image,
            'spotify_preview_url' => $note->spotify_preview_url,

            'author' => $note->is_anonymous ? null : $note->user->name,
            'created_at' => $note->created_at,
            'updated_at' => $note->updated_at,
        ]);
    }

    /**
     * 🔐 POST /api/notes
     * Auth: buat note
     */
    public function store(StoreNoteRequest $request)
    {
        $note = $request->user()->notes()->create(
            $request->validated()
        );

        return response()->json($note, 201);
    }

    /**
     * 🔐 PUT /api/notes/{id}
     * Auth + owner only
     */
    public function update(UpdateNoteRequest $request, $id) // Ubah "Note $note" jadi "$id"
    {
        // 1. Cari manual note-nya berdasarkan ID
        $note = Note::findOrFail($id);

        // 2. Cek otorisasi (Policy)
        // Sekarang $note berisi data asli dr DB, jadi user_id-nya ada isinya.
        $this->authorize('update', $note);

        // 3. Update data
        $note->update($request->validated());

        return response()->json($note);
    }

    /**
     * 🔐 DELETE /api/notes/{id}
     * Auth + owner only
     */
    public function destroy($id)
    {
        $note = Note::findOrFail($id);

        $this->authorize('delete', $note);

        $note->delete();

        return response()->json([
            'message' => 'Note deleted successfully',
        ]);
    }

}
