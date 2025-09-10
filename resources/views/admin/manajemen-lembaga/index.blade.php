@extends('layouts.admin')

@section('title', 'Manajemen Lembaga')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Manajemen Lembaga</h1>
            <p class="page-subtitle">Kelola lembaga dalam sistem Inkluvia</p>
        </div>
        <a href="{{ route('admin.manajemen-lembaga.create') }}" class="btn btn-primary">
            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
            Tambah Lembaga
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" action="{{ route('admin.manajemen-lembaga') }}">
        <div class="filters-grid">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search" class="form-label">Cari Lembaga</label>
                <input type="text" id="search" name="search" class="form-input" 
                       placeholder="Nama, type, alamat..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="type" class="form-label">Filter Type</label>
                <select id="type" name="type" class="form-select">
                    <option value="">Semua Type</option>
                    @php
                        $types = \App\Models\Lembaga::distinct()->pluck('type');
                    @endphp
                    @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="sort_by" class="form-label">Urutkan</label>
                <select id="sort_by" name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="nama" {{ request('sort_by') === 'nama' ? 'selected' : '' }}>Nama</option>
                    <option value="type" {{ request('sort_by') === 'type' ? 'selected' : '' }}>Type</option>
                    <option value="users_count" {{ request('sort_by') === 'users_count' ? 'selected' : '' }}>Jumlah Pengguna</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">&nbsp;</label>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.manajemen-lembaga') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
    </form>
</div>

<!-- Lembaga Table -->
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Nama Lembaga
                        @if(request('sort_by') === 'nama')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'type', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Type
                        @if(request('sort_by') === 'type')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>Alamat</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'users_count', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Pengguna
                        @if(request('sort_by') === 'users_count')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                       style="color: inherit; text-decoration: none; display: flex; align-items: center;">
                        Dibuat
                        @if(request('sort_by') === 'created_at')
                            <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}" style="margin-left: 0.5rem;"></i>
                        @else
                            <i class="fas fa-sort" style="margin-left: 0.5rem; opacity: 0.3;"></i>
                        @endif
                    </a>
                </th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lembagas as $lembaga)
            <tr>
                <td>
                    <div style="display: flex; align-items: center;">
                        <div style="
                            width: 40px; 
                            height: 40px; 
                            border-radius: 8px; 
                            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
                            color: white; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center; 
                            font-weight: 600; 
                            margin-right: 0.75rem;
                        ">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $lembaga->nama }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-primary">{{ $lembaga->type }}</span>
                </td>
                <td>
                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="{{ $lembaga->alamat }}">
                        {{ $lembaga->alamat }}
                    </div>
                </td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-users" style="margin-right: 0.5rem; color: var(--text-light);"></i>
                        {{ $lembaga->users_count }} pengguna
                    </div>
                </td>
                <td>{{ $lembaga->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.manajemen-lembaga.edit', $lembaga) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.manajemen-lembaga.destroy', $lembaga) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus lembaga ini? {{ $lembaga->users_count > 0 ? 'Lembaga ini masih memiliki ' . $lembaga->users_count . ' pengguna.' : '' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" {{ $lembaga->users_count > 0 ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-light);">
                    <i class="fas fa-building" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; display: block;"></i>
                    Tidak ada lembaga ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($lembagas->hasPages())
<div class="pagination">
    {{ $lembagas->links() }}
</div>
@endif

<!-- Summary -->
<div style="margin-top: 1rem; text-align: center; color: var(--text-light); font-size: 0.875rem;">
    Menampilkan {{ $lembagas->firstItem() ?? 0 }} - {{ $lembagas->lastItem() ?? 0 }} dari {{ $lembagas->total() }} lembaga
</div>
@endsection