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

    public function test_user_avatar_is_updated_if_current_avatar_is_url()
    {
        // Setup user dengan avatar berupa URL (menandakan ini dari Google/Eksternal)
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'google_id' => '12345',
            'avatar' => 'https://old-google-avatar.com/me.jpg', // Ini URL
        ]);

        $this->mockSocialite('test@example.com', '12345', 'https://new-google-avatar.com/me.jpg');

        $this->get('/api/auth/google/callback');

        // Harusnya BERUBAH karena yang lama cuma URL
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar' => 'https://new-google-avatar.com/me.jpg',
        ]);
    }

    /**
     * Skenario 2 (YANG DIMINTA): User sudah upload foto sendiri (Lokal).
     * Saat login Google, avatar TIDAK BOLEH berubah.
     */
    public function test_user_avatar_is_NOT_updated_if_current_avatar_is_local_file()
    {
        // Setup user dengan avatar path lokal (hasil upload)
        // Biasanya path lokal tidak punya http:// dan ada di folder avatars/
        $localPath = 'avatars/my-custom-photo.jpg';

        $user = User::factory()->create([
            'email' => 'custom@example.com',
            'google_id' => '67890',
            'avatar' => $localPath, // BUKAN URL
        ]);

        // Google mencoba menawarkan avatar baru
        $this->mockSocialite('custom@example.com', '67890', 'https://google.com/new-photo.jpg');

        $this->get('/api/auth/google/callback');

        // Cek Database: Avatar HARUS TETAP file lokal
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar' => $localPath,
        ]);

        // Pastikan avatar Google TIDAK masuk
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'avatar' => 'https://google.com/new-photo.jpg',
        ]);
    }

    // Helper untuk mock
    private function mockSocialite($email, $id, $avatar)
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn($id);
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('Test User');
        $abstractUser->shouldReceive('getAvatar')->andReturn($avatar);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturn($provider);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }
}
