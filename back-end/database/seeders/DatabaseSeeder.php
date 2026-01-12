<?php

namespace Database\Seeders;

use Database\Seeders\SuperAdminSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil SuperAdminSeeder di sini
        $this->call([
            SuperAdminSeeder::class,
        ]);
    }
}
