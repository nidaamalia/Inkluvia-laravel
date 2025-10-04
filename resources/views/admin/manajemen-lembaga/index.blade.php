@extends('layouts.admin')

@section('title', 'Manajemen Lembaga')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Lembaga</h1>
        <p class="text-gray-500">Kelola lembaga dalam sistem Inkluvia</p>
    </div>
    <a href="{{ route('admin.manajemen-lembaga.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
        <i class="fas fa-plus mr-2"></i>
        Tambah Lembaga
    </a>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <form method="GET" action="{{ route('admin.manajemen-lembaga') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <div>
            <label for="search" class="block text-sm font-medium text-gray-600">Cari Lembaga</label>
            <input type="text" id="search" name="search" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-primary focus:border-primary sm:text-sm" 
                   placeholder="Nama, type, alamat..." 
                   value="{{ request('search') }}">
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-600">Filter Type</label>
            <select id="type" name="type" 
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                <option value="">Semua Type</option>
                @php $types = \App\Models\Lembaga::distinct()->pluck('type'); @endphp
                @foreach($types as $type)
                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sort_by" class="block text-sm font-medium text-gray-600">Urutkan</label>
            <select id="sort_by" name="sort_by" 
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                <option value="nama" {{ request('sort_by') === 'nama' ? 'selected' : '' }}>Nama</option>
                <option value="type" {{ request('sort_by') === 'type' ? 'selected' : '' }}>Type</option>
                <option value="users_count" {{ request('sort_by') === 'users_count' ? 'selected' : '' }}>Jumlah Pengguna</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" 
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                Cari
            </button>
            <a href="{{ route('admin.manajemen-lembaga') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
        
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
    </form>
</div>

<!-- Lembaga Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Nama Lembaga</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Type</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Alamat</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Pengguna</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Dibuat</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($lembagas as $lembaga)
            <tr>
                <td class="px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gradient-to-br from-primary to-secondary text-white font-bold">
                        <i class="fas fa-building"></i>
                    </div>
                    <span class="font-medium text-gray-800">{{ $lembaga->nama }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs bg-primary/10 text-primary font-semibold">
                        {{ $lembaga->type }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 truncate max-w-xs" title="{{ $lembaga->alamat }}">
                    {{ $lembaga->alamat }}
                </td>
                <td class="px-4 py-3 flex items-center gap-2 text-gray-600">
                    <i class="fas fa-users"></i>
                    {{ $lembaga->users_count }} pengguna
                </td>
                <td class="px-4 py-3 text-gray-500">
                    {{ $lembaga->created_at->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="{{ route('admin.manajemen-lembaga.edit', $lembaga) }}" 
                       class="p-2 rounded bg-secondary text-white hover:bg-secondary-light transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.manajemen-lembaga.destroy', $lembaga) }}" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus lembaga ini? {{ $lembaga->users_count > 0 ? 'Lembaga ini masih memiliki ' . $lembaga->users_count . ' pengguna.' : '' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 rounded bg-red-500 text-white hover:bg-red-600 transition"
                                {{ $lembaga->users_count > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                    <i class="fas fa-building text-3xl mb-2"></i>
                    <p>Tidak ada lembaga ditemukan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($lembagas->hasPages())
<div class="mt-4">
    {{ $lembagas->links() }}
</div>
@endif

<!-- Summary -->
<div class="mt-2 text-center text-sm text-gray-500">
    Menampilkan {{ $lembagas->firstItem() ?? 0 }} - {{ $lembagas->lastItem() ?? 0 }} dari {{ $lembagas->total() }} lembaga
</div>
@endsection