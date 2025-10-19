@extends('layouts.user')

@section('title', 'Upload Materi Baru')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <a href="#main-content" class="sr-only focus:not-sr-only">Lewati ke konten utama</a>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('user.materi-saya') }}" class="text-primary hover:text-primary-dark transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
            <span class="sr-only">Kembali</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Upload Materi Baru</h1>
    </div>

    <main id="main-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 sm:p-6 lg:p-8">
                <p class="text-gray-600 mb-6">Upload file PDF untuk dikonversi menjadi format Braille</p>
                <form method="POST" action="{{ route('user.materi-saya.store') }}" enctype="multipart/form-data" id="material-upload-form" class="space-y-8">
                    @csrf

                    <div class="space-y-8">
                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-semibold text-gray-900 mb-2">
                        Judul Materi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                           class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('judul') border-red-500 @enderror"
                           placeholder="Masukkan judul materi pembelajaran">
                    @error('judul')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-900 mb-2">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                              class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-y @error('deskripsi') border-red-500 @enderror"
                              placeholder="Masukkan deskripsi materi pembelajaran">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori, Tingkat, Kelas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kategori -->
                    <div>
                        <label for="kategori" class="block text-sm font-semibold text-gray-900 mb-2">Kategori</label>
                        <select id="kategori" name="kategori" class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('kategori') border-red-500 @enderror" style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Material::getKategoriOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kategori') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kategori')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tingkat -->
                    <div>
                        <label for="tingkat" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tingkat <span class="text-red-500">*</span>
                        </label>
                        <select id="tingkat" name="tingkat" required onchange="updateKelasOptions()"
                                class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('tingkat') border-red-500 @enderror" style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="">Pilih Tingkat</option>
                            @foreach(\App\Models\Material::getTingkatOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('tingkat') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tingkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelas (NEW) -->
                    <div>
                        <label for="kelas" class="block text-sm font-semibold text-gray-900 mb-2">Kelas</label>
                        <select id="kelas" name="kelas" class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('kelas') border-red-500 @enderror" style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="">Pilih Kelas</option>
                            @foreach(\App\Models\Material::getKelasOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('kelas') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kelas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hak Akses -->
                <div>
                    <label for="akses" class="block text-sm font-semibold text-gray-900 mb-2">
                        Hak Akses <span class="text-red-500">*</span>
                    </label>
                    <select id="akses" name="akses" required class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('akses') border-red-500 @enderror" style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        @foreach(\App\Models\Material::getAksesOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('akses', 'private') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('akses')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        <span class="font-medium">Privat:</span> Hanya Anda. 
                        <span class="font-medium">Publik:</span> Lembaga/sekolah yang sama.
                    </p>
                </div>

                <!-- File Upload -->
                <div>
                    <label for="file" class="block text-sm font-semibold text-gray-900 mb-2">
                        File PDF <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-primary rounded-xl p-8 sm:p-12 text-center bg-purple-50 hover:bg-purple-100 transition-all cursor-pointer @error('file') border-red-500 @enderror" id="uploadArea">
                        <div class="upload-content space-y-2">
                            <div class="mb-4">
                                <i class="fas fa-file-pdf text-primary text-5xl sm:text-6xl"></i>
                            </div>
                            <p class="text-gray-900 font-medium text-sm sm:text-base">Drag & Drop file PDF atau klik untuk browse</p>
                            <p class="text-gray-500 text-xs sm:text-sm">Maksimal ukuran file: 10MB</p>
                        </div>
                        <input id="file" name="file" type="file" accept=".pdf" required class="hidden">
                    </div>
                    @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('user.materi-saya') }}" class="w-full sm:w-auto px-6 py-3 text-center border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <i class="fas fa-upload mr-2"></i>Upload dan Konversi
                    </button>
                </div>
            </div>
        </form>
            </div>
        </div>
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('material-upload-form');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('file');
    const uploadContent = uploadArea?.querySelector('.upload-content');

    if (uploadArea && fileInput && uploadContent) {
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('scale-105', 'bg-purple-200');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('scale-105', 'bg-purple-200');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('scale-105', 'bg-purple-200');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                if (validateFile(files[0])) {
                    updateFileDisplay(files[0]);
                }
            }
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && validateFile(file)) {
                updateFileDisplay(file);
            }
        });

        function updateFileDisplay(file) {
            uploadContent.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-file-pdf text-green-600 text-5xl sm:text-6xl"></i>
                </div>
                <p class="text-gray-900 font-medium mb-2 text-sm sm:text-base">${file.name}</p>
                <p class="text-gray-500 text-xs sm:text-sm">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                <button type="button" class="mt-4 px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors" onclick="clearFile()">
                    <i class="fas fa-times mr-1"></i>Hapus File
                </button>
            `;
        }

        function validateFile(file) {
            if (file.type !== 'application/pdf') {
                alert('Hanya file PDF yang diperbolehkan!');
                clearFile();
                return false;
            }

            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran file maksimal 10MB!');
                clearFile();
                return false;
            }

            return true;
        }

        window.clearFile = function() {
            fileInput.value = '';
            uploadContent.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-file-pdf text-primary text-5xl sm:text-6xl"></i>
                </div>
                <p class="text-gray-900 font-medium text-sm sm:text-base">Drag & Drop file PDF atau klik untuk browse</p>
                <p class="text-gray-500 text-xs sm:text-sm">Maksimal ukuran file: 10MB</p>
            `;
        };
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const modal = document.getElementById('processingModal');
            const submitButton = form.querySelector('button[type="submit"]');

            const requiredFields = ['judul', 'tingkat', 'akses'];
            let isValid = true;

            requiredFields.forEach(function(fieldName) {
                const field = document.getElementById(fieldName);
                if (!field || !field.value.trim()) {
                    field?.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                alert('Mohon pilih file PDF terlebih dahulu!');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }

            modal?.classList.remove('hidden');
            submitButton?.setAttribute('disabled', 'disabled');
            submitButton?.classList.add('opacity-70', 'cursor-not-allowed');
        });
    }

    const tingkat = document.getElementById('tingkat')?.value;
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