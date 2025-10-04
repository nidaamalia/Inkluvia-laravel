@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('admin.kelola-pengguna') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors mr-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1>
            <p class="text-gray-500 text-sm">Edit data pengguna <span class="font-medium">{{ $user->nama_lengkap }}</span></p>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('admin.kelola-pengguna.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Username & Nama Lengkap -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" 
                           value="{{ old('name', $user->name) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" 
                           required placeholder="Masukkan username">
                </div>
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap"
                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" 
                           required placeholder="Masukkan nama lengkap">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" 
                       required placeholder="Masukkan alamat email">
            </div>

            <!-- Password -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-3"><i class="fas fa-info-circle mr-1"></i> Kosongkan password jika tidak ingin mengubahnya</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" id="password" name="password"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm"
                               minlength="8" placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm"
                               placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <!-- Tanggal lahir & gender -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" required>
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Lembaga & Role -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="lembaga_id" class="block text-sm font-medium text-gray-700 mb-1">Lembaga <span class="text-red-500">*</span></label>
                    <select id="lembaga_id" name="lembaga_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" required>
                        <option value="">Pilih Lembaga</option>
                        @foreach($lembagas as $lembaga)
                        <option value="{{ $lembaga->id }}" {{ old('lembaga_id', $user->lembaga_id) == $lembaga->id ? 'selected' : '' }}>
                            {{ $lembaga->nama }} ({{ $lembaga->type }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-sm" required>
                        <option value="">Pilih Role</option>
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <!-- Info pengguna -->
            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                <h4 class="text-gray-800 font-medium mb-2">Informasi Pengguna</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div><strong>Bergabung:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</div>
                    <div><strong>Terakhir Update:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.kelola-pengguna') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    Update Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    function validatePassword() {
        if (password.value && password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Password tidak sama');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }
    password.addEventListener('input', validatePassword);
    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endpush
@endsection