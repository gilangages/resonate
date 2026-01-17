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
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]); // Cek DB user hilang
    }
}
