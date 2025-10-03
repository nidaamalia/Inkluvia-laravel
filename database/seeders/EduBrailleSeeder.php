<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialPage;
use Illuminate\Database\Seeder;

class EduBrailleSeeder extends Seeder
{
    public function run(): void
    {
        // Materi 1: Pengenalan Huruf dan Angka
        $material1 = Material::create([
            'judul' => 'Pengenalan Huruf dan Angka',
            'deskripsi' => 'Materi dasar untuk mempelajari huruf dan angka dalam braille',
            'kategori' => 'Dasar',
            'tingkat' => 'SD',
            'status' => 'published',
            'akses' => 'public',
            'created_by' => 1,
            'total_halaman' => 4
        ]);

        // Halaman 1 - Huruf A-Z
        MaterialPage::create([
            'material_id' => $material1->id,
            'page_number' => 1,
            'lines' => [
                'A B C D E F G H I J K L M',
                'N O P Q R S T U V W X Y Z'
            ]
        ]);

        // Halaman 2 - Angka 1-10
        MaterialPage::create([
            'material_id' => $material1->id,
            'page_number' => 2,
            'lines' => [
                '1 2 3 4 5',
                '6 7 8 9 10'
            ]
        ]);

        // Halaman 3 - Angka 10-20
        MaterialPage::create([
            'material_id' => $material1->id,
            'page_number' => 3,
            'lines' => [
                '10 11 12 13 14',
                '15 16 17 18 19 20'
            ]
        ]);

        // Halaman 4 - Angka 100++
        MaterialPage::create([
            'material_id' => $material1->id,
            'page_number' => 4,
            'lines' => [
                '100 200 300 400 500',
                '600 700 800 900 1000'
            ]
        ]);

        // Materi 2: Belajar Suku Kata
        $material2 = Material::create([
            'judul' => 'Belajar Suku Kata',
            'deskripsi' => 'Materi untuk mempelajari suku kata dasar dalam braille',
            'kategori' => 'Bahasa',
            'tingkat' => 'SD',
            'status' => 'published',
            'akses' => 'public',
            'created_by' => 1,
            'total_halaman' => 4
        ]);

        // Halaman 1 - Suku Kata Ba, Be, Bi, Bo, Bu (10 huruf)
        MaterialPage::create([
            'material_id' => $material2->id,
            'page_number' => 1,
            'lines' => [
                'Ba Be Bi Bo Bu',
                'Ca Ce Ci Co Cu',
                'Da De Di Do Du',
                'Fa Fe Fi Fo Fu',
                'Ga Ge Gi Go Gu'
            ]
        ]);

        // Halaman 2 - Lanjutan suku kata
        MaterialPage::create([
            'material_id' => $material2->id,
            'page_number' => 2,
            'lines' => [
                'Ha He Hi Ho Hu',
                'Ja Je Ji Jo Ju',
                'Ka Ke Ki Ko Ku',
                'La Le Li Lo Lu',
                'Ma Me Mi Mo Mu'
            ]
        ]);

        // Halaman 3 - Lanjutan suku kata
        MaterialPage::create([
            'material_id' => $material2->id,
            'page_number' => 3,
            'lines' => [
                'Na Ne Ni No Nu',
                'Pa Pe Pi Po Pu',
                'Ra Re Ri Ro Ru',
                'Sa Se Si So Su',
                'Ta Te Ti To Tu'
            ]
        ]);

        // Halaman 4 - Lanjutan suku kata
        MaterialPage::create([
            'material_id' => $material2->id,
            'page_number' => 4,
            'lines' => [
                'Va Ve Vi Vo Vu',
                'Wa We Wi Wo Wu',
                'Ya Ye Yi Yo Yu',
                'Za Ze Zi Zo Zu'
            ]
        ]);
    }
}
