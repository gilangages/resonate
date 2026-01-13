<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_notes_endpoint_returns_avatar_and_fallback_photo_url()
    {
        // 1. Buat User tanpa avatar manual (avatar = null)
        $user = User::factory()->create([
            'avatar' => null,
            'name' => 'Test User',
        ]);

        // 2. Buat Note
        Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Tes pesan landing page',
        ]);

        // 3. Hit Endpoint Global
        $response = $this->getJson('/api/notes/global');

        // 4. Assert / Pastikan response OK
        $response->assertStatus(200);

        // 5. Pastikan struktur JSON memiliki kedua field gambar
        $response->assertJsonStructure([
            'data' => [
                '*' => [ // Tanda * berarti array of objects
                    'id',
                    'content',
                    'author_name',
                    'author_avatar', // Harus ada key ini (meski null)
                    'author_photo_url', // Harus ada key ini (Dicebear)
                ],
            ],
        ]);

        // 6. Cek spesifik data pertama
        $data = $response->json('data.0');

        // Avatar harus null (karena kita tidak set)
        $this->assertNull($data['author_avatar']);

        // Photo URL harus ada isinya (dari Dicebear)
        $this->assertNotNull($data['author_photo_url']);
        $this->assertStringContainsString('dicebear', $data['author_photo_url']);
    }
}
