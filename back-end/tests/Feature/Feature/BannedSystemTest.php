<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserAppealNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Import atribut Test baru

class BannedSystemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_ban_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_banned' => true,
        ]);
    }

    #[Test]
    public function banned_user_cannot_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_banned' => true,
            'ban_reason' => 'Melanggar aturan.',
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Ekspektasi: 403 Forbidden + JSON berisi status banned
        $response->assertStatus(403)
            ->assertJson([
                'status' => 'banned',
                'message' => 'AKUN_DIBEKUKAN',
                'reason' => 'Melanggar aturan.',
            ]);
    }

    #[Test]
    public function banned_user_can_submit_appeal()
    {
        Notification::fake(); // Mocking notifikasi

        $admin = User::factory()->create(['role' => 'admin']);
        $bannedUser = User::factory()->create(['is_banned' => true]);

        $response = $this->postJson('/api/users/appeal', [
            'email' => $bannedUser->email,
            'reason' => 'Mohon maaf saya khilaf, tolong buka blokir saya.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Permintaan banding telah dikirim ke Admin.']);

        // Pastikan notifikasi terkirim ke admin
        Notification::assertSentTo(
            [$admin], UserAppealNotification::class
        );
    }

    #[Test]
    public function admin_can_restore_banned_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bannedUser = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$bannedUser->id}/restore");

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $bannedUser->id,
            'is_banned' => false, // Harusnya sudah false (aktif kembali)
        ]);
    }
}
