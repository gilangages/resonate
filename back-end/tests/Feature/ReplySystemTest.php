<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReplySystemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_reply_to_a_note_with_another_note()
    {
        $user = User::factory()->create();
        $parentNote = Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Lagu galau ni',
            'music_track_name' => 'Glimpse of Us',
        ]);

        $replier = User::factory()->create();

        $response = $this->actingAs($replier)->postJson('/api/notes', [
            'parent_id' => $parentNote->id,
            'content' => 'Jangan sedih bang',
            'recipient' => 'Abang Galau',
            'music_track_id' => '12345',
            'music_track_name' => 'Manusia Kuat',
            'music_artist_name' => 'Tulus',
            'music_album_image' => 'https://example.com/image.jpg',
            'music_preview_url' => 'https://example.com/audio.mp3',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('notes', [
            'parent_id' => $parentNote->id,
            'content' => 'Jangan sedih bang',
            'music_track_name' => 'Manusia Kuat',
            'user_id' => $replier->id,
        ]);
    }

    #[Test]
    public function main_feed_does_not_show_replies()
    {
        $user = User::factory()->create();

        // Buat Parent Note
        $parentNote = Note::factory()->create(['user_id' => $user->id]);

        // Buat Reply Note
        $replyNote = Note::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $parentNote->id,
        ]);

        // PERBAIKAN: Tambahkan actingAs($user) karena route /api/notes butuh login
        $response = $this->actingAs($user)->getJson('/api/notes');

        $response->assertStatus(200);

        // Harusnya cuma ada 1 data (Parent), Reply tidak muncul di list utama
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $parentNote->id]);
        $response->assertJsonMissing(['id' => $replyNote->id]);
    }

    #[Test]
    public function show_note_detail_includes_replies()
    {
        $user = User::factory()->create();
        $parentNote = Note::factory()->create(['user_id' => $user->id]);
        $replyNote = Note::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $parentNote->id,
            'music_track_name' => 'Reply Song',
        ]);

        // Route show note sifatnya public, jadi tidak perlu actingAs
        $response = $this->getJson("/api/notes/{$parentNote->id}");

        $response->assertStatus(200);

        // Pastikan data reply ada di dalam response detail
        $response->assertJsonStructure([
            'data' => [
                'id',
                'replies' => [
                    '*' => ['id', 'music_track_name', 'author_name'],
                ],
            ],
        ]);

        $response->assertJsonFragment(['music_track_name' => 'Reply Song']);
    }
}
