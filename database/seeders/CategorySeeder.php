<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Kita buat mapping antara nama prodi dan kodenya
        $categories = [
            ['name' => 'Informatika', 'code' => 'IF'],
            ['name' => 'Elektro',     'code' => 'EL'],
            ['name' => 'Mesin',       'code' => 'MS'],
            ['name' => 'Umum',        'code' => 'UM'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'code' => $cat['code'],
            ]);
        }
    }
}
