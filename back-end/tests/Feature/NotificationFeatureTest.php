<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserAppealNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Pastikan Notifikasi tersimpan di Database saat User Banding
     */
    public function test_appeal_creates_database_notification()
    {
        $user = User::factory()->create(['is_banned' => true]);
        $admin = User::factory()->create(['role' => 'admin']);

        // User kirim banding
        $this->postJson('/api/users/appeal', [
            'email' => $user->email,
            'message' => 'Tolong unban saya',
        ]);

        // Cek tabel notifications di database
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
            'type' => UserAppealNotification::class,
        ]);

        // Cek apakah data JSON-nya tersimpan benar
        $notification = $admin->notifications()->first();
        $this->assertEquals('Tolong unban saya', $notification->data['appeal_message']);
    }

    /**
     * Test 2: Pastikan API mengembalikan list notifikasi ke Admin
     */
    public function test_admin_can_fetch_notifications()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Buat notifikasi dummy
        Notification::send($admin, new UserAppealNotification('user@test.com', 'Tes Pesan'));

        // Login sebagai admin
        $response = $this->actingAs($admin)->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'data' => ['appeal_message', 'email'], 'read_at', 'created_at'],
                ],
            ]);
    }

    /**
     * Test 3: Pastikan API Hitung Unread Count (Untuk Badge Merah)
     */
    public function test_admin_can_get_unread_count()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Kirim 3 notifikasi
        Notification::send($admin, new UserAppealNotification('u1@test.com', 'Pesan 1'));
        Notification::send($admin, new UserAppealNotification('u2@test.com', 'Pesan 2'));
        Notification::send($admin, new UserAppealNotification('u3@test.com', 'Pesan 3'));

        // Tandai 1 sudah dibaca
        $admin->notifications()->first()->markAsRead();

        // Hitung
        $response = $this->actingAs($admin)->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJson(['count' => 2]); // Harusnya sisa 2 yang belum dibaca
    }
}
