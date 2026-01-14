<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedNoteAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario 1: Guest (orang asing tanpa akun) membuka link.
     * HARUS: Sukses (200) dan menampilkan data.
     */
    public function test_guest_can_view_shared_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Halo dunia',
            'music_track_name' => 'Lagu Keren',
        ]);

        // Akses endpoint public
        $response = $this->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $note->id)
            ->assertJsonPath('data.content', 'Halo dunia')
            ->assertJsonPath('data.music_track_name', 'Lagu Keren');
    }

    /**
     * Skenario 2: User yang sudah login membuka link.
     * HARUS: Sukses (200).
     */
    public function test_authenticated_user_can_view_shared_note()
    {
        $note = Note::factory()->create();
        $viewer = User::factory()->create();

        // Login dan akses
        $response = $this->actingAs($viewer, 'sanctum')
            ->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $note->id);
    }

    /**
     * Skenario 3: Mencoba membuka link ID yang tidak ada.
     * HARUS: 404 Not Found.
     */
    public function test_accessing_non_existent_note_returns_404()
    {
        $response = $this->getJson("/api/notes/999999");
        $response->assertStatus(404);
    }

    /**
     * Skenario 4 (KEAMANAN): Guest mencoba MENGHAPUS note lewat API.
     * HARUS: Ditolak (401 Unauthorized).
     */
    public function test_guest_cannot_delete_note()
    {
        $note = Note::factory()->create();

        // Coba method DELETE tanpa token
        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('notes', ['id' => $note->id]); // Data harus tetap ada
    }

    /**
     * Skenario 5 (KEAMANAN): Guest mencoba MENGEDIT note.
     * HARUS: Ditolak (401 Unauthorized).
     */
    public function test_guest_cannot_update_note()
    {
        $note = Note::factory()->create(['content' => 'Asli']);

        $response = $this->putJson("/api/notes/{$note->id}", [
            'content' => 'Hacked',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseHas('notes', ['content' => 'Asli']);
    }
}
