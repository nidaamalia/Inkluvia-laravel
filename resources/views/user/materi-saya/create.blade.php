@extends('layouts.user')

@section('title', 'Upload Materi Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="#main-content" class="sr-only focus:not-sr-only">Lewati ke konten utama</a>

    <header class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Upload Materi Baru</h1>
            <a href="{{ route('user.materi-saya') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
        <p class="text-gray-600">Upload file PDF untuk dikonversi menjadi format Braille</p>
    </header>

    <main id="main-content">
        <form method="POST" action="{{ route('user.materi-saya.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Materi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary @error('judul') border-red-500 @enderror">
                    @error('judul')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Kategori, Tingkat, Kelas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kategori -->
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="kategori" name="kategori" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kategori') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tingkat -->
                    <div>
                        <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat <span class="text-red-500">*</span>
                        </label>
                        <select id="tingkat" name="tingkat" required onchange="updateKelasOptions()"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Tingkat</option>
                            @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('tingkat') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas (NEW) -->
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select id="kelas" name="kelas" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Kelas</option>
                            @foreach(\App\Models\Material::getKelasOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kelas') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                            <option value="{{ $value }}" {{ old('akses', 'private') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">
                        <span class="font-medium">Privat:</span> Hanya Anda. 
                        <span class="font-medium">Publik:</span> Lembaga/sekolah yang sama.
                    </p>
                </div>

                <!-- File Upload -->
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        File PDF <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark">
                                    <span>Upload file</span>
                                    <input id="file" name="file" type="file" accept=".pdf" required class="sr-only" onchange="displayFileName(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF hingga 10MB</p>
                            <p id="file-name" class="text-sm font-medium text-primary mt-2"></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary">
                        <i class="fas fa-upload mr-2"></i>Upload dan Konversi
                    </button>
                    <a href="{{ route('user.materi-saya') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </div>
        </form>
    </main>
</div>

<!-- Processing Modal -->
<div id="processingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" role="dialog">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 text-center">
        <i class="fas fa-sync fa-spin text-5xl text-primary mb-4"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Sedang Memproses...</h3>
        <p class="text-gray-600">Materi sedang diupload dan dikonversi ke format Braille.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = `File dipilih: ${fileName}`;
    }
}

function updateKelasOptions() {
    const tingkat = document.getElementById('tingkat').value;
    const kelasSelect = document.getElementById('kelas');
    
    // Clear current options
    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
    
    const allOptions = @json(\App\Models\Material::getKelasOptions());
    
    // Filter based on tingkat
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
    
    // Add filtered options
    for (const [value, label] of Object.entries(filteredOptions)) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        kelasSelect.appendChild(option);
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    const modal = document.getElementById('processingModal');
    modal.classList.remove('hidden');
});

// Initialize kelas options on page load
document.addEventListener('DOMContentLoaded', function() {
    const tingkat = document.getElementById('tingkat').value;
    if (tingkat) {
        updateKelasOptions();
    }
});
</script>
@endpush

@push('styles')
<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>
@endpush