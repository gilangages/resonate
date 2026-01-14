<?php

namespace Tests\Feature\Fixes;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAndMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_correct_role_structure()
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('user.role', 'admin');
    }

    public function test_my_notes_route_is_accessible_and_not_conflicted()
    {
        $user = User::factory()->create();
        Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->getJson('/api/notes/my');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_protected_route()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Asumsi ada route admin user list
        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(200);
    }

    // TEST TAMBAHAN: Pastikan Non-Admin Ditolak dengan Pesan Benar
    public function test_non_admin_cannot_access_admin_route()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(403);
        // Memastikan pesan debug kita muncul (Role Anda terdeteksi...)
        $response->assertJsonFragment(['message' => 'Hanya admin yang boleh masuk. Role Anda terdeteksi sebagai: [user]']);
    }
}
