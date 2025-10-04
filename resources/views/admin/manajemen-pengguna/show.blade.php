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
                    <p class="text-sm text-gray-500">Detail informasi pengguna</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- User Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Personal</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-detail label="Nama Lengkap" :value="$user->nama_lengkap" />
                    <x-detail label="Username" :value="$user->name" />
                    <x-detail label="Email" :value="$user->email" />
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <p class="text-gray-900">
                            {{ $user->tanggal_lahir ? $user->tanggal_lahir->format('d F Y') : '-' }}
                            @if($user->tanggal_lahir)
                                <span class="text-sm text-gray-500">({{ $user->tanggal_lahir->age }} tahun)</span>
                            @endif
                        </p>
                    </div>

                    <x-detail label="Jenis Kelamin" :value="$user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                               ($user->role === 'guru' ? 'bg-blue-100 text-blue-800' :
                               ($user->role === 'content_manager' ? 'bg-yellow-100 text-yellow-800' :
                               'bg-green-100 text-green-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Lembaga Information -->
            @if($user->lembaga)
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Lembaga</h3>
                </div>
                <div class="p-6 flex items-start space-x-4">
                    <div class="w-12 h-12 bg-secondary flex items-center justify-center rounded-lg">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $user->lembaga->nama }}</h4>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $user->lembaga->type === 'Sekolah' ? 'bg-blue-100 text-blue-800' :
                               ($user->lembaga->type === 'Universitas' ? 'bg-purple-100 text-purple-800' :
                               ($user->lembaga->type === 'Lembaga Kursus' ? 'bg-green-100 text-green-800' :
                               'bg-yellow-100 text-yellow-800')) }}">
                            {{ $user->lembaga->type }}
                        </span>
                        <p class="text-gray-600 mt-2">
                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $user->lembaga->alamat }}
                        </p>
                        <a href="{{ route('admin.lembagas.show', $user->lembaga) }}" class="mt-2 inline-block text-primary hover:text-primary-dark text-sm font-medium">
                            Lihat Detail Lembaga <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Sidebar (Status & Actions) -->
        <div class="space-y-6">
            <!-- Account Status -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Status Akun</h3>
                </div>
                <div class="p-6 space-y-4">
                    <x-status-row label="Status">
                        @if($user->email_verified_at)
                            <x-badge color="green"><i class="fas fa-check-circle mr-1"></i> Aktif</x-badge>
                        @else
                            <x-badge color="red"><i class="fas fa-times-circle mr-1"></i> Tidak Aktif</x-badge>
                        @endif
                    </x-status-row>
                    <x-status-row label="Tanggal Daftar" :value="$user->created_at->format('d M Y, H:i')" />
                    <x-status-row label="Terakhir Update" :value="$user->updated_at->format('d M Y, H:i')" />
                    @if($user->email_verified_at)
                        <x-status-row label="Verifikasi Email" :value="$user->email_verified_at->format('d M Y, H:i')" />
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="block w-full bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary-dark transition-colors text-center font-medium">
                        <i class="fas fa-edit mr-2"></i> Edit Pengguna
                    </a>

                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="block w-full border border-primary text-primary py-2 px-4 rounded-lg hover:bg-primary hover:text-white transition-colors font-medium">
                                <i class="fas fa-{{ $user->email_verified_at ? 'user-slash' : 'user-check' }} mr-2"></i>
                                {{ $user->email_verified_at ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <button onclick="confirmDelete()" class="block w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i> Hapus Pengguna
                        </button>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i> Ini adalah akun Anda sendiri
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">{{ $user->created_at->diffInDays(now()) }}</div>
                        <p class="text-sm text-gray-500">Hari sejak bergabung</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center text-sm text-gray-600">
                        <i class="fas fa-chart-line mr-1"></i> Statistik detail akan segera hadir
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
