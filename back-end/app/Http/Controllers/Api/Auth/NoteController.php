<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
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
        // Ambil notes terbaru beserta data user-nya
        $notes = Note::with('user')->latest()->get();

        // WRAPPING: Gunakan collection() karena datanya banyak (list)
        return NoteResource::collection($notes);
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
    public function store(Request $request)
    {
        // ... validasi dan save kode lama kamu ...

        $note = $request->user()->notes()->create($validated);

        // WRAPPING: Gunakan new NoteResource() karena datanya cuma satu
        return new NoteResource($note);
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
