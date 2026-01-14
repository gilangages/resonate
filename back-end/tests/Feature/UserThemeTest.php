<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// <--- Import attribute ini

class UserThemeTest extends TestCase
{
    use RefreshDatabase;

    #[Test] // <--- Gunakan attribute ini sebagai pengganti /** @test */
    public function user_baru_mendapat_tema_merah_dan_bisa_diubah(): void
    {
        // Pastikan kolom card_theme ada di $fillable Model User.php
        $user = User::factory()->create(['card_theme' => 'red']);

        $response = $this->actingAs($user)
            ->patchJson('/api/users/current', [
                'card_theme' => 'blue',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('blue', $user->fresh()->card_theme);
    }

    #[Test]
    public function tema_tetap_tersimpan_setelah_logout_dan_login_kembali(): void
    {
        $user = User::factory()->create(['card_theme' => 'purple']);

        // Simulasi ambil profil (userDetail)
        $response = $this->actingAs($user)
            ->getJson('/api/users/current');

        $response->assertStatus(200)
            ->assertJsonPath('data.card_theme', 'purple');
    }

    #[Test]
    public function user_baru_mendapat_tema_merah_dan_bisa_diubah_permanen(): void
    {
        // 1. User daftar (default merah)
        $user = User::factory()->create(['card_theme' => 'red']);

        // 2. User ubah tema ke biru via API
        $response = $this->actingAs($user)
            ->patchJson('/api/users/current', ['card_theme' => 'blue']);

        $response->assertStatus(200);

        // 3. Pastikan di database sudah biru
        $this->assertEquals('blue', $user->fresh()->card_theme);

        // 4. Simulasi Login Ulang (ambil profile)
        $responseProfile = $this->actingAs($user)
            ->getJson('/api/users/current');

        // Pastikan response profile mengirimkan tema biru
        $responseProfile->assertJsonPath('data.card_theme', 'blue');
    }

};
