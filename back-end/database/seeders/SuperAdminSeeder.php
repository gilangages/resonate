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
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Cek berdasarkan email agar tidak duplikat
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // Ganti dengan password yang aman
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Opsi: Tambahkan beberapa user dummy untuk testing moderasi
        // User::factory(10)->create();

        echo "Selesai! Akun admin: admin@musicnote.com | password: password123 \n";
    }
}
