<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\NoteReply;
use Illuminate\Http\Request;

class NoteReplyController extends Controller
{
    /**
     * Store a newly created reply in storage.
     */
    public function store(Request $request, $noteId)
    {
        $request->validate([
            'music_track_id' => 'required',
            'music_track_name' => 'required',
            'music_artist_name' => 'required',
            'content' => 'nullable|string|max:500',
            'initial_name' => 'nullable|string|max:50',
        ]);

        $note = Note::findOrFail($noteId);

        // Buat reply baru di tabel note_replies
        $reply = NoteReply::create([
            'note_id' => $note->id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'initial_name' => $request->initial_name,
            'music_track_id' => $request->music_track_id,
            'music_track_name' => $request->music_track_name,
            'music_artist_name' => $request->music_artist_name,
            'music_album_image' => $request->music_album_image,
            'music_preview_url' => $request->music_preview_url,
            'music_track_link' => $request->music_track_link ?? null,
        ]);

        // Load user agar frontend bisa langsung menampilkan avatar/nama tanpa refresh
        $reply->load('user');

        return response()->json([
            'message' => 'Reply sent successfully',
            'data' => $reply,
        ], 201);
    }

    /**
     * Remove the specified reply from storage.
     */
    public function destroy(Request $request, $id)
    {
        $reply = NoteReply::findOrFail($id);

        // Pastikan hanya pemilik reply atau admin yang bisa hapus
        if ($reply->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->delete();

        return response()->json(['message' => 'Reply deleted successfully']);
    }
}
