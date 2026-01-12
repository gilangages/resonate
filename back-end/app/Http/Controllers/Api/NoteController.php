<?php

namespace App\Http\Controllers\Api; // <--- Namespace yang BENAR (tanpa Auth)

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note; // <--- WAJIB: Import Request
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <--- Import Resource
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use AuthorizesRequests;

    /**
     * GET /api/notes
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
     * GET /api/notes/{id}
     * Public: detail note
     */
    public function show($id)
    {
        $note = Note::with('user')->findOrFail($id);

        return new NoteResource($note);
    }

    /**
     * POST /api/notes
     * Auth: buat note
     */
    public function store(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'content' => 'required|string',
            'recipient' => 'nullable|string',
            'initial_name' => 'nullable|string',
            'spotify_track_id' => 'nullable|string',
        ]);

        // Create Note
        $note = $request->user()->notes()->create($validated);

        // WRAPPING: Gunakan new NoteResource() karena datanya cuma satu
        return new NoteResource($note);
    }

    /**
     * PUT /api/notes/{id}
     * Auth + owner only
     */
    public function update(Request $request, $id)
    {
        // 1. Cari manual note-nya berdasarkan ID
        $note = Note::findOrFail($id);

        // 2. Cek otorisasi (Policy)
        $this->authorize('update', $note);

        // Validasi input update
        $validated = $request->validate([
            'content' => 'sometimes|string',
            'recipient' => 'nullable|string',
            'initial_name' => 'nullable|string',
            'spotify_track_id' => 'nullable|string',
        ]);

        // 3. Update data
        $note->update($validated);

        return new NoteResource($note);
    }

    /**
     * DELETE /api/notes/{id}
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
