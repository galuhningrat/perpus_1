<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. IMPORT BUKU INFORMATIKA (Real Data)
        // =============================================
        $this->importInformatikaBooks();

        // =============================================
        // 2. IMPORT BUKU MESIN (Real Data)
        // =============================================
        $this->importMesinBooks();

        // =============================================
        // 3. GENERATE DUMMY DATA (Elektro & Umum)
        // =============================================
        $this->generateDummyBooks();
    }

    private function importInformatikaBooks(): void
    {
        $catInformatika = Category::where('code', 'IF')->first();

        // Sample data dari CSV yang kamu upload
        $books = [
            ['title' => 'Discrete Mathematics and Its Applications', 'author' => 'Kenneth H. Rosen', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 2],
            ['title' => 'Menjadi Programmer C++ dalam 5 Tahap Pembelajaran', 'author' => 'Budi Raharjo', 'publisher' => 'Zurra InfiGro Media', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Struktur Data dengan Pemrograman Generik', 'author' => 'Dr. Eng. R.H. Sianipar, M.Eng', 'publisher' => 'Penerbit ANDI', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Visual C++ dalam 12 Pelajaran Yang Mudah', 'author' => 'Greg Perry & Ian Spencer', 'publisher' => 'Penerbit ANDI Yogyakarta', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Data Structures and Algorithm Analysis in C++', 'author' => 'Mark Allen Weiss', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Algoritma Pemrograman dan Struktur Data Menggunakan C++', 'author' => 'Cipta Ramadhani, ST., M.Eng.', 'publisher' => 'Penerbit ANDI', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Pemrograman GUI dengan C++ dan Qt', 'author' => '-', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => '50 Algorithms Every Programmer Should Know', 'author' => 'Imran Ahmad', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Code: The Hidden Language of Computer Hardware and Software', 'author' => 'Charles Petzold', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Computer System Architecture', 'author' => 'M. Morris Mano', 'publisher' => 'Prentice-Hall', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Jaringan Komputer TCP/IP', 'author' => 'Winarno Sugeng, Theta Dinnarwaty Putri', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Rekayasa Perangkat Lunak: Pendekatan Praktisi', 'author' => 'Roger S. Pressman, Ph.D.', 'publisher' => 'McGraw-Hill', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Introduction to Cryptography: Principles and Applications', 'author' => 'Hans Delfs, Helmut Knebl', 'publisher' => 'Springer', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Pengolahan Citra Digital & Teknik Pemrogramannya', 'author' => 'Usman Ahmad', 'publisher' => 'Penerbit Graha Ilmu', 'year' => null, 'isbn' => null, 'stock' => 2],
            ['title' => 'Artificial Intelligence: Searching, Reasoning, Planning, dan Learning', 'author' => 'Dr. Suyanto, S.T., M.Sc.', 'publisher' => 'Penerbit INFORMATIKA', 'year' => 2020, 'isbn' => null, 'stock' => 1],
            ['title' => 'Machine Learning & Computational Intelligence', 'author' => 'Widodo Budiharto', 'publisher' => 'Penerbit ANDI', 'year' => 2016, 'isbn' => '978-979-29-5849-2', 'stock' => 1],
            ['title' => 'Python Programming for Beginners', 'author' => 'Maxwell Ernstrom', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Basis Data', 'author' => 'Fathansyah', 'publisher' => 'Penerbit INFORMATIKA', 'year' => 2018, 'isbn' => '978-602-6232-66-3', 'stock' => 1],
            ['title' => 'Sistem Informasi Geografis: Konsep-Konsep Dasar', 'author' => 'Eddy Prahasta', 'publisher' => 'Penerbit INFORMATIKA', 'year' => null, 'isbn' => null, 'stock' => 1],
            ['title' => 'Computer Networks', 'author' => 'Andrew S. Tanenbaum', 'publisher' => '-', 'year' => null, 'isbn' => null, 'stock' => 1],
        ];

        foreach ($books as $book) {
            Book::create([
                'category_id' => $catInformatika->id,
                'title' => $book['title'],
                'author' => $book['author'],
                'publisher' => $book['publisher'],
                'publication_year' => $book['year'],
                'isbn' => $book['isbn'],
                'stock' => $book['stock'],
                'available_stock' => $book['stock'], // Awalnya semua tersedia
                'shelf_location' => 'IF-' . Str::upper(Str::random(3)),
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Imported 20 Informatika books (sample from your data)');
    }

    private function importMesinBooks(): void
    {
        $catMesin = Category::where('code', 'MS')->first();

        // Sample data dari PDF Mesin
        $books = [
            ['title' => 'Menggambar Mesin Menurut Standar ISO', 'author' => 'G. Takeshi Sato N. Sugiarto H', 'publisher' => 'PT. Pradnya Paramita', 'year' => 1981, 'isbn' => null, 'stock' => 6],
            ['title' => 'Dasar Perencanaan dan Pemilihan Elemen Mesin', 'author' => 'Ir. Sularso, MSME', 'publisher' => 'PT. Pradnya Paramita Jakarta', 'year' => 1991, 'isbn' => null, 'stock' => 3],
            ['title' => 'Mekanika Teknik 2', 'author' => 'B. Sudibyo, Ing. HTL', 'publisher' => 'ATMI Press Solo', 'year' => 1983, 'isbn' => null, 'stock' => 1],
            ['title' => 'Fisika untuk Sains dan Teknik', 'author' => 'Paul A. Tipler', 'publisher' => 'Erlangga', 'year' => 1991, 'isbn' => null, 'stock' => 2],
            ['title' => 'Manufacturing Engineering and Technology', 'author' => 'Serope Kalpakjian, Steven Schmid', 'publisher' => 'Education South Asia', 'year' => 2006, 'isbn' => null, 'stock' => 1],
            ['title' => 'Machine Design An Integrated Approach', 'author' => 'Robert L. Norton', 'publisher' => 'Pearson International Edition', 'year' => 2006, 'isbn' => null, 'stock' => 1],
            ['title' => 'Teknologi Mekanik', 'author' => 'B.H. Amstead, Phillip F. Ostwald, Myron L. Begeman', 'publisher' => 'Erlangga', 'year' => 1995, 'isbn' => null, 'stock' => 1],
            ['title' => 'Product Design and Development', 'author' => 'Karl T. Ulrich, Steven D. Eppinger', 'publisher' => 'MC Graw Hill', 'year' => 2003, 'isbn' => null, 'stock' => 1],
            ['title' => 'Mechanical Engineering Design', 'author' => 'Richard G. Budynas, J. Nisbett', 'publisher' => 'ME Graw Hill Education', 'year' => 2014, 'isbn' => null, 'stock' => 1],
            ['title' => 'Dasar Perancangan Teknik Mesin', 'author' => 'Eka Yogaswara', 'publisher' => 'CV Amico', 'year' => 2019, 'isbn' => null, 'stock' => 2],
            ['title' => 'Ilmu dan Teknologi Bahan', 'author' => 'Lawrence H. Van Vlack, Sriati Djaprie', 'publisher' => 'Erlangga', 'year' => 1981, 'isbn' => null, 'stock' => 1],
            ['title' => 'Teknik Permesinan Gerinda', 'author' => 'Eka Yogaswara', 'publisher' => 'CV Amico', 'year' => 2017, 'isbn' => null, 'stock' => 1],
            ['title' => 'Teknik Pengelasan Logam', 'author' => 'Ir. Suharto', 'publisher' => 'Rineka Cipta', 'year' => 1991, 'isbn' => null, 'stock' => 1],
            ['title' => 'Mekanika Bahan', 'author' => 'James M. Gere', 'publisher' => 'Erlangga', 'year' => 1996, 'isbn' => null, 'stock' => 1],
            ['title' => 'Teknik Permesinan CNC Bubut', 'author' => 'Eka Yogaswara', 'publisher' => 'Armico Bandung', 'year' => 2017, 'isbn' => null, 'stock' => 1],
        ];

        foreach ($books as $book) {
            Book::create([
                'category_id' => $catMesin->id,
                'title' => $book['title'],
                'author' => $book['author'],
                'publisher' => $book['publisher'],
                'publication_year' => $book['year'],
                'isbn' => $book['isbn'],
                'stock' => $book['stock'],
                'available_stock' => $book['stock'],
                'shelf_location' => 'MS-' . Str::upper(Str::random(3)),
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Imported 15 Mesin books (sample from your data)');
    }

    private function generateDummyBooks(): void
    {
        $catElektro = Category::where('code', 'EL')->first();
        $catUmum = Category::where('code', 'UM')->first();

        // Dummy Elektro Books
        $elektroBooks = [
            ['title' => 'Rangkaian Listrik', 'author' => 'William H. Hayt', 'publisher' => 'Erlangga', 'year' => 2012],
            ['title' => 'Elektronika Analog', 'author' => 'Thomas L. Floyd', 'publisher' => 'Pearson', 'year' => 2015],
            ['title' => 'Sistem Tenaga Listrik', 'author' => 'Ir. Sulasno', 'publisher' => 'Erlangga', 'year' => 2010],
            ['title' => 'Mikroprosesor dan Mikrokontroler', 'author' => 'Agfianto Eko Putra', 'publisher' => 'Andi', 'year' => 2016],
            ['title' => 'Telekomunikasi Digital', 'author' => 'Bernard Sklar', 'publisher' => 'Prentice Hall', 'year' => 2001],
        ];

        foreach ($elektroBooks as $book) {
            Book::create([
                'category_id' => $catElektro->id,
                'title' => $book['title'],
                'author' => $book['author'],
                'publisher' => $book['publisher'],
                'publication_year' => $book['year'],
                'stock' => rand(1, 3),
                'available_stock' => rand(1, 3),
                'shelf_location' => 'EL-' . Str::upper(Str::random(3)),
                'is_active' => true,
            ]);
        }

        // Dummy Umum Books
        $umumBooks = [
            ['title' => 'Kalkulus Jilid 1', 'author' => 'Purcell & Varberg', 'publisher' => 'Erlangga', 'year' => 2010],
            ['title' => 'Matematika Diskrit', 'author' => 'Rinaldi Munir', 'publisher' => 'Informatika', 'year' => 2012],
            ['title' => 'Aljabar Linear', 'author' => 'Howard Anton', 'publisher' => 'Erlangga', 'year' => 2004],
            ['title' => 'Statistika Dasar', 'author' => 'Sudjana', 'publisher' => 'Tarsito', 'year' => 2005],
            ['title' => 'Bahasa Inggris Teknik', 'author' => 'Engineering English Team', 'publisher' => 'Graha Ilmu', 'year' => 2018],
        ];

        foreach ($umumBooks as $book) {
            Book::create([
                'category_id' => $catUmum->id,
                'title' => $book['title'],
                'author' => $book['author'],
                'publisher' => $book['publisher'],
                'publication_year' => $book['year'],
                'stock' => rand(2, 5),
                'available_stock' => rand(2, 5),
                'shelf_location' => 'UM-' . Str::upper(Str::random(3)),
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Generated 5 Elektro + 5 Umum dummy books');
    }
}
