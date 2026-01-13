<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Pastikan Model User (Accessor) menghasilkan URL dengan background.
     */
    public function test_user_model_generates_photo_url_with_background_color()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'avatar' => null, // Pastikan avatar null agar fallback ke photo_url
        ]);

        // Assert: URL harus mengandung parameter background
        $this->assertStringContainsString('backgroundColor=9a203e', $user->photo_url);
    }

    /**
     * Test 2: Pastikan Endpoint Register mengembalikan user dengan photo_url yang benar.
     */
    public function test_register_api_returns_correct_avatar_url()
    {
        $payload = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // PERBAIKAN: Route sesuai api.php (/users)
        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['photo_url']]);

        $photoUrl = $response->json('user.photo_url');

        $this->assertStringContainsString('api.dicebear.com', $photoUrl);
        $this->assertStringContainsString('backgroundColor=9a203e', $photoUrl);
    }

    /**
     * Test 3: Pastikan Endpoint Login mengembalikan data yang konsisten.
     */
    public function test_login_api_returns_correct_avatar_url()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
            'avatar' => null,
            'is_banned' => false, // Pastikan tidak di-banned agar bisa login
        ]);

        // PERBAIKAN: Route sesuai api.php (/users/login)
        $response = $this->postJson('/api/users/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $photoUrl = $response->json('user.photo_url');

        $this->assertStringContainsString('backgroundColor=9a203e', $photoUrl);
    }
}
