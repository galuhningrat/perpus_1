<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Database Seeding...');
        $this->command->newLine();

        // Urutan penting: Categories dulu, baru Users & Books
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            BookSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database Seeding Completed!');
        $this->command->info('📊 Login Credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@stti.ac.id', 'admin123'],
                ['Pustakawan', 'pustaka1@stti.ac.id', 'pustaka123'],
                ['Mahasiswa (IF)', 'rizki.if23@student.stti.ac.id', 'mahasiswa123'],
                ['Dosen', 'hendra.dosen@stti.ac.id', 'dosen123'],
            ]
        );
    }
}
