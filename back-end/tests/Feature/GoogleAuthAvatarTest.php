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

    public function test_it_converts_small_google_avatar_to_high_res()
    {
        // 1. Mock Socialite User
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        // Simulasi data dari Google (Perhatikan URL-nya kecil: s96-c)
        $abstractUser->shouldReceive('getId')->andReturn('123456789');
        $abstractUser->shouldReceive('getName')->andReturn('Abdian Test');
        $abstractUser->shouldReceive('getEmail')->andReturn('abdian@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a-/AOh14Bg=s96-c');

        // 2. Mock Socialite Provider
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturn($provider);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        // Inject Mock ke Facade Socialite
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Panggil Route Callback
        $response = $this->get('/api/auth/google/callback');

        // 4. Assertions

        // Pastikan redirect berhasil (biasanya 302 ke frontend)
        $response->assertStatus(302);

        // Cek Database: Pastikan URL yang tersimpan BUKAN s96-c, tapi s1024
        $this->assertDatabaseHas('users', [
            'email' => 'abdian@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14Bg=s1024', // Target kita
        ]);

        // Pastikan URL lama yang kecil TIDAK ada
        $this->assertDatabaseMissing('users', [
            'email' => 'abdian@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14Bg=s96-c',
        ]);
    }
}
