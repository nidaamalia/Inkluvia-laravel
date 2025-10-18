@extends('layouts.admin')

@section('title', 'Upload Materi Pembelajaran')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header with back button -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.manajemen-materi') }}" class="text-primary hover:text-primary-dark transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Upload Materi Pembelajaran</h1>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 sm:p-6 lg:p-8">
            <form action="{{ route('admin.manajemen-materi.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <!-- Mandatory Fields Section -->
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-900 mb-4">Informasi Materi</h5>
                    
                    <!-- Judul Materi -->
                    <div class="mb-6">
                        <label for="judul" class="block text-sm font-semibold text-gray-900 mb-2">
                            Judul Materi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('judul') border-red-500 @enderror" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul') }}" 
                               placeholder="Masukkan judul materi pembelajaran" 
                               required>
                        @error('judul')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tingkat Pembelajaran -->
                    <div class="mb-6">
                        <label for="tingkat" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tingkat Pembelajaran <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('tingkat') border-red-500 @enderror" 
                                id="tingkat" 
                                name="tingkat" 
                                required
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="">Pilih Tingkat</option>
                            @foreach(\App\Models\Material::getTingkatOptions() as $key => $value)
                                <option value="{{ $key }}" {{ old('tingkat') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('tingkat')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pengaturan Hak Akses -->
                    <div class="mb-6">
                        <label for="akses" class="block text-sm font-semibold text-gray-900 mb-2">
                            Pengaturan Hak Akses <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('akses') border-red-500 @enderror" 
                                id="akses" 
                                name="akses" 
                                required
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="">Pilih Hak Akses</option>
                            @foreach(\App\Models\Material::getAksesOptions() as $key => $value)
                                <option value="{{ $key }}" {{ old('akses') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('akses')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Optional Fields Section -->
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-900 mb-4">Informasi Tambahan (Opsional)</h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tahun Terbit -->
                        <div>
                            <label for="tahun_terbit" class="block text-sm font-semibold text-gray-900 mb-2">
                                Tahun Terbit
                            </label>
                            <input type="number" 
                                   class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('tahun_terbit') border-red-500 @enderror" 
                                   id="tahun_terbit" 
                                   name="tahun_terbit" 
                                   value="{{ old('tahun_terbit') }}" 
                                   placeholder="Contoh: 2024" 
                                   min="1900" 
                                   max="{{ date('Y') }}">
                            @error('tahun_terbit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Penerbit -->
                        <div>
                            <label for="penerbit" class="block text-sm font-semibold text-gray-900 mb-2">
                                Penerbit
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('penerbit') border-red-500 @enderror" 
                                   id="penerbit" 
                                   name="penerbit" 
                                   value="{{ old('penerbit') }}" 
                                   placeholder="Nama penerbit">
                            @error('penerbit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Edisi -->
                        <div>
                            <label for="edisi" class="block text-sm font-semibold text-gray-900 mb-2">
                                Edisi
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('edisi') border-red-500 @enderror" 
                                   id="edisi" 
                                   name="edisi" 
                                   value="{{ old('edisi') }}" 
                                   placeholder="Contoh: Edisi 1, Cetakan 2">
                            @error('edisi')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategori" class="block text-sm font-semibold text-gray-900 mb-2">
                                Kategori
                            </label>
                            <select class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all appearance-none bg-white @error('kategori') border-red-500 @enderror" 
                                    id="kategori" 
                                    name="kategori"
                                    style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                <option value="">Pilih Kategori</option>
                                @foreach(\App\Models\Material::getKategoriOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mt-6">
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-900 mb-2">
                            Deskripsi
                        </label>
                        <textarea class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-y @error('deskripsi') border-red-500 @enderror" 
                                  id="deskripsi" 
                                  name="deskripsi" 
                                  rows="4" 
                                  placeholder="Masukkan deskripsi materi pembelajaran">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-900 mb-4">Upload File</h5>
                    
                    <div>
                        <label for="file" class="block text-sm font-semibold text-gray-900 mb-2">
                            Upload File <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-primary rounded-xl p-8 sm:p-12 text-center bg-purple-50 hover:bg-purple-100 transition-all cursor-pointer @error('file') border-red-500 @enderror" 
                             id="uploadArea">
                            <div class="upload-content">
                                <div class="mb-4">
                                    <i class="fas fa-file-pdf text-primary text-5xl sm:text-6xl"></i>
                                </div>
                                <p class="text-gray-900 font-medium mb-2 text-sm sm:text-base">Drag & Drop file PDF atau klik untuk browse</p>
                                <p class="text-gray-500 text-xs sm:text-sm">Maksimal ukuran file: 50MB</p>
                            </div>
                            <input type="file" class="hidden" id="file" name="file" accept=".pdf" required>
                        </div>
                        @error('file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('admin.manajemen-materi') }}" 
                       class="w-full sm:w-auto px-6 py-3 text-center border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium"
                            id="submitButton">
                        <i class="fas fa-upload mr-2"></i>Upload Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="conversionOverlay" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-lg px-8 py-6 flex flex-col items-center gap-4">
        <i class="fas fa-spinner fa-spin text-3xl text-primary"></i>
        <h3 class="text-lg font-semibold text-gray-900">Sedang memproses materi</h3>
        <p class="text-sm text-gray-600 text-center max-w-xs">
            Mohon tunggu sebentar, sistem sedang melakukan konversi PDF dengan Gemini AI.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('file');
    const uploadContent = uploadArea.querySelector('.upload-content');
    const submitButton = document.getElementById('submitButton');
    const overlay = document.getElementById('conversionOverlay');

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
            updateFileDisplay(files[0]);
        }
    });

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            updateFileDisplay(file);
            validateFile(file);
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
        
        if (file.size > 50 * 1024 * 1024) {
            alert('Ukuran file maksimal 50MB!');
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
            <p class="text-gray-900 font-medium mb-2 text-sm sm:text-base">Drag & Drop file PDF atau klik untuk browse</p>
            <p class="text-gray-500 text-xs sm:text-sm">Maksimal ukuran file: 50MB</p>
        `;
    };

    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const requiredFields = ['judul', 'tingkat', 'akses', 'file'];
        let isValid = true;
        
        requiredFields.forEach(function(fieldName) {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
            return;
        }

        submitButton.disabled = true;
        submitButton.classList.add('opacity-70', 'cursor-not-allowed');
        overlay.classList.remove('hidden');
    });
});
</script>
@endsection