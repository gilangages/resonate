<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Akun Super Admin
        User::firstOrCreate(
            ['email' => 'qbdian@gmail.com'], // Cek berdasarkan email agar tidak duplikat
            [
                'name' => 'Super Admin Abdian',
                'password' => Hash::make('abdian123'), // Ganti dengan password yang aman
                'role' => 'admin',
                'avatar' => null,
                'email_verified_at' => now(),
            ]
        );

        // Opsi: Tambahkan beberapa user dummy untuk testing moderasi
        // User::factory(10)->create();

        echo "Selesai! Akun admin: qbdian@gmail.com | password: abdian123 \n";
    }
}
