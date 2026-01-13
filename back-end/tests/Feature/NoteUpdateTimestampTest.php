<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteUpdateTimestampTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_a_note_updates_the_updated_at_timestamp()
    {
        // 1. Buat User & Note
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Konten awal',
        ]);

        // Simpan waktu pembuatan (created_at)
        $createdAt = $note->created_at;

        // 2. Beri jeda waktu 1 detik agar timestamp pasti berbeda
        // (Karena database cepat, update instan bisa jadi detiknya sama)
        sleep(1);

        // 3. Lakukan Update via API
        // 3. Lakukan Update via API
        $response = $this->actingAs($user)
            ->putJson("/api/notes/{$note->id}", [
                'content' => 'Konten setelah diedit',
                'recipient' => $note->recipient,
                'initial_name' => $note->initial_name, // Tambahkan ini jika perlu

                // BAGIAN PENTING YANG KURANG:
                'music_track_id' => $note->music_track_id, //

                // Data lagu lainnya yang sudah ada
                'music_track_name' => $note->music_track_name,
                'music_artist_name' => $note->music_artist_name,
                'music_album_image' => $note->music_album_image,
                'music_preview_url' => $note->music_preview_url, // Tambahkan biar lengkap
                'music_track_link' => $note->music_track_link, // Tambahkan biar lengkap
            ]);
        $response->assertStatus(200);

        // 4. Ambil data terbaru dari database
        $updatedNote = $note->fresh();

        // 5. Assertion (Pengecekan)
        // Pastikan updated_at sekarang lebih besar (setelah) created_at
        $this->assertTrue(
            $updatedNote->updated_at->gt($createdAt),
            "Timestamp updated_at harus lebih baru daripada created_at setelah diedit."
        );

        // Opsional: Cek response JSON juga mengandung data yang benar
        $response->assertJsonStructure([
            'data' => [
                'created_at',
                'updated_at',
            ],
        ]);
    }
}
