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
    private function applyFilters($query, Request $request)
    {
        // 1. Fitur Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%")
                    ->orWhere('music_track_name', 'like', "%{$search}%")
                    ->orWhere('music_artist_name', 'like', "%{$search}%");
            });
        }

        // 2. Fitur Filter Sort (Newest / Oldest)
        if ($request->has('sort') && $request->sort == 'oldest') {
            $query->oldest();
        } else {
            $query->latest(); // Default Newest
        }

        return $query;
    }
    public function index(Request $request)
    {
        $query = Note::with('user');

        // Terapkan filter
        $query = $this->applyFilters($query, $request);

        return NoteResource::collection($query->paginate(15));
    }

    /**
     * GET /api/notes/global
     * Public: Ambil 6 note acak untuk Landing Page
     */
    public function globalIndex()
    {
        // Ambil 6 data secara acak (Random)
        $notes = Note::with('user')
            ->inRandomOrder()
            ->take(6)
            ->get();

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
        // Query hanya note milik user yang login
        $query = $request->user()->notes()->with('user');

        // Terapkan filter
        $query = $this->applyFilters($query, $request);

        return NoteResource::collection($query->paginate(15));
    }
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notes,id',
        ]);

        // Hapus note yang ID-nya ada di list DAN milik user yang login (Security)
        $deletedCount = $request->user()->notes()
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json([
            'message' => "$deletedCount notes deleted successfully",
        ]);
    }

}
