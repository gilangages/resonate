<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use AuthorizesRequests;
    /**
     * 🌍 GET /api/notes
     * Public: list semua note
     */
    public function index()
    {
        $notes = Note::with('user:id,name')
            ->latest()
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'message' => $note->message,
                    'music_track_id' => $note->music_track_id,
                    'author' => $note->is_anonymous ? null : $note->user->name,
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
            'message' => $note->message,
            'music_track_id' => $note->music_track_id,
            'author' => $note->is_anonymous ? null : $note->user->name,
            'created_at' => $note->created_at,
        ]);
    }

    /**
     * 🔐 POST /api/notes
     * Auth: buat note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'music_track_id' => 'nullable|string',
            'is_anonymous' => 'boolean',
        ]);

        $note = Note::create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'music_track_id' => $validated['music_track_id'] ?? null,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
        ]);

        return response()->json([
            'message' => 'Note created successfully',
            'note' => $note,
        ], 201);
    }

    /**
     * 🔐 PUT /api/notes/{id}
     * Auth + owner only
     */
    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);

        $this->authorize('update', $note);

        $note->update($request->only([
            'message',
            'music_track_id',
            'is_anonymous',
        ]));

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
