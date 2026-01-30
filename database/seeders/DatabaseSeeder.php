<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin agar bisa login
        User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@stti.com',
            'nim_nidn' => '00000000', // Sesuai aturan unique & not null kamu
            'role' => 'super_admin',
            'password' => bcrypt('password'),
        ]);

        // 2. Panggil Seeder lainnya
        $this->call([
            CategorySeeder::class,
            BookSeeder::class,
        ]);
    }
}
