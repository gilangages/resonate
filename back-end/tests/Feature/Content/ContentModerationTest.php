<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModerationTest extends TestCase
{
    use RefreshDatabase;

    // Data dummy lagu agar kita tidak perlu ketik ulang
    private function validNoteData($overrides = [])
    {
        return array_merge([
            'recipient' => 'Seseorang',
            'theme' => 'blue',
            'is_anonymous' => true,
            // Data lagu wajib (dummy)
            'music_track_id' => '12345',
            'music_track_name' => 'Lagu Testing',
            'music_artist_name' => 'Artis Testing',
            'music_album_image' => 'https://via.placeholder.com/150',
            'music_preview_url' => 'https://example.com/preview.mp3',
        ], $overrides);
    }

    public function test_user_cannot_post_bad_words()
    {
        $user = User::factory()->create();

        // Kita kirim data lengkap, tapi content-nya kotor
        $response = $this->actingAs($user)->postJson('/api/notes', $this->validNoteData([
            'content' => 'Dasar kamu bodoh banget!',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    public function test_user_can_post_clean_words()
    {
        $user = User::factory()->create();

        // Kita kirim data lengkap dengan content bersih
        $response = $this->actingAs($user)->postJson('/api/notes', $this->validNoteData([
            'content' => 'Kamu sangat baik hati dan ramah.',
        ]));

        // Sekarang harusnya sukses 201 Created
        $response->assertStatus(201);
    }
}
