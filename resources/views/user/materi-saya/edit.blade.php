@extends('layouts.user')

@section('title', 'Edit Materi')

@section('content')
<div class="max-w-4xl mx-auto">
    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Edit Materi</h1>
            <a href="{{ route('user.materi-saya') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
        <p class="text-gray-600">Perbarui informasi materi Anda</p>
    </header>

    <main>
        <form method="POST" action="{{ route('user.materi-saya.update', $material) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Materi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $material->judul) }}" required
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary @error('judul') border-red-500 @enderror">
                    @error('judul')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary">{{ old('deskripsi', $material->deskripsi) }}</textarea>
                </div>

                <!-- Kategori, Tingkat, Kelas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kategori -->
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="kategori" name="kategori" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kategori', $material->kategori) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tingkat -->
                    <div>
                        <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat <span class="text-red-500">*</span>
                        </label>
                        <select id="tingkat" name="tingkat" required onchange="updateKelasOptions()"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary">
                            <option value="">Pilih Tingkat</option>
                            @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('tingkat', $material->tingkat) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select id="kelas" name="kelas" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary">
                            <option value="">Pilih Kelas</option>
                            @foreach(\App\Models\Material::getKelasOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kelas', $material->kelas) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Hak Akses -->
                <div>
                    <label for="akses" class="block text-sm font-medium text-gray-700 mb-2">
                        Hak Akses <span class="text-red-500">*</span>
                    </label>
                    <select id="akses" name="akses" required class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary">
                        @foreach(\App\Models\Material::getAksesOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('akses', $material->akses) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- File Upload (Optional) -->
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Ganti File PDF (Opsional)
                    </label>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Mengganti file akan memulai konversi ulang. Konten yang sudah diedit manual akan hilang.
                        </p>
                    </div>
                    <input type="file" id="file" name="file" accept=".pdf" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary"
                           onchange="displayFileName(this)">
                    <p id="file-name" class="mt-2 text-sm text-gray-500"></p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('user.materi-saya') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </div>
        </form>
    </main>
</div>
@endsection

@push('scripts')
<script>
function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = `File dipilih: ${fileName}`;
        fileNameDisplay.classList.add('text-primary', 'font-medium');
    }
}

function updateKelasOptions() {
    const tingkat = document.getElementById('tingkat').value;
    const kelasSelect = document.getElementById('kelas');
    const currentKelas = '{{ old("kelas", $material->kelas) }}';
    
    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
    
    const allOptions = @json(\App\Models\Material::getKelasOptions());
    let filteredOptions = {};
    
    if (tingkat === 'paud') {
        filteredOptions = {
            'tk_a': allOptions['tk_a'],
            'tk_b': allOptions['tk_b']
        };
    } else if (tingkat === 'sd') {
        for (let i = 1; i <= 6; i++) {
            filteredOptions[i.toString()] = allOptions[i.toString()];
        }
    } else if (tingkat === 'smp') {
        for (let i = 7; i <= 9; i++) {
            filteredOptions[i.toString()] = allOptions[i.toString()];
        }
    } else if (tingkat === 'sma') {
        for (let i = 10; i <= 12; i++) {
            filteredOptions[i.toString()] = allOptions[i.toString()];
        }
    } else if (tingkat === 'perguruan_tinggi') {
        for (let i = 1; i <= 8; i++) {
            filteredOptions[`semester_${i}`] = allOptions[`semester_${i}`];
        }
    } else if (tingkat === 'umum') {
        filteredOptions['semua'] = allOptions['semua'];
    }
    
    for (const [value, label] of Object.entries(filteredOptions)) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        if (value === currentKelas) {
            option.selected = true;
        }
        kelasSelect.appendChild(option);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tingkat = document.getElementById('tingkat').value;
    if (tingkat) {
        updateKelasOptions();
    }
});
</script>
@endpush