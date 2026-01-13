<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserAppealNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AppealSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_can_submit_appeal_with_message()
    {
        Notification::fake();

        // 1. Buat User yang di-banned
        $user = User::factory()->create([
            'is_banned' => true,
            'ban_reason' => 'Spamming',
        ]);

        // 2. Buat Admin
        $admin = User::factory()->create(['role' => 'admin']);

        // 3. User mengirim request sanggahan
        $appealMessage = "Maaf min, akun saya dibajak kemarin. Tolong pulihkan.";

        $response = $this->postJson('/api/users/appeal', [
            'email' => $user->email,
            'message' => $appealMessage,
        ]);

        // 4. Assert Response Sukses
        $response->assertStatus(200)
            ->assertJson(['message' => 'Permintaan banding telah dikirim ke Admin.']);

        // 5. Assert Notifikasi Terkirim ke Admin dengan PESAN YANG BENAR
        Notification::assertSentTo(
            [$admin],
            UserAppealNotification::class,
            function ($notification, $channels, $notifiable) use ($user, $appealMessage) {
                // PERBAIKAN DI SINI: Gunakan toArray, bukan toDatabase
                $data = $notification->toArray($notifiable);

                return $data['email'] === $user->email &&
                    $data['appeal_message'] === $appealMessage;
            }
        );
    }

    public function test_appeal_requires_message()
    {
        $response = $this->postJson('/api/users/appeal', [
            'email' => 'some@email.com',
            'message' => '', // Kosong
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }
}
