<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lembaga;

class LembagaSeeder extends Seeder
{
    public function run(): void
    {
        $lembagas = [
            [
                'nama' => 'Pengguna Mandiri',
                'type' => 'Individu',
                'alamat' => '',
                'deskripsi' => 'Kategori khusus untuk pengguna individu yang menggunakan platform untuk keperluan pribadi'
            ],
            [
                'nama' => 'SLB A Surabaya',
                'type' => 'Sekolah',
                'alamat' => '',
                'login_key' => '1234567890',
                'deskripsi' => 'Sekolah Luar Biasa dengan program pendidikan inklusif'
            ],
            [
                'nama' => 'Kawan Netra',
                'type' => 'Lembaga',
                'alamat' => 'Pucang Anom I No.22, Pucang Sewu, Kec. Gubeng, Surabaya', 
                'login_key' => '1234567891',
                'deskripsi' => 'Lembaga yang fokus pada pemberdayaan penyandang disabilitas netra'
            ],
        ];

        foreach ($lembagas as $lembaga) {
            Lembaga::create($lembaga);
        }
    }
}