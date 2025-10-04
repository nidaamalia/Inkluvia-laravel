@extends('layouts.admin')

@section('title', 'Content Manager - Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manajemen Materi</h1>
        <a href="{{ route('admin.manajemen-materi.create') }}" 
           class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium text-center">
            <i class="fas fa-plus mr-2"></i>Tambah Materi Baru
        </a>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <form method="GET" action="{{ route('admin.manajemen-materi') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               class="w-full pl-12 pr-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari materi">
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <select name="kategori" 
                            class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white"
                            style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Material::getKategoriOptions() as $key => $value)
                            <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <select name="status" 
                            class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white"
                            style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div>
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Materials Content -->
    @if($materials->count() > 0)
        <!-- Desktop Table View -->
        <div class="hidden lg:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Judul</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Tingkat</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Akses</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Braille</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($materials as $material)
                            <tr class="hover:bg-purple-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $material->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $material->judul }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full border border-gray-300">
                                        {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? ucfirst($material->tingkat) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <select class="status-select text-xs font-medium px-3 py-1.5 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary appearance-none bg-white pr-8" 
                                            data-material-id="{{ $material->id }}" 
                                            data-current-status="{{ $material->status }}"
                                            style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 12px;">
                                        <option value="draft" {{ $material->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="processing" {{ $material->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="review" {{ $material->status == 'review' ? 'selected' : '' }}>Review</option>
                                        <option value="published" {{ $material->status == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="archived" {{ $material->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-{{ $material->akses_badge_color }} text-white text-xs font-medium rounded-full">
                                        {{ $material->akses_display }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($material->braille_data_path)
                                        <button type="button" 
                                                class="px-3 py-1.5 text-xs font-medium text-primary border-2 border-primary rounded-lg hover:bg-primary-dark hover:text-white transition-colors"
                                                onclick="previewBraille({{ $material->id }})">
                                            <i class="fas fa-braille mr-1"></i>Braille
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-500">Belum tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                class="p-2 text-primary hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-colors" 
                                                onclick="previewMaterial({{ $material->id }})"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <a href="{{ route('admin.manajemen-materi.edit', $material) }}" 
                                           class="p-2 text-yellow-600 hover:bg-yellow-600 hover:text-white border-2 border-yellow-600 rounded-lg transition-colors"
                                           title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.manajemen-materi.destroy', $material) }}" 
                                              method="POST" 
                                              class="inline" 
                                              onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-red-600 hover:bg-red-600 hover:text-white border-2 border-red-600 rounded-lg transition-colors"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @foreach($materials as $material)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 mb-1">{{ $material->judul }}</h3>
                            <p class="text-xs text-gray-500">{{ $material->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <!-- Tingkat -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Tingkat:</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full border border-gray-300">
                                {{ \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? ucfirst($material->tingkat) }}
                            </span>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <select class="status-select text-xs font-medium px-3 py-1.5 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary appearance-none bg-white pr-8" 
                                    data-material-id="{{ $material->id }}" 
                                    data-current-status="{{ $material->status }}"
                                    style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 12px;">
                                <option value="draft" {{ $material->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="processing" {{ $material->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="review" {{ $material->status == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="published" {{ $material->status == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ $material->status == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <!-- Akses -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Akses:</span>
                            <span class="px-3 py-1 bg-{{ $material->akses_badge_color }} text-white text-xs font-medium rounded-full">
                                {{ $material->akses_display }}
                            </span>
                        </div>

                        <!-- Braille -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Braille:</span>
                            @if($material->braille_data_path)
                                <button type="button" 
                                        class="px-3 py-1.5 text-xs font-medium text-blue-600 border-2 border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-colors" 
                                        onclick="previewBraille({{ $material->id }})">
                                    <i class="fas fa-braille mr-1"></i>Lihat
                                </button>
                            @else
                                <span class="text-xs text-gray-500">Belum tersedia</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button type="button" 
                                class="flex-1 px-4 py-2 text-sm font-medium text-primary hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-colors" 
                                onclick="previewMaterial({{ $material->id }})">
                            <i class="fas fa-eye mr-1"></i>Lihat
                        </button>
                        <a href="{{ route('admin.manajemen-materi.edit', $material) }}" 
                           class="flex-1 px-4 py-2 text-center text-sm font-medium text-yellow-600 hover:bg-yellow-600 hover:text-white border-2 border-yellow-600 rounded-lg transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.manajemen-materi.destroy', $material) }}" 
                              method="POST" 
                              class="flex-1" 
                              onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-600 hover:text-white border-2 border-red-600 rounded-lg transition-colors">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mt-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan {{ $materials->firstItem() }} - {{ $materials->lastItem() }} dari {{ $materials->total() }} hasil
                </div>
                @if ($materials->hasPages())
                    <nav aria-label="Pagination">
                        <ul class="flex items-center gap-2">
                            @if ($materials->onFirstPage())
                                <li>
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $materials->previousPageUrl() }}" 
                                       class="px-3 py-2 text-sm text-primary hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-colors">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            @foreach ($materials->getUrlRange(1, $materials->lastPage()) as $page => $url)
                                @if ($page == $materials->currentPage())
                                    <li>
                                        <span class="px-3 py-2 text-sm bg-primary text-white rounded-lg font-medium">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li class="hidden sm:block">
                                        <a href="{{ $url }}" 
                                           class="px-3 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white border-2 border-gray-300 rounded-lg transition-colors">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                            @if ($materials->hasMorePages())
                                <li>
                                    <a href="{{ $materials->nextPageUrl() }}" 
                                       class="px-3 py-2 text-sm text-primary hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-colors">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li>
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
            <h5 class="text-xl font-bold text-gray-900 mb-2">Belum ada materi</h5>
            <p class="text-gray-600 mb-6">Mulai dengan menambahkan materi baru</p>
            <a href="{{ route('admin.manajemen-materi.create') }}" 
               class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>Tambah Materi Pertama
            </a>
        </div>
    @endif
</div>

<!-- Material Preview Modal -->
<div class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" id="materialPreviewModal">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-primary text-white px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-bold">
                <i class="fas fa-book mr-2"></i>Preview Materi
            </h5>
            <button type="button" class="text-white hover:text-gray-200 text-2xl" onclick="closeModal('materialPreviewModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]" id="previewContent">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                <p class="mt-4 text-gray-600">Memuat preview materi...</p>
            </div>
        </div>
        <div class="border-t border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3">
            <button type="button" 
                    class="w-full sm:w-auto px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors" 
                    onclick="closeModal('materialPreviewModal')">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
            <button type="button" 
                    class="w-full sm:w-auto px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors" 
                    id="downloadJsonBtn">
                <i class="fas fa-download mr-2"></i>Download JSON
            </button>
        </div>
    </div>
</div>

<!-- Braille Preview Modal -->
<div class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" id="braillePreviewModal">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-primary text-white px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-bold">
                <i class="fas fa-braille mr-2"></i>Preview Braille
            </h5>
            <button type="button" class="text-white hover:text-gray-200 text-2xl" onclick="closeModal('braillePreviewModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]" id="brailleContent">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 bg-primary"></div>
                <p class="mt-4 text-gray-600">Memuat konten braille...</p>
            </div>
        </div>
        <div class="border-t border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3">
            <button type="button" 
                    class="w-full sm:w-auto px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors" 
                    onclick="closeModal('braillePreviewModal')">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
            <button type="button" 
                class="w-full sm:w-auto px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors" 
                id="downloadBrailleBtn">
                <i class="fas fa-download mr-2"></i>Download Braille
            </button>
        </div>
    </div>
</div>

<script>
let currentMaterialId = null;
let currentBrailleMaterialId = null;

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function previewMaterial(materialId) {
    currentMaterialId = materialId;
    const modal = document.getElementById('materialPreviewModal');
    modal.classList.remove('hidden');
    
    document.getElementById('previewContent').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            <p class="mt-4 text-gray-600">Memuat preview materi...</p>
        </div>
    `;
    
    fetch(`/admin/manajemen-materi/${materialId}/preview`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            displayMaterialPreview(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewContent').innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-6xl text-yellow-500 mb-4"></i>
                    <h5 class="text-xl font-bold text-gray-900 mb-2">Gagal memuat preview</h5>
                    <p class="text-gray-600">${error.message || 'Terjadi kesalahan saat memuat data materi'}</p>
                </div>
            `;
        });
}

function displayMaterialPreview(data) {
    let html = '';
    
    if (data.pages && data.pages.length > 0) {
        if (data.judul || data.penerbit || data.tahun || data.edisi) {
            html += '<div class="bg-primary text-white rounded-lg p-4 mb-6">';
            html += '<h6 class="font-bold text-lg mb-3"><i class="fas fa-info-circle mr-2"></i>Informasi Materi</h6>';
            html += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">';
            html += `<p><strong>Judul:</strong> ${data.judul || 'Tidak ada'}</p>`;
            html += `<p><strong>Penerbit:</strong> ${data.penerbit || 'Tidak ada'}</p>`;
            html += `<p><strong>Tahun:</strong> ${data.tahun || 'Tidak ada'}</p>`;
            html += `<p><strong>Edisi:</strong> ${data.edisi || 'Tidak ada'}</p>`;
            html += '</div></div>';
        }
        
        html += '<div><h6 class="font-bold text-lg mb-3"><i class="fas fa-code mr-2"></i>JSON Data</h6>';
        html += '<pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-xs font-mono whitespace-pre-wrap break-words overflow-x-auto max-h-96">';
        html += JSON.stringify(data, null, 2);
        html += '</pre></div>';
    } else {
        html = `
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-bold text-gray-900 mb-2">Tidak ada konten</h5>
                <p class="text-gray-600">Materi ini belum memiliki konten yang dapat ditampilkan</p>
            </div>
        `;
    }
    
    document.getElementById('previewContent').innerHTML = html;
}

document.getElementById('downloadJsonBtn').addEventListener('click', function() {
    if (currentMaterialId) {
        window.open(`/admin/manajemen-materi/${currentMaterialId}/download-json`, '_blank');
    }
});

function previewBraille(materialId) {
    currentBrailleMaterialId = materialId;
    const modal = document.getElementById('braillePreviewModal');
    modal.classList.remove('hidden');
    
    document.getElementById('brailleContent').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Memuat konten braille...</p>
        </div>
    `;
    
    fetch(`/admin/manajemen-materi/${materialId}/braille-content`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            displayBrailleContent(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('brailleContent').innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-6xl text-yellow-500 mb-4"></i>
                    <h5 class="text-xl font-bold text-gray-900 mb-2">Gagal memuat braille</h5>
                    <p class="text-gray-600">${error.message || 'Terjadi kesalahan saat memuat konten braille'}</p>
                </div>
            `;
        });
}

function displayBrailleContent(data) {
    const jsonString = JSON.stringify(data, null, 2);
    const html = `<pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-xs font-mono whitespace-pre-wrap break-words overflow-x-auto max-h-96">${jsonString}</pre>`;
    document.getElementById('brailleContent').innerHTML = html;
}

document.getElementById('downloadBrailleBtn').addEventListener('click', function() {
    if (currentBrailleMaterialId) {
        window.open(`/admin/manajemen-materi/${currentBrailleMaterialId}/download-braille`, '_blank');
    }
});

// Status update functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const materialId = this.dataset.materialId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;
            
            if (newStatus === currentStatus) return;
            
            this.disabled = true;
            this.style.opacity = '0.6';
            
            fetch(`/admin/manajemen-materi/${materialId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.dataset.currentStatus = newStatus;
                    showAlert('Status berhasil diperbarui!', 'success');
                } else {
                    this.value = currentStatus;
                    showAlert(data.error || 'Gagal memperbarui status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.value = currentStatus;
                showAlert('Terjadi kesalahan saat memperbarui status', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white flex items-center gap-3 animate-fade-in`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection