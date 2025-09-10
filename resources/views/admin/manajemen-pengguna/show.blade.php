@extends('admin.layout')

@section('title', 'Detail Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl">
                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->nama_lengkap }}</h1>
                    <p class="text-sm text-gray-600">Detail informasi pengguna</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- User Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Personal</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $user->nama_lengkap }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <p class="text-gray-900">{{ $user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <p class="text-gray-900">
                                {{ $user->tanggal_lahir ? $user->tanggal_lahir->format('d F Y') : '-' }}
                                @if($user->tanggal_lahir)
                                    <span class="text-sm text-gray-500">({{ $user->tanggal_lahir->age }} tahun)</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <p class="text-gray-900">{{ $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                   ($user->role === 'guru' ? 'bg-blue-100 text-blue-800' : 
                                   ($user->role === 'content_manager' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lembaga Information -->
            @if($user->lembaga)
            <div class="bg-white rounded-lg shadow-sm mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Lembaga</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $user->lembaga->nama }}</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $user->lembaga->type === 'Sekolah' ? 'bg-blue-100 text-blue-800' :
                                   ($user->lembaga->type === 'Universitas' ? 'bg-purple-100 text-purple-800' :
                                   ($user->lembaga->type === 'Lembaga Kursus' ? 'bg-green-100 text-green-800' :
                                   ($user->lembaga->type === 'Organisasi' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ $user->lembaga->type }}
                            </span>
                            <p class="text-gray-600 mt-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $user->lembaga->alamat }}
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('admin.lembagas.show', $user->lembaga) }}" class="text-primary hover:text-primary-700 text-sm font-medium">
                                    Lihat Detail Lembaga <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Account Status & Actions -->
        <div class="space-y-6">
            <!-- Account Status -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Status Akun</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i>
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Tanggal Daftar</span>
                        <span class="text-sm text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Terakhir Update</span>
                        <span class="text-sm text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</span>
                    </div>
                    
                    @if($user->email_verified_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Verifikasi Email</span>
                        <span class="text-sm text-gray-900">{{ $user->email_verified_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="w-full bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium text-center block">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Pengguna
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full border border-primary text-primary py-2 px-4 rounded-lg hover:bg-primary hover:text-white transition-colors font-medium">
                                <i class="fas fa-{{ $user->email_verified_at ? 'user-slash' : 'user-check' }} mr-2"></i>
                                {{ $user->email_verified_at ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        
                        <button onclick="confirmDelete()" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Pengguna
                        </button>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Ini adalah akun Anda sendiri
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Statistics -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">
                            {{ $user->created_at->diffInDays(now()) }}
                        </div>
                        <div class="text-sm text-gray-500">Hari sejak bergabung</div>
                    </div>
                    
                    <!-- Future: Add more statistics like login count, activity, etc. -->
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-chart-line mr-1"></i>
                            Statistik detail akan segera hadir
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    @if($user->id !== auth()->id())
        <form id="delete-form" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection