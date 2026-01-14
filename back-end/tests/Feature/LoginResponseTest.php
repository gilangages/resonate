<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginResponseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test apakah response login menyertakan field 'role'.
     */
    public function test_login_api_returns_role_field()
    {
        // 1. Buat user admin
        $password = 'password123';
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt($password),
            'role' => 'admin',
        ]);

        // 2. Lakukan request login
        $response = $this->postJson('/api/users/login', [
            'email' => 'admin@test.com',
            'password' => $password,
        ]);

        // 3. Pastikan login sukses
        $response->assertStatus(200);

        // 4. CEK STRUKTUR JSON
        // Pastikan di dalam object 'user' terdapat key 'role'
        // dan nilainya adalah 'admin'
        $response->assertJsonStructure([
            'user' => [
                'id',
                'email',
                'role', // Ini yang krusial
            ],
            'token',
        ]);

        $response->assertJsonPath('user.role', 'admin');
    }
}
