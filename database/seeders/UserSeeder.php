<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // SUPER ADMIN (Kaprodi/Kepala Perpustakaan)
        // =============================================
        User::create([
            'nim_nidn' => 'ADMIN001',
            'name' => 'Dr. Budi Santoso, S.T., M.T.',
            'email' => 'admin@stti.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'prodi' => 'Umum',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // =============================================
        // PUSTAKAWAN (Staf Perpustakaan)
        // =============================================
        $pustakawan = [
            [
                'nim_nidn' => 'PUSTAKA001',
                'name' => 'Siti Nurhaliza, S.Sos.',
                'email' => 'pustaka1@stti.ac.id',
                'password' => Hash::make('pustaka123'),
                'role' => 'pustakawan',
                'prodi' => 'Umum',
                'phone' => '081234567891',
            ],
            [
                'nim_nidn' => 'PUSTAKA002',
                'name' => 'Andi Wijaya, S.Kom.',
                'email' => 'pustaka2@stti.ac.id',
                'password' => Hash::make('pustaka123'),
                'role' => 'pustakawan',
                'prodi' => 'Umum',
                'phone' => '081234567892',
            ],
        ];

        foreach ($pustakawan as $p) {
            User::create($p);
        }

        // =============================================
        // MEMBER - MAHASISWA (Sample Data)
        // =============================================
        $mahasiswa = [
            // Informatika
            [
                'nim_nidn' => '20230101001',
                'name' => 'Ahmad Rizki Pratama',
                'email' => 'rizki.if23@student.stti.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'member',
                'prodi' => 'Informatika',
                'phone' => '081234560001',
            ],
            [
                'nim_nidn' => '20230101002',
                'name' => 'Dina Amelia Putri',
                'email' => 'dina.if23@student.stti.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'member',
                'prodi' => 'Informatika',
                'phone' => '081234560002',
            ],
            // Elektro
            [
                'nim_nidn' => '20230201001',
                'name' => 'Farhan Maulana',
                'email' => 'farhan.el23@student.stti.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'member',
                'prodi' => 'Elektro',
                'phone' => '081234560003',
            ],
            [
                'nim_nidn' => '20230201002',
                'name' => 'Siti Rahmawati',
                'email' => 'siti.el23@student.stti.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'member',
                'prodi' => 'Elektro',
                'phone' => '081234560004',
            ],
            // Mesin
            [
                'nim_nidn' => '20230301001',
                'name' => 'Budi Hartono',
                'email' => 'budi.ms23@student.stti.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'member',
                'prodi' => 'Mesin',
                'phone' => '081234560005',
            ],
            [
                'nim_nidn' => '20230301002',
                'name' => 'Dewi Kusuma',
                'email' => 'dewi.ms23@student.stti.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'member',
                'prodi' => 'Mesin',
                'phone' => '081234560006',
            ],
        ];

        foreach ($mahasiswa as $mhs) {
            User::create($mhs);
        }

        // =============================================
        // MEMBER - DOSEN (Sample Data)
        // =============================================
        $dosen = [
            [
                'nim_nidn' => 'NIDN0101088801',
                'name' => 'Dr. Eng. Hendra Wijaya, S.T., M.T.',
                'email' => 'hendra.dosen@stti.ac.id',
                'password' => Hash::make('dosen123'),
                'role' => 'member',
                'prodi' => 'Informatika',
                'phone' => '081234570001',
            ],
            [
                'nim_nidn' => 'NIDN0102089002',
                'name' => 'Ir. Eka Yogaswara, M.T.',
                'email' => 'eka.dosen@stti.ac.id',
                'password' => Hash::make('dosen123'),
                'role' => 'member',
                'prodi' => 'Mesin',
                'phone' => '081234570002',
            ],
        ];

        foreach ($dosen as $d) {
            User::create($d);
        }

        $this->command->info('✅ Users created: 1 Super Admin, 2 Pustakawan, 6 Mahasiswa, 2 Dosen');
    }
}
