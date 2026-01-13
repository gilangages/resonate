<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_avatar_is_updated_on_login_if_changed_in_google()
    {
        // 1. Setup: Buat user yang sudah ada dengan avatar lama
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'google_id' => '12345',
            'avatar' => 'https://old-avatar.com/image.jpg',
        ]);

        // 2. Mocking Socialite (Pura-pura jadi Google)
        // Kita simulasikan Google mengembalikan user yang sama, tapi dengan avatar BARU
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('12345');
        $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Test User');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://new-avatar-from-google.com/image.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturn($provider);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Action: Panggil endpoint callback Google
        // (Pastikan route ini sesuai dengan routes/api.php kamu, biasanya 'auth/google/callback')
        $response = $this->get('/api/auth/google/callback');

        // 4. Assert: Cek apakah di database avatar sudah berubah
        $response->assertStatus(302); // Redirect ke frontend

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
            'avatar' => 'https://new-avatar-from-google.com/image.jpg', // HARUS yang baru
        ]);

        // Pastikan avatar lama sudah tidak ada di record user ini
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'avatar' => 'https://old-avatar.com/image.jpg',
        ]);
    }
}
