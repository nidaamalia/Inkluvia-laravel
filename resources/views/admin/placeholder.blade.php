@extends('admin.layout')

@section('title', $title ?? 'Halaman')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title ?? 'Halaman' }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $description ?? 'Deskripsi halaman' }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Coming Soon Content -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-tools text-3xl text-primary"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Sedang Dalam Pengembangan</h2>
            <p class="text-gray-600 mb-8">
                Fitur <strong>{{ $title ?? 'ini' }}</strong> sedang dalam tahap pengembangan dan akan segera hadir. 
                Kami bekerja keras untuk memberikan pengalaman terbaik untuk Anda.
            </p>
            
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Yang Akan Hadir:</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        @if(isset($features))
                            @foreach($features as $feature)
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        @else
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Interface yang user-friendly
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Fitur pencarian dan filter canggih
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Manajemen data yang komprehensif
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Laporan dan analitik
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('dashboard') }}" class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium text-center">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex-1 border border-primary text-primary py-2 px-4 rounded-lg hover:bg-primary hover:text-white transition-colors font-medium text-center">
                        <i class="fas fa-users mr-2"></i>
                        Kelola Pengguna
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Roadmap Pengembangan</h3>
        <div class="space-y-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Dashboard & Manajemen Pengguna</h4>
                    <p class="text-sm text-gray-600">Sistem dasar untuk mengelola pengguna dan lembaga</p>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mt-1">
                        Selesai
                    </span>
                </div>
            </div>
            
            <div class="flex items-start">
                <div class="flex-shrink-0 w-3 h-3 bg-yellow-500 rounded-full mt-2"></div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">{{ $title ?? 'Fitur Ini' }}</h4>
                    <p class="text-sm text-gray-600">{{ $description ?? 'Fitur yang sedang Anda akses' }}</p>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-1">
                        Dalam Pengembangan
                    </span>
                </div>
            </div>
            
            <div class="flex items-start">
                <div class="flex-shrink-0 w-3 h-3 bg-gray-300 rounded-full mt-2"></div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Fitur Pembelajaran</h4>
                    <p class="text-sm text-gray-600">Sistem pembelajaran dan konten untuk pengguna</p>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mt-1">
                        Akan Datang
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection