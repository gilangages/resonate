<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\NoteReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplySystemRefactoredTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_reply_to_a_note_using_new_table()
    {
        // 1. Setup User & Note Induk
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $otherUser->id,
            'content' => 'Ini note induk',
        ]);

        // 2. Lakukan Request POST ke endpoint baru
        $response = $this->actingAs($user)->postJson("/api/notes/{$note->id}/reply", [
            'content' => 'Ini balasan refactor',
            'initial_name' => 'Si Pengetes',
            'music_track_id' => '12345',
            'music_track_name' => 'Lagu Test',
            'music_artist_name' => 'Artis Test',
            'music_album_image' => 'https://example.com/image.jpg',
        ]);

        // 3. Assert Response OK
        $response->assertStatus(201);

        // 4. Assert Database (Pastikan masuk ke tabel note_replies, BUKAN notes)
        $this->assertDatabaseHas('note_replies', [
            'note_id' => $note->id,
            'user_id' => $user->id,
            'content' => 'Ini balasan refactor',
            'music_track_name' => 'Lagu Test',
        ]);

        // 5. Pastikan TIDAK masuk ke tabel notes (sebagai note baru dgn parent_id)
        // Logika lama akan membuat parent_id terisi, logika baru tabel notes bersih dari reply
        $this->assertDatabaseMissing('notes', [
            'content' => 'Ini balasan refactor',
        ]);
    }

    /**
     * @test
     */
    public function replies_are_included_in_note_detail()
    {
        $note = Note::factory()->create();
        $user = User::factory()->create();

        // Buat manual reply di tabel baru
        NoteReply::create([
            'note_id' => $note->id,
            'user_id' => $user->id,
            'content' => 'Reply Manual',
            'music_track_id' => '111',
            'music_track_name' => 'Test',
            'music_artist_name' => 'Test',
        ]);

        // Hit endpoint detail note
        $response = $this->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200);

        // Pastikan JSON structure tetap memiliki key 'replies'
        // dan datanya berasal dari tabel baru
        $response->assertJsonPath('data.replies.0.content', 'Reply Manual');
    }

    /**
     * @test
     */
    public function user_can_delete_their_own_reply()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create();

        $reply = NoteReply::create([
            'note_id' => $note->id,
            'user_id' => $user->id,
            'content' => 'Akan dihapus',
            'music_track_id' => '111',
            'music_track_name' => 'Test',
            'music_artist_name' => 'Test',
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/replies/{$reply->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('note_replies', ['id' => $reply->id]);
    }
}
