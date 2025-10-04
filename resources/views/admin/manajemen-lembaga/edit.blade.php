@extends('layouts.admin')

@section('title', 'Edit Lembaga')

@section('content')
<div class="w-full px-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.manajemen-lembaga') }}" 
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg mr-3">
            ← Kembali
        </a>
        <div>
            <h1 class="text-2xl font-bold">Edit Lembaga</h1>
            <p class="text-gray-500">Edit data lembaga <b>{{ $lembaga->nama }}</b></p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.manajemen-lembaga', $lembaga) }}" 
          class="bg-white shadow-md rounded-xl p-6 w-full">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama -->
            <div class="col-span-2">
                <label for="nama" class="block font-medium text-gray-700 mb-2">Nama Lembaga *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $lembaga->nama) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300"
                       placeholder="Masukkan nama lembaga" required>
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block font-medium text-gray-700 mb-2">Type Lembaga *</label>
                <select id="type" name="type" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300 bg-white shadow-sm" required>
                    <option value="Sekolah" {{ old('type', $lembaga->type) == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                    <option value="Lembaga" {{ old('type', $lembaga->type) == 'Lembaga' ? 'selected' : '' }}>Lembaga</option>
                    <option value="Individu" {{ old('type', $lembaga->type) == 'Individu' ? 'selected' : '' }}>Individu</option>
                </select>
            </div>

            <!-- Alamat -->
            <div class="col-span-2">
                <label for="alamat" class="block font-medium text-gray-700 mb-2">Alamat *</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300"
                          placeholder="Masukkan alamat lengkap lembaga" required>{{ old('alamat', $lembaga->alamat) }}</textarea>
            </div>

            <!-- Deskripsi -->
            <div class="col-span-2">
                <label for="deskripsi" class="block font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300"
                          placeholder="Tuliskan deskripsi lembaga (opsional)">{{ old('deskripsi', $lembaga->deskripsi) }}</textarea>
            </div>
        </div>

        <!-- Informasi Lembaga -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 my-6">
            <h4 class="font-semibold mb-3">Informasi Lembaga</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
                <div><strong>Dibuat:</strong> {{ $lembaga->created_at->format('d/m/Y H:i') }}</div>
                <div><strong>Terakhir Update:</strong> {{ $lembaga->updated_at->format('d/m/Y H:i') }}</div>
                <div><strong>Total Pengguna:</strong> {{ $lembaga->users()->count() }} pengguna</div>
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 my-6">
            <h4 class="font-semibold mb-3 flex items-center">
                <i class="fas fa-eye mr-2"></i> Preview Lembaga
            </h4>
            <div class="bg-white border rounded-lg p-4 flex items-start">
                <div class="w-10 h-10 flex items-center justify-center rounded-lg text-white font-bold mr-3"
                     style="background: linear-gradient(135deg,#513587,#A7AAFF)">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <div class="font-semibold" id="preview-nama">{{ $lembaga->nama }}</div>
                    <div class="text-sm text-gray-500">
                        <span class="px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs" id="preview-type">{{ $lembaga->type }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1" id="preview-alamat">{{ $lembaga->alamat }}</div>
                </div>
            </div>
        </div>

        <!-- Daftar Pengguna -->
        @if($lembaga->users()->count() > 0)
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 my-6">
            <h4 class="font-semibold mb-3 flex items-center">
                <i class="fas fa-users mr-2"></i> Pengguna di Lembaga Ini ({{ $lembaga->users()->count() }})
            </h4>
            <div class="space-y-2 max-h-56 overflow-y-auto">
                @foreach($lembaga->users()->limit(10)->get() as $user)
                <div class="bg-white border rounded-lg p-3 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-8 h-8 flex items-center justify-center bg-purple-100 text-purple-700 font-semibold rounded-full mr-2">
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-sm">{{ $user->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @endforeach
                @if($lembaga->users()->count() > 10)
                <div class="text-center text-gray-500 text-sm py-2">
                    dan {{ $lembaga->users()->count() - 10 }} pengguna lainnya...
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Tombol -->
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('admin.manajemen-lembaga') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                <i class="fas fa-save mr-1"></i> Update Lembaga
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaInput = document.getElementById('nama');
    const typeInput = document.getElementById('type');
    const alamatInput = document.getElementById('alamat');

    const previewNama = document.getElementById('preview-nama');
    const previewType = document.getElementById('preview-type');
    const previewAlamat = document.getElementById('preview-alamat');

    function updatePreview() {
        previewNama.textContent = namaInput.value || 'Nama Lembaga';
        previewType.textContent = typeInput.value || 'Type';
        previewAlamat.textContent = alamatInput.value || 'Alamat lembaga';
    }

    namaInput.addEventListener('input', updatePreview);
    typeInput.addEventListener('change', updatePreview);
    alamatInput.addEventListener('input', updatePreview);
});
</script>
@endsection
