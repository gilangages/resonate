<?php

namespace Tests\Feature\Fixes;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_role_can_be_assigned()
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'super@admin.com',
            'role' => 'admin',
        ]);
    }

    public function test_my_notes_route_does_not_conflict_with_show_id_route()
    {
        $user = User::factory()->create();

        // Note: Kita tidak perlu mengisi semua field karena Factory sudah mengurusnya
        Note::factory()->create([
            'user_id' => $user->id,
            'content' => 'Test Note Content',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/notes/my');

        $response->assertStatus(200);
        // Pastikan responnya adalah array data (pagination atau list)
        $response->assertJsonStructure(['data']);
    }

    public function test_show_note_route_still_works()
    {
        $user = User::factory()->create();
        // PERBAIKAN: Hapus 'is_private' karena kolom itu tidak ada
        $note = Note::factory()->create();

        // Akses detail note spesifik
        $response = $this->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $note->id]);
    }
}
