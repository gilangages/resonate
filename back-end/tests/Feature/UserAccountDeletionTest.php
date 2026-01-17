<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Http\UploadedFile; // Tidak perlu lagi import ini
// use Illuminate\Support\Facades\Storage; // Tidak perlu lagi import ini
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_own_account()
    {
        // 1. Setup User & Data
        $user = User::factory()->create();

        // Kita tidak perlu lagi membuat file avatar palsu karena logika
        // penghapusan file sudah kita hapus di Controller demi performa.

        // Buat Note dummy untuk cek fitur Cascade Delete
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

        // Pastikan Note user tersebut juga hilang (Cascade check)
        $this->assertDatabaseEmpty('notes');

        // HAPUS baris Storage::assertMissing karena Controller tidak lagi menghapus file.
    }

    public function test_guest_cannot_delete_account()
    {
        $response = $this->deleteJson('/api/users/current');
        $response->assertStatus(401); // Unauthorized
    }
}
