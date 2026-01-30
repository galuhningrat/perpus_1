<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Informatika',
                'code' => 'IF',
                'description' => 'Buku-buku terkait Ilmu Komputer, Pemrograman, Jaringan, Database, AI, dan teknologi informasi lainnya'
            ],
            [
                'name' => 'Elektro',
                'code' => 'EL',
                'description' => 'Buku-buku terkait Teknik Elektro, Elektronika, Sistem Tenaga, Telekomunikasi, dan Kontrol'
            ],
            [
                'name' => 'Mesin',
                'code' => 'MS',
                'description' => 'Buku-buku terkait Teknik Mesin, Manufaktur, Termodinamika, Material, dan Otomotif'
            ],
            [
                'name' => 'Umum',
                'code' => 'UM',
                'description' => 'Buku-buku Matematika, Fisika, Bahasa, Manajemen, dan Pengetahuan Umum lainnya'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ 4 Categories created successfully!');
    }
}
