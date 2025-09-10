@extends('admin.layout')

@section('title', 'Detail Lembaga')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-secondary rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $lembaga->nama }}</h1>
                    <p class="text-sm text-gray-600">Detail informasi lembaga</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.lembagas.edit', $lembaga) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.lembagas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Lembaga Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Lembaga</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lembaga</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $lembaga->nama }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Lembaga</label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $lembaga->type === 'Sekolah' ? 'bg-blue-100 text-blue-800' :
                                   ($lembaga->type === 'Universitas' ? 'bg-purple-100 text-purple-800' :
                                   ($lembaga->type === 'Lembaga Kursus' ? 'bg-green-100 text-green-800' :
                                   ($lembaga->type === 'Organisasi' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ $lembaga->type }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-2"></i>
                                <p class="text-gray-900 flex-1">{{ $lembaga->alamat }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Daftar</label>
                                <p class="text-gray-900">{{ $lembaga->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Update</label>
                                <p class="text-gray-900">{{ $lembaga->updated_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users List -->
            <div class="bg-white rounded-lg shadow-sm mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Pengguna Terdaftar</h3>
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $lembaga->users->count() }} Pengguna
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    @if($lembaga->users->count() > 0)
                        <div class="space-y-4">
                            @foreach($lembaga->users as $user)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-semibold text-sm">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                               ($user->role === 'guru' ? 'bg-blue-100 text-blue-800' : 
                                               ($user->role === 'content_manager' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        @if($user->email_verified_at)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-primary hover:text-primary-700">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.users.index', ['lembaga_id' => $lembaga->id]) }}" class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors font-medium text-center block">
                                <i class="fas fa-users mr-2"></i>
                                Lihat Semua Pengguna dari {{ $lembaga->nama }}
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl mb-4 opacity-30"></i>
                            <p class="text-lg font-medium">Belum ada pengguna terdaftar</p>
                            <p class="text-sm">Lembaga ini belum memiliki pengguna yang terdaftar</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Pengguna Pertama
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary">{{ $lembaga->users->count() }}</div>
                        <div class="text-sm text-gray-500">Total Pengguna</div>
                    </div>
                    
                    @if($lembaga->users->count() > 0)
                        <div class="space-y-3">
                            @php
                                $roleStats = $lembaga->users->groupBy('role')->map->count();
                                $activeUsers = $lembaga->users->whereNotNull('email_verified_at')->count();
                            @endphp
                            
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Pengguna Aktif</span>
                                <span class="text-sm font-medium text-green-600">{{ $activeUsers }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Tidak Aktif</span>
                                <span class="text-sm font-medium text-red-600">{{ $lembaga->users->count() - $activeUsers }}</span>
                            </div>
                            
                            @foreach($roleStats as $role => $count)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ ucfirst($role) }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="pt-4 border-t border-gray-100">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-900">
                                {{ $lembaga->created_at->diffInDays(now()) }}
                            </div>
                            <div class="text-sm text-gray-500">Hari sejak terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.lembagas.edit', $lembaga) }}" class="w-full bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium text-center block">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Lembaga
                    </a>
                    
                    <a href="{{ route('admin.users.create') }}?lembaga_id={{ $lembaga->id }}" class="w-full border border-primary text-primary py-2 px-4 rounded-lg hover:bg-primary hover:text-white transition-colors font-medium text-center block">
                        <i class="fas fa-user-plus mr-2"></i>
                        Tambah Pengguna
                    </a>
                    
                    @if($lembaga->users->count() === 0)
                        <button onclick="confirmDelete()" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Lembaga
                        </button>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak dapat menghapus lembaga yang memiliki pengguna
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information (Future Feature) -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p>
                        <i class="fas fa-phone mr-2"></i>
                        Fitur kontak akan segera hadir
                    </p>
                    <p>
                        <i class="fas fa-envelope mr-2"></i>
                        Email lembaga akan ditambahkan
                    </p>
                    <p>
                        <i class="fas fa-globe mr-2"></i>
                        Website resmi akan ditambahkan
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    @if($lembaga->users->count() === 0)
        <form id="delete-form" method="POST" action="{{ route('admin.lembagas.destroy', $lembaga) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus lembaga ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection