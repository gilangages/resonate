<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_own_account()
    {
        // 1. Setup User & Data
        Storage::fake('public');
        $user = User::factory()->create();

        // Buat Avatar palsu
        $avatar = UploadedFile::fake()->image('avatar.jpg');
        $path = $avatar->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        // Buat Note dummy
        Note::factory()->create(['user_id' => $user->id]);

        // 2. Login
        Sanctum::actingAs($user);

        // 3. Action: Hit Endpoint Delete
        $response = $this->deleteJson('/api/users/current');

        // 4. Assertions
        $response->assertStatus(200)
            ->assertJson(['message' => 'Account deleted successfully.']);

        // Pastikan User hilang dari DB
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Pastikan Note hilang (Cascade check)
        $this->assertDatabaseEmpty('notes');

        // Pastikan File Avatar hilang dari Storage
        Storage::disk('public')->assertMissing($path);
    }

    public function test_guest_cannot_delete_account()
    {
        $response = $this->deleteJson('/api/users/current');
        $response->assertStatus(401); // Unauthorized
    }
}
