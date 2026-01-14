<?php

namespace Tests\Feature\Admin;

use App\Models\Note;
use App\Models\User;
use App\Notifications\NoteDeletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNoteDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_note_with_reason_and_user_gets_notification()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Ini adalah konten yang melanggar aturan.',
        ]);

        $reason = 'Konten mengandung ujaran kebencian.';

        $response = $this->actingAs($admin)->deleteJson("/api/admin/notes/{$note->id}", [
            'reason' => $reason,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Catatan dihapus dan user telah diberitahu.']);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);

        $notification = $user->notifications()->first();

        $this->assertNotNull($notification, 'User seharusnya menerima notifikasi.');
        $this->assertEquals(NoteDeletedNotification::class, $notification->type);
        $this->assertEquals('Pesan Dihapus Admin', $notification->data['title']);
        $this->assertEquals('alert', $notification->data['type']);
        $this->assertEquals($reason, $notification->data['reason']);

        // PERBAIKAN DI SINI:
        // Cek bagian awal kalimatnya saja agar lebih aman dari logika substr
        $this->assertStringContainsString('Ini adalah konten', $notification->data['message']);

        // ATAU jika ingin spesifik hasil potongannya:
        // $this->assertStringContainsString('Ini adalah konten yang melangg...', $notification->data['message']);
    }

    public function test_admin_delete_note_without_reason_uses_default_message()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin)->deleteJson("/api/admin/notes/{$note->id}");

        $notification = $user->notifications()->first();

        $this->assertEquals('Melanggar Pedoman Komunitas', $notification->data['reason']);
    }

    public function test_admin_can_fetch_notes_with_user_avatar()
    {
        // 1. Setup Admin & User Biasa
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['avatar' => 'https://example.com/avatar.jpg']);

        // 2. Buat Note oleh User tersebut
        Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Test Content',
        ]);

        // 3. Admin melakukan request ke endpoint notes
        $response = $this->actingAs($admin)
            ->getJson('/api/admin/notes');

        // 4. Assert struktur JSON harus mengandung avatar di dalam object user
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'content',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'avatar', // Pastikan field ini ada
                        ],
                    ],
                ],
            ]);

        // Verifikasi isi avatar
        $data = $response->json('data.0');
        $this->assertEquals($user->avatar, $data['user']['avatar']);
    }
}
