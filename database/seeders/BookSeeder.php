<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID Kategori
        $catInformatika = Category::where('name', 'Informatika')->first()->id;
        $catMesin = Category::where('name', 'Mesin')->first()->id;

        // 1. IMPORT BUKU INFORMATIKA
        $this->importCsv(base_path('data_informatika.csv'), $catInformatika);

        // 2. IMPORT BUKU MESIN
        $this->importCsv(base_path('data_mesin.csv'), $catMesin);
    }

    private function importCsv($path, $categoryId)
    {
        if (!file_exists($path)) return;

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // Skip header

        while (($data = fgetcsv($file)) !== FALSE) {
            // Logika sederhana: sesuaikan index kolom dengan file CSV kamu
            // Untuk file Informatika kamu, judul biasanya di index 2
            Book::create([
                'category_id'      => $categoryId,
                'title'            => $data[2] ?? 'Tanpa Judul',
                'author'           => $data[3] ?? '-',
                'publisher'        => $data[5] ?? '-',
                'publication_year' => (int)($data[6] ?? 0),
                'isbn'             => $data[8] ?? null,
                'stock'            => (int)($data[9] ?? 1),
            ]);
        }
        fclose($file);
    }
}
