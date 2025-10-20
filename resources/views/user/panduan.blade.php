@extends('layouts.user')

@section('title', 'Panduan Pengguna')

@section('content')
<main class="space-y-8" aria-labelledby="panduan-title">
    <header class="flex items-center justify-between">
        <div>
            <h1 id="panduan-title" class="text-3xl font-bold text-gray-900">Panduan Pengguna</h1>
            <p class="mt-2 text-gray-600">Pelajari cara menggunakan fitur-fitur utama platform Inkluvia.</p>
        </div>
    </header>

    <section aria-label="Panduan Memulai" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Memulai Pembelajaran</h2>
        <div class="space-y-2 text-gray-700">
            <p>Ikuti langkah berikut untuk memulai sesi belajar pertama Anda:</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Buka menu <strong>Jadwal Sesi Belajar</strong> untuk membuat atau memilih jadwal.</li>
                <li>Pilih materi yang ingin dipelajari lalu kirimkan ke perangkat braille.</li>
                <li>Gunakan tombol navigasi untuk berpindah halaman selama sesi belajar.</li>
            </ol>
        </div>
    </section>

    <section aria-label="Panduan Materi" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Mengelola Materi</h2>
        <div class="space-y-2 text-gray-700">
            <p>Menu <strong>Materi Saya</strong> memungkinkan Anda membuat, mengedit, dan mengunduh materi.</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Gunakan tombol <strong>Buat Materi</strong> untuk menambahkan materi baru.</li>
                <li>Pilih salah satu materi untuk melihat pratinjau dalam format braille.</li>
                <li>Tekan tombol <strong>Kirim ke Perangkat</strong> untuk mengirim materi ke perangkat yang dipilih.</li>
            </ul>
        </div>
    </section>

    <section aria-label="Panduan Perpustakaan" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Memanfaatkan Perpustakaan</h2>
        <div class="space-y-2 text-gray-700">
            <p>Temukan materi publik pada menu <strong>Perpustakaan</strong> dan simpan sebagai materi favorit.</p>
            <p class="text-sm text-gray-500">Tip: gunakan filter pencarian untuk menemukan materi sesuai kebutuhan Anda.</p>
        </div>
    </section>
</main>
@endsection
