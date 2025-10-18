<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MaterialSeeder extends Seeder
{
    private $adminUser;
    
    public function run()
    {
        // Get or create admin user
        $this->adminUser = User::where('role', 'admin')->first();
        
        if (!$this->adminUser) {
            $this->command->error('No admin user found. Please create an admin user first.');
            return;
        }

        $this->command->info('Starting material seeding...');

        // FASE A - Kelas 1 & 2 SD
        $this->seedFaseA();
        
        // FASE B - Kelas 3 & 4 SD
        $this->seedFaseB();
        
        // FASE C - Kelas 5 & 6 SD
        $this->seedFaseC();
        
        // FASE D - Kelas 7, 8, 9 SMP
        $this->seedFaseD();
        
        // FASE E - Kelas 10 SMA
        $this->seedFaseE();
        
        // FASE F - Kelas 11 & 12 SMA
        $this->seedFaseF();

        $this->command->info('Material seeding completed successfully!');
    }

    // ==================== FASE A ====================
    private function seedFaseA()
    {
        $this->command->info('Seeding FASE A materials...');

        // Pendidikan Pancasila
        $this->createMaterial([
            'judul' => 'Aku dan Aturan di Rumah',
            'kategori' => 'agama',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Memahami pentingnya aturan di rumah dan cara menerapkannya dalam kehidupan sehari-hari.',
        ]);

        $this->createMaterial([
            'judul' => 'Mengenal Simbol-Simbol Pancasila',
            'kategori' => 'agama',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Mengenalkan lambang-lambang Pancasila kepada siswa kelas rendah.',
        ]);

        // Bahasa Indonesia
        $this->createMaterial([
            'judul' => 'Bunyi dan Huruf',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Pengenalan bunyi dan huruf untuk pembelajaran membaca awal.',
        ]);

        $this->createMaterial([
            'judul' => 'Membaca Suku Kata dan Kata',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Belajar membaca dengan metode suku kata untuk pemula.',
        ]);

        $this->createMaterial([
            'judul' => 'Mengenal Tanda Baca Titik dan Koma',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '2',
            'deskripsi' => 'Memahami penggunaan tanda baca dasar dalam kalimat.',
        ]);

        // Matematika
        $this->createMaterial([
            'judul' => 'Bilangan Cacah sampai 100',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Mengenal dan menghitung bilangan cacah dari 1 hingga 100.',
        ]);

        $this->createMaterial([
            'judul' => 'Penjumlahan dan Pengurangan Dasar',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Operasi hitung dasar penjumlahan dan pengurangan untuk pemula.',
        ]);

        $this->createMaterial([
            'judul' => 'Mengenal Bentuk Bangun Datar',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '2',
            'deskripsi' => 'Mengenal segitiga, persegi, dan lingkaran beserta ciri-cirinya.',
        ]);

        // IPAS
        $this->createMaterial([
            'judul' => 'Tubuhku dan Panca Indra',
            'kategori' => 'ipa',
            'tingkat' => 'sd',
            'kelas' => '1',
            'deskripsi' => 'Mengenal bagian tubuh dan fungsi panca indra manusia.',
        ]);

        $this->createMaterial([
            'judul' => 'Hewan dan Tumbuhan di Sekitarku',
            'kategori' => 'ipa',
            'tingkat' => 'sd',
            'kelas' => '2',
            'deskripsi' => 'Mengenal berbagai jenis hewan dan tumbuhan di lingkungan sekitar.',
        ]);
    }

    // ==================== FASE B ====================
    private function seedFaseB()
    {
        $this->command->info('Seeding FASE B materials...');

        // Pendidikan Pancasila
        $this->createMaterial([
            'judul' => 'Hak dan Kewajiban di Rumah dan Sekolah',
            'kategori' => 'agama',
            'tingkat' => 'sd',
            'kelas' => '3',
            'deskripsi' => 'Memahami hak dan kewajiban sebagai anggota keluarga dan siswa.',
        ]);

        $this->createMaterial([
            'judul' => 'Keragaman Suku Bangsa dan Budaya di Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'sd',
            'kelas' => '4',
            'deskripsi' => 'Mengenal keberagaman suku, budaya, dan adat istiadat di Indonesia.',
        ]);

        // Bahasa Indonesia
        $this->createMaterial([
            'judul' => 'Ide Pokok dan Kalimat Utama dalam Paragraf',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '3',
            'deskripsi' => 'Mengidentifikasi ide pokok dan kalimat utama dalam sebuah paragraf.',
        ]);

        $this->createMaterial([
            'judul' => 'Menulis Karangan Sederhana',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '4',
            'deskripsi' => 'Belajar menulis karangan narasi dan deskripsi sederhana.',
        ]);

        // Matematika
        $this->createMaterial([
            'judul' => 'Bilangan Cacah sampai 1.000',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '3',
            'deskripsi' => 'Mengenal dan mengoperasikan bilangan cacah hingga ribuan.',
        ]);

        $this->createMaterial([
            'judul' => 'Operasi Hitung Perkalian dan Pembagian',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '3',
            'deskripsi' => 'Memahami konsep perkalian dan pembagian bilangan bulat.',
        ]);

        $this->createMaterial([
            'judul' => 'Pengenalan Pecahan Sederhana',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '4',
            'deskripsi' => 'Memahami konsep pecahan dan operasi dasarnya.',
        ]);

        // IPAS
        $this->createMaterial([
            'judul' => 'Wujud Zat dan Perubahannya',
            'kategori' => 'ipa',
            'tingkat' => 'sd',
            'kelas' => '3',
            'deskripsi' => 'Mengenal wujud zat (padat, cair, gas) dan perubahannya.',
        ]);

        $this->createMaterial([
            'judul' => 'Siklus Hidup Makhluk Hidup',
            'kategori' => 'ipa',
            'tingkat' => 'sd',
            'kelas' => '4',
            'deskripsi' => 'Memahami siklus hidup berbagai makhluk hidup.',
        ]);

        // Bahasa Inggris
        $this->createMaterial([
            'judul' => 'Greetings and Introduction',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sd',
            'kelas' => '3',
            'deskripsi' => 'Basic English greetings and self-introduction for beginners.',
        ]);

        $this->createMaterial([
            'judul' => 'My Family',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sd',
            'kelas' => '4',
            'deskripsi' => 'Learning family members vocabulary in English.',
        ]);
    }

    // ==================== FASE C ====================
    private function seedFaseC()
    {
        $this->command->info('Seeding FASE C materials...');

        // Pendidikan Pancasila
        $this->createMaterial([
            'judul' => 'Makna dan Penerapan Sila-Sila Pancasila',
            'kategori' => 'agama',
            'tingkat' => 'sd',
            'kelas' => '5',
            'deskripsi' => 'Memahami makna setiap sila Pancasila dan penerapannya dalam kehidupan.',
        ]);

        $this->createMaterial([
            'judul' => 'Bhinneka Tunggal Ika',
            'kategori' => 'ips',
            'tingkat' => 'sd',
            'kelas' => '6',
            'deskripsi' => 'Memahami makna Bhinneka Tunggal Ika sebagai semboyan persatuan Indonesia.',
        ]);

        // Bahasa Indonesia
        $this->createMaterial([
            'judul' => 'Menentukan Gagasan Pokok dari Teks',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '5',
            'deskripsi' => 'Mengidentifikasi gagasan pokok dari berbagai jenis teks.',
        ]);

        $this->createMaterial([
            'judul' => 'Menulis Surat Pribadi dan Surat Resmi Sederhana',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sd',
            'kelas' => '6',
            'deskripsi' => 'Belajar menulis surat dengan format yang benar.',
        ]);

        // Matematika
        $this->createMaterial([
            'judul' => 'Operasi Hitung Bilangan Bulat dan Pecahan',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '5',
            'deskripsi' => 'Operasi hitung campuran bilangan bulat dan pecahan.',
        ]);

        $this->createMaterial([
            'judul' => 'Volume Kubus dan Balok',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '6',
            'deskripsi' => 'Menghitung volume bangun ruang kubus dan balok.',
        ]);

        $this->createMaterial([
            'judul' => 'Koordinat Kartesius Sederhana',
            'kategori' => 'matematika',
            'tingkat' => 'sd',
            'kelas' => '6',
            'deskripsi' => 'Pengenalan sistem koordinat Kartesius dan cara membaca grafik.',
        ]);

        // IPAS
        $this->createMaterial([
            'judul' => 'Sistem Organ Manusia',
            'kategori' => 'ipa',
            'tingkat' => 'sd',
            'kelas' => '5',
            'deskripsi' => 'Mempelajari sistem pernapasan, pencernaan, dan peredaran darah manusia.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem Tata Surya',
            'kategori' => 'ipa',
            'tingkat' => 'sd',
            'kelas' => '6',
            'deskripsi' => 'Mengenal planet-planet dan benda langit dalam tata surya.',
        ]);

        $this->createMaterial([
            'judul' => 'Negara-Negara di ASEAN',
            'kategori' => 'ips',
            'tingkat' => 'sd',
            'kelas' => '6',
            'deskripsi' => 'Mengenal negara-negara anggota ASEAN dan kerja sama regional.',
        ]);
    }

    // ==================== FASE D ====================
    private function seedFaseD()
    {
        $this->command->info('Seeding FASE D materials...');

        // Pendidikan Pancasila
        $this->createMaterial([
            'judul' => 'Sejarah Lahirnya Pancasila dan UUD NRI Tahun 1945',
            'kategori' => 'agama',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Mempelajari sejarah perumusan Pancasila dan UUD 1945.',
        ]);

        $this->createMaterial([
            'judul' => 'Demokrasi dan Kedaulatan Rakyat',
            'kategori' => 'agama',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Memahami prinsip demokrasi dan kedaulatan rakyat di Indonesia.',
        ]);

        // Bahasa Indonesia
        $this->createMaterial([
            'judul' => 'Teks Deskripsi dan Narasi',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Menganalisis struktur dan unsur kebahasaan teks deskripsi dan narasi.',
        ]);

        $this->createMaterial([
            'judul' => 'Teks Prosedur',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Memahami teks prosedur dan cara membuat langkah-langkah yang jelas.',
        ]);

        $this->createMaterial([
            'judul' => 'Teks Eksplanasi dan Ulasan',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Menganalisis teks eksplanasi fenomena alam dan ulasan karya.',
        ]);

        $this->createMaterial([
            'judul' => 'Drama dan Karya Fiksi',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Mempelajari unsur intrinsik drama dan karya fiksi.',
        ]);

        // Matematika
        $this->createMaterial([
            'judul' => 'Bilangan Bulat dan Pecahan',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Operasi hitung bilangan bulat dan pecahan tingkat lanjut.',
        ]);

        $this->createMaterial([
            'judul' => 'Bentuk Aljabar dan Operasinya',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Mengenal bentuk aljabar dan operasi penjumlahan, pengurangan, perkalian, dan pembagian.',
        ]);

        $this->createMaterial([
            'judul' => 'Persamaan Linear Satu Variabel',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Menyelesaikan persamaan dan pertidaksamaan linear satu variabel.',
        ]);

        $this->createMaterial([
            'judul' => 'Teorema Pythagoras',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Memahami dan menerapkan teorema Pythagoras dalam segitiga siku-siku.',
        ]);

        $this->createMaterial([
            'judul' => 'Statistika - Penyajian dan Ukuran Pemusatan Data',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Menyajikan data dalam diagram dan menghitung mean, median, modus.',
        ]);

        $this->createMaterial([
            'judul' => 'Persamaan Kuadrat',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Menyelesaikan persamaan kuadrat dengan berbagai metode.',
        ]);

        $this->createMaterial([
            'judul' => 'Transformasi Geometri',
            'kategori' => 'matematika',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Memahami refleksi, translasi, rotasi, dan dilatasi pada bidang koordinat.',
        ]);

        // IPA
        $this->createMaterial([
            'judul' => 'Objek IPA dan Pengamatannya',
            'kategori' => 'ipa',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Mempelajari besaran, satuan, dan pengukuran dalam IPA.',
        ]);

        $this->createMaterial([
            'judul' => 'Klasifikasi Makhluk Hidup',
            'kategori' => 'ipa',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Memahami sistem klasifikasi makhluk hidup berdasarkan taksonomi.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem Organisasi Kehidupan',
            'kategori' => 'ipa',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Mempelajari sel, jaringan, organ, dan sistem organ.',
        ]);

        $this->createMaterial([
            'judul' => 'Getaran, Gelombang, dan Bunyi',
            'kategori' => 'ipa',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Memahami konsep getaran, gelombang, dan perambatan bunyi.',
        ]);

        $this->createMaterial([
            'judul' => 'Listrik Statis dan Dinamis',
            'kategori' => 'ipa',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Mempelajari muatan listrik, rangkaian listrik, dan hukum Ohm.',
        ]);

        $this->createMaterial([
            'judul' => 'Pewarisan Sifat (Genetika)',
            'kategori' => 'ipa',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Memahami pewarisan sifat dan hukum Mendel.',
        ]);

        // IPS
        $this->createMaterial([
            'judul' => 'Kondisi Geografis Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Mempelajari letak geografis dan kondisi alam Indonesia.',
        ]);

        $this->createMaterial([
            'judul' => 'Interaksi Sosial dan Lembaga Sosial',
            'kategori' => 'ips',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Memahami bentuk-bentuk interaksi sosial dan peran lembaga sosial.',
        ]);

        $this->createMaterial([
            'judul' => 'Masa Praaksara, Hindu-Buddha, dan Islam di Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Mempelajari sejarah Indonesia dari masa praaksara hingga masuknya Islam.',
        ]);

        $this->createMaterial([
            'judul' => 'Perdagangan Antardaerah dan Internasional',
            'kategori' => 'ips',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Memahami konsep perdagangan dan pengaruhnya terhadap ekonomi.',
        ]);

        $this->createMaterial([
            'judul' => 'Masa Penjajahan dan Pergerakan Kebangsaan Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Mempelajari masa penjajahan dan lahirnya gerakan kebangsaan.',
        ]);

        $this->createMaterial([
            'judul' => 'Perubahan Sosial Budaya dan Globalisasi',
            'kategori' => 'ips',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Memahami dampak globalisasi terhadap perubahan sosial budaya.',
        ]);

        // Bahasa Inggris
        $this->createMaterial([
            'judul' => "It's English Time! - Greetings and Expressions",
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Learning greetings, leave takings, and thanking expressions.',
        ]);

        $this->createMaterial([
            'judul' => 'Simple Present Tense for Daily Activities',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'smp',
            'kelas' => '7',
            'deskripsi' => 'Understanding and using simple present tense in daily context.',
        ]);

        $this->createMaterial([
            'judul' => 'Narrative Text - Fables and Legends',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'smp',
            'kelas' => '8',
            'deskripsi' => 'Reading and understanding narrative texts about fables and legends.',
        ]);

        $this->createMaterial([
            'judul' => 'Passive Voice and Degrees of Comparison',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'smp',
            'kelas' => '9',
            'deskripsi' => 'Learning passive voice structure and comparative degrees.',
        ]);
    }

    // ==================== FASE E ====================
    private function seedFaseE()
    {
        $this->command->info('Seeding FASE E materials...');

        // Matematika Wajib
        $this->createMaterial([
            'judul' => 'Eksponen dan Logaritma',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami sifat-sifat eksponen dan logaritma serta penerapannya.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem Persamaan Linear Tiga Variabel',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Menyelesaikan sistem persamaan linear tiga variabel.',
        ]);

        $this->createMaterial([
            'judul' => 'Fungsi Kuadrat dan Grafik',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Menganalisis fungsi kuadrat dan membuat grafiknya.',
        ]);

        $this->createMaterial([
            'judul' => 'Trigonometri Dasar',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Mempelajari perbandingan trigonometri dan identitas trigonometri.',
        ]);

        // Fisika
        $this->createMaterial([
            'judul' => 'Besaran dan Satuan',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami besaran pokok, besaran turunan, dan sistem satuan internasional.',
        ]);

        $this->createMaterial([
            'judul' => 'Gerak Lurus dan Gerak Parabola',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Menganalisis gerak lurus beraturan, GLBB, dan gerak parabola.',
        ]);

        $this->createMaterial([
            'judul' => 'Hukum Newton tentang Gerak',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami dan menerapkan hukum Newton I, II, dan III.',
        ]);

        // Kimia
        $this->createMaterial([
            'judul' => 'Struktur Atom dan Sistem Periodik',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Mempelajari perkembangan model atom dan tabel periodik unsur.',
        ]);

        $this->createMaterial([
            'judul' => 'Ikatan Kimia',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami ikatan ion, kovalen, dan logam.',
        ]);

        // Biologi
        $this->createMaterial([
            'judul' => 'Keanekaragaman Hayati',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Mempelajari tingkat keanekaragaman hayati dan upaya pelestariannya.',
        ]);

        $this->createMaterial([
            'judul' => 'Virus dan Bakteri',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami struktur, reproduksi, dan peranan virus dan bakteri.',
        ]);

        // Sejarah
        $this->createMaterial([
            'judul' => 'Manusia dan Sejarah',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami konsep dasar sejarah dan penelitian sejarah.',
        ]);

        $this->createMaterial([
            'judul' => 'Peradaban Awal Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Mempelajari peradaban awal Nusantara dari masa praaksara.',
        ]);

        // Geografi
        $this->createMaterial([
            'judul' => 'Pengetahuan Dasar Geografi',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami konsep geografi, pendekatan, dan prinsip geografi.',
        ]);

        $this->createMaterial([
            'judul' => 'Dinamika Litosfer dan Dampaknya',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Mempelajari struktur bumi, tektonisme, dan vulkanisme.',
        ]);

        // Ekonomi
        $this->createMaterial([
            'judul' => 'Ilmu Ekonomi dan Permasalahannya',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami konsep dasar ilmu ekonomi dan masalah ekonomi.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem Ekonomi',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Membandingkan sistem ekonomi tradisional, komando, pasar, dan campuran.',
        ]);

        // Sosiologi
        $this->createMaterial([
            'judul' => 'Fungsi dan Peran Sosiologi',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami sosiologi sebagai ilmu tentang masyarakat.',
        ]);

        $this->createMaterial([
            'judul' => 'Individu, Kelompok, dan Hubungan Sosial',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Menganalisis interaksi sosial dan pembentukan kelompok sosial.',
        ]);

        // Bahasa Indonesia
        $this->createMaterial([
            'judul' => 'Teks Laporan Hasil Observasi',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Menganalisis struktur dan kebahasaan teks laporan hasil observasi.',
        ]);

        $this->createMaterial([
            'judul' => 'Teks Eksposisi',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Memahami dan menyusun teks eksposisi dengan argumentasi yang kuat.',
        ]);

        $this->createMaterial([
            'judul' => 'Teks Anekdot',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Menganalisis teks anekdot dan strukturnya.',
        ]);

        // Bahasa Inggris
        $this->createMaterial([
            'judul' => 'Expressing Intentions and Offering Help',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Learning expressions for stating intentions and offering assistance.',
        ]);

        $this->createMaterial([
            'judul' => 'Announcement and News Item Text',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Understanding structure and language features of announcements and news.',
        ]);

        $this->createMaterial([
            'judul' => 'Recount Text - Personal Experience',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '10',
            'deskripsi' => 'Writing and analyzing recount texts about past experiences.',
        ]);
    }

    // ==================== FASE F ====================
    private function seedFaseF()
    {
        $this->command->info('Seeding FASE F materials...');

        // Matematika Wajib Kelas 11
        $this->createMaterial([
            'judul' => 'Program Linear',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Menyelesaikan masalah optimasi dengan program linear.',
        ]);

        $this->createMaterial([
            'judul' => 'Matriks dan Determinan',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Operasi matriks, determinan, dan invers matriks.',
        ]);

        $this->createMaterial([
            'judul' => 'Barisan dan Deret',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Barisan dan deret aritmatika serta geometri.',
        ]);

        $this->createMaterial([
            'judul' => 'Limit Fungsi',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Memahami konsep limit fungsi aljabar dan trigonometri.',
        ]);

        // Matematika Wajib Kelas 12
        $this->createMaterial([
            'judul' => 'Integral Tak Tentu dan Tentu',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Menghitung integral dan aplikasinya dalam geometri.',
        ]);

        $this->createMaterial([
            'judul' => 'Distribusi Peluang',
            'kategori' => 'matematika',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Distribusi binomial dan distribusi normal.',
        ]);

        // Fisika Kelas 11
        $this->createMaterial([
            'judul' => 'Elastisitas dan Hukum Hooke',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Memahami sifat elastisitas bahan dan hukum Hooke.',
        ]);

        $this->createMaterial([
            'judul' => 'Fluida Statis dan Dinamis',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Mempelajari tekanan hidrostatis dan hukum Pascal.',
        ]);

        $this->createMaterial([
            'judul' => 'Teori Kinetik Gas',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Memahami perilaku gas ideal berdasarkan teori kinetik.',
        ]);

        $this->createMaterial([
            'judul' => 'Termodinamika',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Hukum-hukum termodinamika dan penerapannya.',
        ]);

        // Fisika Kelas 12
        $this->createMaterial([
            'judul' => 'Listrik Arus Searah',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Rangkaian listrik arus searah dan hukum Kirchhoff.',
        ]);

        $this->createMaterial([
            'judul' => 'Medan Magnet dan Induksi Elektromagnetik',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Gaya Lorentz, induksi elektromagnetik, dan transformator.',
        ]);

        $this->createMaterial([
            'judul' => 'Gelombang Elektromagnetik',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Spektrum gelombang elektromagnetik dan aplikasinya.',
        ]);

        $this->createMaterial([
            'judul' => 'Fisika Modern - Teori Relativitas',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Pengenalan teori relativitas khusus Einstein.',
        ]);

        // Kimia Kelas 11
        $this->createMaterial([
            'judul' => 'Termokimia',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Perubahan entalpi pada reaksi kimia.',
        ]);

        $this->createMaterial([
            'judul' => 'Laju Reaksi',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Faktor-faktor yang mempengaruhi laju reaksi kimia.',
        ]);

        $this->createMaterial([
            'judul' => 'Kesetimbangan Kimia',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Kesetimbangan dinamis dan tetapan kesetimbangan.',
        ]);

        $this->createMaterial([
            'judul' => 'Larutan Asam Basa',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Teori asam basa dan pH larutan.',
        ]);

        // Kimia Kelas 12
        $this->createMaterial([
            'judul' => 'Sifat Koligatif Larutan',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Penurunan tekanan uap, kenaikan titik didih, dan penurunan titik beku.',
        ]);

        $this->createMaterial([
            'judul' => 'Reaksi Redoks dan Elektrokimia',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Sel volta, sel elektrolisis, dan korosi.',
        ]);

        $this->createMaterial([
            'judul' => 'Kimia Unsur',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Sifat dan kegunaan unsur-unsur periode ketiga.',
        ]);

        $this->createMaterial([
            'judul' => 'Kimia Organik - Hidrokarbon',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Alkana, alkena, alkuna, dan reaksi-reaksinya.',
        ]);

        // Biologi Kelas 11
        $this->createMaterial([
            'judul' => 'Sel sebagai Unit Kehidupan',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Struktur dan fungsi organel sel.',
        ]);

        $this->createMaterial([
            'judul' => 'Jaringan Tumbuhan dan Hewan',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Jenis-jenis jaringan pada tumbuhan dan hewan.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem Gerak pada Manusia',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Rangka, otot, dan mekanisme gerak.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem Peredaran Darah',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Struktur jantung, pembuluh darah, dan mekanisme peredaran darah.',
        ]);

        // Biologi Kelas 12
        $this->createMaterial([
            'judul' => 'Pertumbuhan dan Perkembangan',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Faktor-faktor yang mempengaruhi pertumbuhan makhluk hidup.',
        ]);

        $this->createMaterial([
            'judul' => 'Metabolisme Sel',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Katabolisme (respirasi) dan anabolisme (fotosintesis).',
        ]);

        $this->createMaterial([
            'judul' => 'Materi Genetik - DNA dan RNA',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Struktur DNA, RNA, dan sintesis protein.',
        ]);

        $this->createMaterial([
            'judul' => 'Evolusi dan Teori Asal Usul Kehidupan',
            'kategori' => 'ipa',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Teori evolusi Darwin dan mekanisme evolusi.',
        ]);

        // Sejarah Kelas 11
        $this->createMaterial([
            'judul' => 'Jalur dan Pengaruh Kedatangan Bangsa Barat',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Kedatangan Portugis, Spanyol, Belanda, dan Inggris ke Nusantara.',
        ]);

        $this->createMaterial([
            'judul' => 'Kolonialisme dan Imperialisme di Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Sistem tanam paksa dan Politik Etis.',
        ]);

        $this->createMaterial([
            'judul' => 'Pergerakan Nasional Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Lahirnya organisasi pergerakan nasional seperti Budi Utomo dan Sumpah Pemuda.',
        ]);

        // Sejarah Kelas 12
        $this->createMaterial([
            'judul' => 'Proklamasi Kemerdekaan Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Peristiwa sekitar proklamasi 17 Agustus 1945.',
        ]);

        $this->createMaterial([
            'judul' => 'Revolusi Nasional Indonesia 1945-1950',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Perjuangan mempertahankan kemerdekaan.',
        ]);

        $this->createMaterial([
            'judul' => 'Indonesia pada Masa Orde Baru dan Reformasi',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Perkembangan politik, ekonomi, dan sosial era Orde Baru hingga Reformasi.',
        ]);

        // Geografi Kelas 11
        $this->createMaterial([
            'judul' => 'Dinamika Atmosfer',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Lapisan atmosfer, cuaca, dan iklim.',
        ]);

        $this->createMaterial([
            'judul' => 'Dinamika Hidrosfer',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Siklus air, sungai, danau, dan laut.',
        ]);

        $this->createMaterial([
            'judul' => 'Sebaran Flora dan Fauna di Indonesia',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Persebaran flora dan fauna serta faktor-faktor yang mempengaruhinya.',
        ]);

        // Geografi Kelas 12
        $this->createMaterial([
            'judul' => 'Pola Keruangan Desa dan Kota',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Struktur dan pola keruangan desa serta kota.',
        ]);

        $this->createMaterial([
            'judul' => 'Pengelolaan Sumber Daya Alam',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Pemanfaatan dan pelestarian sumber daya alam.',
        ]);

        $this->createMaterial([
            'judul' => 'Mitigasi Bencana Alam',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Jenis-jenis bencana alam dan upaya mitigasinya.',
        ]);

        // Ekonomi Kelas 11
        $this->createMaterial([
            'judul' => 'Ketenagakerjaan',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Angkatan kerja, pengangguran, dan upaya meningkatkan kualitas tenaga kerja.',
        ]);

        $this->createMaterial([
            'judul' => 'Pendapatan Nasional',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Konsep dan metode penghitungan pendapatan nasional.',
        ]);

        $this->createMaterial([
            'judul' => 'APBN dan APBD',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Struktur dan fungsi APBN serta APBD.',
        ]);

        // Ekonomi Kelas 12
        $this->createMaterial([
            'judul' => 'Akuntansi sebagai Sistem Informasi',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Siklus akuntansi perusahaan jasa.',
        ]);

        $this->createMaterial([
            'judul' => 'Manajemen',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Fungsi manajemen dalam organisasi.',
        ]);

        $this->createMaterial([
            'judul' => 'Koperasi dan Kewirausahaan',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Prinsip koperasi dan sikap wirausaha.',
        ]);

        // Sosiologi Kelas 11
        $this->createMaterial([
            'judul' => 'Pembentukan Kelompok Sosial',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Jenis-jenis kelompok sosial dan dinamikanya.',
        ]);

        $this->createMaterial([
            'judul' => 'Konflik Sosial',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Penyebab konflik sosial dan cara pengendaliannya.',
        ]);

        $this->createMaterial([
            'judul' => 'Integrasi Sosial',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Proses integrasi dan faktor-faktor yang mempengaruhinya.',
        ]);

        // Sosiologi Kelas 12
        $this->createMaterial([
            'judul' => 'Perubahan Sosial',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Bentuk-bentuk dan faktor penyebab perubahan sosial.',
        ]);

        $this->createMaterial([
            'judul' => 'Globalisasi dan Dampaknya',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Pengaruh globalisasi terhadap kehidupan masyarakat.',
        ]);

        $this->createMaterial([
            'judul' => 'Penelitian Sosial',
            'kategori' => 'ips',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Metode penelitian kualitatif dan kuantitatif.',
        ]);

        // Bahasa Indonesia Kelas 11
        $this->createMaterial([
            'judul' => 'Teks Prosedur Kompleks',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Menganalisis dan menyusun teks prosedur yang kompleks.',
        ]);

        $this->createMaterial([
            'judul' => 'Teks Eksplanasi',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Menganalisis fenomena alam dan sosial dalam teks eksplanasi.',
        ]);

        $this->createMaterial([
            'judul' => 'Ceramah dan Pidato',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Menganalisis isi dan struktur ceramah serta pidato.',
        ]);

        // Bahasa Indonesia Kelas 12
        $this->createMaterial([
            'judul' => 'Surat Lamaran Pekerjaan',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Menyusun surat lamaran pekerjaan yang efektif.',
        ]);

        $this->createMaterial([
            'judul' => 'Teks Editorial',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Menganalisis opini dan argumentasi dalam teks editorial.',
        ]);

        $this->createMaterial([
            'judul' => 'Karya Ilmiah',
            'kategori' => 'bahasa_indonesia',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Menyusun karya ilmiah sederhana dengan sistematika yang benar.',
        ]);

        // Bahasa Inggris Kelas 11
        $this->createMaterial([
            'judul' => 'Expressing Opinions and Thoughts',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Learning how to express opinions and respond to others.',
        ]);

        $this->createMaterial([
            'judul' => 'Analytical Exposition Text',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Understanding argumentative texts and persuasive language.',
        ]);

        $this->createMaterial([
            'judul' => 'Explanation Text - Natural Phenomena',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Explaining how and why natural phenomena occur.',
        ]);

        // Bahasa Inggris Kelas 12
        $this->createMaterial([
            'judul' => 'Application Letter and CV',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Writing professional application letters and curriculum vitae.',
        ]);

        $this->createMaterial([
            'judul' => 'Hortatory Exposition',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Persuasive texts with recommendations.',
        ]);

        $this->createMaterial([
            'judul' => 'News Item Text',
            'kategori' => 'bahasa_inggris',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Understanding structure and features of news articles.',
        ]);

        // PKN Kelas 11-12
        $this->createMaterial([
            'judul' => 'Hak Asasi Manusia (HAM)',
            'kategori' => 'agama',
            'tingkat' => 'sma',
            'kelas' => '11',
            'deskripsi' => 'Memahami konsep HAM dan implementasinya di Indonesia.',
        ]);

        $this->createMaterial([
            'judul' => 'Sistem dan Dinamika Demokrasi Pancasila',
            'kategori' => 'agama',
            'tingkat' => 'sma',
            'kelas' => '12',
            'deskripsi' => 'Menganalisis sistem politik dan pemerintahan Indonesia.',
        ]);
    }

    // ==================== HELPER METHOD ====================
    private function createMaterial(array $data)
    {
        $defaultData = [
            'status' => 'published',
            'akses' => 'public',
            'created_by' => $this->adminUser->id,
            'penerbit' => 'Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi',
            'tahun_terbit' => date('Y'),
            'total_halaman' => rand(10, 50),
        ];

        $materialData = array_merge($defaultData, $data);

        // Create JSON content for the material
        $jsonContent = $this->generateMaterialJsonContent($materialData);
        
        // Save JSON file
        $jsonPath = 'materials/json/seed_' . uniqid() . '.json';
        Storage::disk('private')->put(
            $jsonPath,
            json_encode($jsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // Create material record
        $material = Material::create(array_merge($materialData, [
            'file_path' => $jsonPath
        ]));

        // Generate and save Braille data
        $this->generateBrailleForMaterial($material, $jsonContent);

        $this->command->line("  ✓ Created: {$materialData['judul']} ({$materialData['tingkat']} - Kelas {$materialData['kelas']})");

        return $material;
    }

    private function generateMaterialJsonContent(array $data)
    {
        $pages = [];
        $pageCount = $data['total_halaman'];

        for ($i = 1; $i <= $pageCount; $i++) {
            $lines = $this->generatePageLines($data, $i, $pageCount);
            $pages[] = [
                'page' => $i,
                'lines' => $lines
            ];
        }

        return [
            'judul' => $data['judul'],
            'penerbit' => $data['penerbit'],
            'tahun' => $data['tahun_terbit'],
            'edisi' => $data['edisi'] ?? 'Edisi 1',
            'kategori' => $data['kategori'],
            'tingkat' => $data['tingkat'],
            'kelas' => $data['kelas'],
            'pages' => $pages,
            'processing_method' => 'seeder_generated'
        ];
    }

    private function generatePageLines(array $data, int $pageNumber, int $totalPages)
    {
        $lines = [];
        $lineNumber = 1;

        // Title page
        if ($pageNumber === 1) {
            $lines[] = [
                'line' => $lineNumber++,
                'text' => strtoupper($data['judul'])
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => ''
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => 'Kategori: ' . ucfirst(str_replace('_', ' ', $data['kategori']))
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => 'Tingkat: ' . strtoupper($data['tingkat']) . ' - Kelas ' . $data['kelas']
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => ''
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => 'Penerbit: ' . $data['penerbit']
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => 'Tahun: ' . $data['tahun_terbit']
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => ''
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => 'DESKRIPSI'
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => $data['deskripsi']
            ];

        } else {
            // Content pages
            $lines[] = [
                'line' => $lineNumber++,
                'text' => 'BAB ' . ($pageNumber - 1) . ' - ' . $data['judul']
            ];
            $lines[] = [
                'line' => $lineNumber++,
                'text' => ''
            ];

            // Generate sample content based on category
            $content = $this->generateContentByCategory($data['kategori'], $data['tingkat']);
            
            foreach ($content as $paragraph) {
                $lines[] = [
                    'line' => $lineNumber++,
                    'text' => $paragraph
                ];
                $lines[] = [
                    'line' => $lineNumber++,
                    'text' => ''
                ];
            }

            // Add practice questions for some pages
            if ($pageNumber > 2 && $pageNumber % 3 === 0) {
                $lines[] = [
                    'line' => $lineNumber++,
                    'text' => 'LATIHAN SOAL'
                ];
                $lines[] = [
                    'line' => $lineNumber++,
                    'text' => ''
                ];
                
                for ($q = 1; $q <= 3; $q++) {
                    $lines[] = [
                        'line' => $lineNumber++,
                        'text' => "$q. " . $this->generateSampleQuestion($data['kategori'], $q)
                    ];
                }
            }
        }

        // Footer
        $lines[] = [
            'line' => $lineNumber++,
            'text' => ''
        ];
        $lines[] = [
            'line' => $lineNumber++,
            'text' => '--- Halaman ' . $pageNumber . ' dari ' . $totalPages . ' ---'
        ];

        return $lines;
    }

    private function generateContentByCategory(string $kategori, string $tingkat)
    {
        $content = [];

        switch ($kategori) {
            case 'matematika':
                $content[] = 'Matematika adalah ilmu yang mempelajari pola, struktur, dan hubungan logis. Dalam materi ini, kita akan memahami konsep-konsep dasar yang menjadi fondasi pemahaman matematika.';
                $content[] = 'Contoh penerapan dalam kehidupan sehari-hari: menghitung belanja di pasar, mengukur luas tanah, atau menentukan kecepatan kendaraan.';
                if ($tingkat === 'smp' || $tingkat === 'sma') {
                    $content[] = 'Rumus dasar: a + b = c, dimana a dan b adalah bilangan yang dijumlahkan, dan c adalah hasilnya.';
                }
                break;

            case 'ipa':
                $content[] = 'Ilmu Pengetahuan Alam mempelajari fenomena alam berdasarkan metode ilmiah. Observasi, eksperimen, dan analisis data adalah kunci dalam memahami IPA.';
                $content[] = 'Melalui praktikum dan pengamatan langsung, siswa dapat memahami prinsip-prinsip sains yang terjadi di alam semesta.';
                if ($tingkat === 'sma') {
                    $content[] = 'Metode ilmiah meliputi: observasi, hipotesis, eksperimen, analisis data, dan kesimpulan.';
                }
                break;

            case 'bahasa_indonesia':
                $content[] = 'Bahasa Indonesia adalah bahasa persatuan yang kaya akan struktur dan kaidah kebahasaan. Menguasai bahasa Indonesia dengan baik akan memudahkan komunikasi dalam berbagai situasi.';
                $content[] = 'Dalam mempelajari bahasa Indonesia, kita mengenal berbagai jenis teks seperti narasi, deskripsi, eksposisi, dan argumentasi.';
                $content[] = 'Keterampilan berbahasa meliputi menyimak, berbicara, membaca, dan menulis yang harus dikuasai secara seimbang.';
                break;

            case 'ips':
                $content[] = 'Ilmu Pengetahuan Sosial mengkaji interaksi manusia dengan lingkungan sosial, ekonomi, dan budayanya. IPS membantu kita memahami dinamika masyarakat.';
                $content[] = 'Indonesia memiliki keragaman suku, budaya, dan adat istiadat yang menjadi kekayaan bangsa. Memahami keberagaman ini penting untuk menjaga persatuan.';
                if ($tingkat === 'smp' || $tingkat === 'sma') {
                    $content[] = 'Faktor geografis, historis, dan ekonomi sangat mempengaruhi perkembangan kehidupan sosial masyarakat.';
                }
                break;

            case 'bahasa_inggris':
                $content[] = 'English is an international language used for global communication. Learning English opens opportunities for education, career, and cultural exchange.';
                $content[] = 'In this material, we will learn grammar, vocabulary, and communication skills through various text types and activities.';
                $content[] = 'Practice makes perfect. Regular reading, listening, speaking, and writing in English will improve your language proficiency.';
                break;

            case 'agama':
                $content[] = 'Pendidikan Pancasila dan Kewarganegaraan membentuk karakter siswa sebagai warga negara yang baik. Nilai-nilai Pancasila menjadi pedoman dalam kehidupan berbangsa.';
                $content[] = 'Setiap sila dalam Pancasila memiliki makna mendalam yang harus dipahami dan diamalkan dalam kehidupan sehari-hari.';
                $content[] = 'Dengan memahami hak dan kewajiban sebagai warga negara, kita dapat berkontribusi positif bagi kemajuan bangsa.';
                break;

            default:
                $content[] = 'Materi pembelajaran ini dirancang untuk memberikan pemahaman komprehensif kepada siswa tentang topik yang dipelajari.';
                $content[] = 'Melalui pendekatan sistematis dan contoh-contoh konkret, siswa diharapkan dapat menguasai kompetensi yang ditargetkan.';
                $content[] = 'Pembelajaran aktif dan partisipatif akan meningkatkan pemahaman dan retensi materi.';
        }

        return $content;
    }

    private function generateSampleQuestion(string $kategori, int $number)
    {
        $questions = [
            'matematika' => [
                'Hitunglah hasil dari operasi berikut dengan langkah yang tepat!',
                'Tentukan nilai x dari persamaan yang diberikan!',
                'Selesaikan soal cerita berikut dengan menggunakan rumus yang sesuai!'
            ],
            'ipa' => [
                'Jelaskan proses yang terjadi berdasarkan teori yang telah dipelajari!',
                'Apa yang dimaksud dengan konsep ini dan berikan contohnya!',
                'Bandingkan dan kontraskan dua fenomena yang disebutkan!'
            ],
            'bahasa_indonesia' => [
                'Tentukan ide pokok paragraf tersebut!',
                'Buatlah kalimat efektif dari kata-kata yang disediakan!',
                'Analisis struktur teks tersebut!'
            ],
            'ips' => [
                'Jelaskan faktor-faktor yang mempengaruhi peristiwa tersebut!',
                'Bagaimana dampak dari fenomena ini terhadap kehidupan masyarakat?',
                'Bandingkan kondisi geografis dua wilayah yang disebutkan!'
            ],
            'bahasa_inggris' => [
                'Translate the following sentence into proper English!',
                'Complete the dialogue with appropriate expressions!',
                'Identify the main idea of the text above!'
            ],
            'agama' => [
                'Jelaskan makna dari sila yang disebutkan!',
                'Berikan contoh penerapan nilai tersebut dalam kehidupan sehari-hari!',
                'Bagaimana hubungan antara konsep ini dengan kehidupan berbangsa?'
            ]
        ];

        $categoryQuestions = $questions[$kategori] ?? [
            'Jelaskan konsep utama dari materi ini!',
            'Berikan contoh penerapan dalam kehidupan nyata!',
            'Apa kesimpulan yang dapat diambil dari pembahasan ini?'
        ];

        return $categoryQuestions[($number - 1) % count($categoryQuestions)];
    }

    private function generateBrailleForMaterial($material, $jsonContent)
    {
        try {
            $brailleConverter = new \App\Services\BrailleConverter();
            
            $brailleData = [
                'judul' => $brailleConverter->toBraille($jsonContent['judul'] ?? ''),
                'penerbit' => $brailleConverter->toBraille($jsonContent['penerbit'] ?? ''),
                'tahun' => $brailleConverter->toBraille((string)($jsonContent['tahun'] ?? '')),
                'edisi' => $brailleConverter->toBraille($jsonContent['edisi'] ?? ''),
                'pages' => []
            ];

            foreach ($jsonContent['pages'] as $pageData) {
                $braillePage = [
                    'page' => $pageData['page'],
                    'lines' => []
                ];

                foreach ($pageData['lines'] as $line) {
                    if (!empty(trim($line['text']))) {
                        $originalText = trim($line['text']);
                        $brailleText = $brailleConverter->toBraille($originalText);
                        $decimalValues = $this->convertBrailleToDecimal($brailleText);

                        $braillePage['lines'][] = [
                            'line' => $line['line'],
                            'text' => $brailleText,
                            'original_text' => $originalText,
                            'decimal_values' => $decimalValues,
                            'decimal' => implode('', $decimalValues)
                        ];
                    }
                }

                $brailleData['pages'][] = $braillePage;
            }

            // Save Braille file
            $braillePath = 'materials/braille/seed_' . uniqid() . '.json';
            Storage::disk('private')->put(
                $braillePath,
                json_encode($brailleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $material->update(['braille_data_path' => $braillePath]);

        } catch (\Exception $e) {
            $this->command->warn("  ⚠ Braille generation failed for: {$material->judul}");
            $this->command->warn("    Error: " . $e->getMessage());
        }
    }

    private function convertBrailleToDecimal(string $brailleText)
    {
        $decimalValues = [];
        $length = mb_strlen($brailleText, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($brailleText, $i, 1, 'UTF-8');
            $decimal = $this->brailleCharToDecimal($char);
            $decimalValues[] = str_pad((string)$decimal, 2, '0', STR_PAD_LEFT);
        }

        return $decimalValues;
    }

    private function brailleCharToDecimal(string $brailleChar): int
    {
        $codePoint = $this->getCodePoint($brailleChar);

        if ($codePoint === null || $codePoint < 0x2800 || $codePoint > 0x28FF) {
            return 0;
        }

        $mask = $codePoint - 0x2800;
        return $mask & 0b00111111;
    }

    private function getCodePoint(string $char): ?int
    {
        if ($char === '' || $char === ' ') {
            return 0x2800;
        }

        $encoded = mb_convert_encoding($char, 'UCS-4BE', 'UTF-8');
        if ($encoded === false) {
            return null;
        }

        $codePoint = unpack('N', $encoded);
        return $codePoint[1] ?? null;
    }
}