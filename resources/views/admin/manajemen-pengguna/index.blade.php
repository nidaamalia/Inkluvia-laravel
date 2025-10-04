@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
        <p class="text-gray-500">Kelola pengguna sistem Inkluvia</p>
    </div>
    <a href="{{ route('admin.kelola-pengguna.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark shadow">
        <i class="fas fa-plus mr-2"></i>
        Tambah Pengguna
    </a>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <form method="GET" action="{{ route('admin.kelola-pengguna') }}">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Pengguna</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                    placeholder="Nama, email...">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Filter Role</label>
                <select id="role" name="role"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div>
                <label for="lembaga" class="block text-sm font-medium text-gray-700">Filter Lembaga</label>
                <select id="lembaga" name="lembaga"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    <option value="">Semua Lembaga</option>
                    @foreach($lembagas as $lembaga)
                        <option value="{{ $lembaga->id }}" {{ request('lembaga') == $lembaga->id ? 'selected' : '' }}>
                            {{ $lembaga->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sort_by" class="block text-sm font-medium text-gray-700">Urutkan</label>
                <select id="sort_by" name="sort_by"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="nama_lengkap" {{ request('sort_by') === 'nama_lengkap' ? 'selected' : '' }}>Nama</option>
                    <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="role" {{ request('sort_by') === 'role' ? 'selected' : '' }}>Role</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white text-sm rounded-lg shadow hover:bg-primary-dark">
                    Cari
                </button>
                <a href="{{ route('admin.kelola-pengguna') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg shadow hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </div>
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
    </form>
</div>

<!-- Users Table -->
<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama Lengkap</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Email</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Lembaga</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Role</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Jenis Kelamin</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Bergabung</th>
                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            @forelse($users as $user)
                <tr>
                    <td class="px-4 py-2 flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-secondary-light flex items-center justify-center text-primary font-semibold">
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $user->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->lembaga->nama ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                     {{ $user->role === 'admin' 
                                        ? 'bg-primary-light text-white' 
                                        : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $user->jenis_kelamin ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('admin.kelola-pengguna.edit', $user) }}" 
                               class="p-2 bg-secondary text-white rounded hover:bg-secondary-light">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.kelola-pengguna.destroy', $user) }}" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        <i class="fas fa-users text-3xl mb-2 opacity-30 block"></i>
                        Tidak ada pengguna ditemukan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($users->hasPages())
    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endif

<!-- Summary -->
<div class="mt-2 text-center text-sm text-gray-500">
    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
</div>
@endsection
