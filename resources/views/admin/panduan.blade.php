@extends('layouts.admin')

@section('title', 'Panduan Admin')

@section('content')
<main class="space-y-8" aria-labelledby="panduan-admin-title">
    <header class="flex items-center justify-between">
        <div>
            <h1 id="panduan-admin-title" class="text-3xl font-bold text-gray-900">Panduan Admin</h1>
            <p class="mt-2 text-gray-600">Ikuti panduan berikut untuk mengelola platform Inkluvia secara efektif.</p>
        </div>
    </header>

    <section aria-label="Pengelolaan Pengguna" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Manajemen Pengguna</h2>
        <div class="space-y-2 text-gray-700">
            <p>Gunakan menu <strong>Manajemen Pengguna</strong> untuk membuat, memperbarui, atau menonaktifkan akun.</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Pantau status pengguna dan peran yang dimiliki.</li>
                <li>Gunakan fitur pencarian untuk menemukan akun dengan cepat.</li>
                <li>Pastikan informasi lembaga dan perangkat terhubung dengan benar.</li>
            </ul>
        </div>
    </section>

    <section aria-label="Manajemen Materi" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Mengelola Materi</h2>
        <div class="space-y-2 text-gray-700">
            <p>Menu <strong>Manajemen Materi</strong> membantu memvalidasi dan memublikasikan materi braille.</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Tinjau materi baru untuk memastikan kualitas dan aksesibilitas.</li>
                <li>Gunakan aksi <strong>Generate Braille</strong> bila diperlukan pembaruan konten.</li>
                <li>Setel status materi sesuai tahap persetujuan sebelum dirilis.</li>
            </ol>
        </div>
    </section>

    <section aria-label="Perangkat" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Mengelola Perangkat</h2>
        <div class="space-y-2 text-gray-700">
            <p>Pastikan setiap perangkat aktif dan terhubung dengan benar kepada pengguna atau lembaga.</p>
            <p class="text-sm text-gray-500">Tip: gunakan tombol <strong>Ping</strong> untuk memastikan perangkat masih responsif.</p>
        </div>
    </section>

    <section aria-label="Pengaturan" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-semibold text-primary">Pengaturan Sistem</h2>
        <div class="space-y-2 text-gray-700">
            <p>Gunakan menu <strong>Pengaturan</strong> untuk memperbarui profil admin dan mengganti kata sandi.</p>
            <p>Pastikan informasi kontak selalu diperbarui untuk komunikasi internal.</p>
        </div>
    </section>
</main>
@endsection
