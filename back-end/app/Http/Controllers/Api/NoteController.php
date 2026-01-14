<?php

namespace App\Http\Controllers\Api; // <--- Namespace yang BENAR (tanpa Auth)

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
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
        $notes = Note::with('user')->latest()->paginate(15);

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
     * Ganti Request biasa menjadi StoreNoteRequest
     */
    public function store(StoreNoteRequest $request)
    {
        // Tidak perlu $request->validate manual lagi karena sudah dihandle StoreNoteRequest
        // Langsung ambil data yang sudah divalidasi
        $validated = $request->validated();

        // Create Note
        $note = $request->user()->notes()->create($validated);

        return new NoteResource($note);
    }

    /**
     * PUT /api/notes/{id}
     * Auth + owner only
     */
    public function update(UpdateNoteRequest $request, $id)
    {
        $note = Note::findOrFail($id);

        // Cek otorisasi
        $this->authorize('update', $note);

        // 3. GUNAKAN 'validated()' DARI REQUEST, BUKAN VALIDASI MANUAL
        // Ini akan mengambil rules lengkap dari file UpdateNoteRequest.php yang sudah kamu buat
        $validated = $request->validated();

        // Update data ke database
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

    /**
     * GET /api/notes/my
     * Auth: Mengambil daftar note milik user yang sedang login
     */
    public function myNotes(Request $request)
    {
        // Ambil user dari token, lalu ambil notes-nya
        $notes = $request->user()->notes()->with('user')->latest()->paginate(15);

        return NoteResource::collection($notes);
    }
}
