@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
    <p class="text-gray-600 mt-1">Kelola platform pembelajaran inklusif Inkluvia</p>
</div>

<!-- Welcome Card -->
<div class="bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl shadow p-6 mb-8">
    <h2 class="text-xl font-semibold">Selamat Datang, {{ Auth::user()->nama_lengkap }}!</h2>
    <p class="mt-2 text-sm opacity-90">
        Kelola dan pantau seluruh aktivitas platform pembelajaran inklusif Inkluvia
    </p>
</div>

<!-- Stats Grid -->
<div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white p-5 rounded-lg shadow flex items-center space-x-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-primary text-white">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</div>
            <div class="text-sm text-gray-600">Total Pengguna</div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center space-x-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white">
            <i class="fas fa-building"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_lembagas'] }}</div>
            <div class="text-sm text-gray-600">Total Lembaga</div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center space-x-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-500 text-white">
            <i class="fas fa-laptop"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['active_devices'] }}</div>
            <div class="text-sm text-gray-600">Perangkat Aktif</div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow flex items-center space-x-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-500 text-white">
            <i class="fas fa-book"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_materials'] }}</div>
            <div class="text-sm text-gray-600">Total Materi</div>
        </div>
    </div>
</div>

<!-- Admin Actions -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Panel Administrasi</h3>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.kelola-pengguna') }}" class="flex flex-col items-center justify-center bg-primary text-white p-6 rounded-lg shadow hover:bg-primary-dark transition">
            <i class="fas fa-users text-3xl mb-2"></i>
            Manajemen Pengguna
        </a>
        <a href="{{ route('admin.manajemen-lembaga') }}" class="flex flex-col items-center justify-center bg-indigo-500 text-white p-6 rounded-lg shadow hover:bg-indigo-600 transition">
            <i class="fas fa-building text-3xl mb-2"></i>
            Manajemen Lembaga
        </a>
        <a href="{{ route('admin.kelola-perangkat') }}" class="flex flex-col items-center justify-center bg-gray-400 text-white p-6 rounded-lg shadow hover:bg-gray-600 transition">
            <i class="fas fa-laptop text-3xl mb-2"></i>
            Manajemen Perangkat
        </button>
        <a href="{{ route('admin.manajemen-materi') }}" class="flex flex-col items-center justify-center bg-teal-500 text-white p-6 rounded-lg shadow hover:bg-teal-600 transition">
            <i class="fas fa-braille text-3xl mb-2"></i>
            Manajemen Materi
        </a>
    </div>
</div>

<!-- User Statistics -->
@if(count($stats['users_by_role']) > 0)
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Pengguna</h3>
    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
        @foreach($stats['users_by_role'] as $role => $count)
        <div class="text-center p-4 bg-gray-50 rounded-lg">
            <div class="text-2xl font-bold text-primary">{{ $count }}</div>
            <div class="text-sm text-gray-600 capitalize">{{ $role }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Users -->
@if($stats['recent_users']->count() > 0)
<div class="bg-white rounded-lg shadow p-6 mb-8 overflow-x-auto">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengguna Terbaru</h3>
    <table class="min-w-full text-sm text-left text-gray-700 border">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Lembaga</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['recent_users'] as $user)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ $user->nama_lengkap }}</td>
                <td class="px-4 py-2">{{ $user->email }}</td>
                <td class="px-4 py-2">{{ $user->lembaga->nama ?? '-' }}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded-lg {{ $user->role === 'admin' ? 'bg-primary text-white' : 'bg-green-500 text-white' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-4 py-2">{{ $user->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
    <div class="text-center py-8 text-gray-500">
        <i class="fas fa-inbox text-4xl mb-3 opacity-40"></i>
        <p>Belum ada aktivitas terbaru</p>
    </div>
</div>
@endsection
