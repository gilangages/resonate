<?php

namespace Tests\Feature\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    // Skenario 1: User biasa GA BOLEH masuk halaman admin
    public function test_regular_user_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->getJson('/api/admin/users');

        $response->assertStatus(403); // Harusnya Forbidden
    }

    // Skenario 2: Admin BOLEH masuk
    public function test_admin_can_access_user_list()
    {
        $admin = User::factory()->admin()->create(); // Pakai factory admin yg kita buat tadi

        $response = $this->actingAs($admin)->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    // Skenario 3: Admin bisa hapus user biasa
    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/users/{$targetUser->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'is_banned' => true,
        ]);
    }

    public function test_admin_can_access_user_list_with_pagination()
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(15)->create(); // Buat 15 user

        $response = $this->actingAs($admin)->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => ['id', 'name', 'email', 'role', 'is_banned'], // Cek struktur data
                ],
                'total',
                'per_page',
            ]);

        // Default pagination 10, jadi harusnya ada 10 data di page 1 (termasuk admin)
        // Note: count bisa 10 atau 11 tergantung factory admin ikut terambil atau tidak (biasanya ikut)
        $this->assertCount(10, $response->json('data'));
    }

    public function test_admin_can_search_users()
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'SpesialTarget', 'email' => 'target@example.com']);
        User::factory()->count(5)->create(); // User lain

        // Search berdasarkan nama
        $response = $this->actingAs($admin)->getJson('/api/admin/users?search=Spesial');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data); // Harusnya cuma ketemu 1
        $this->assertEquals($target->email, $data[0]['email']);
    }
}
