@extends('layouts.admin')

@section('title', 'Manajemen Perangkat')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Perangkat</h1>
            <p class="text-gray-500">Kelola perangkat EduBraille dalam sistem Inkluvia</p>
        </div>
        <a href="{{ route('admin.kelola-perangkat.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary/90 transition">
            + Tambah Perangkat
        </a>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-purple-500 to-purple-400 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-laptop text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Total Perangkat</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-400 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-wifi text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Online</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-blue-400 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Aktif</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-tools text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Maintenance</p>
                    <p class="text-xl font-bold">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-xl shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" placeholder="Cari perangkat..."
                   class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
            <select class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
                <option>Semua Status</option>
            </select>
            <select class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
                <option>Semua Koneksi</option>
            </select>
            <select class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
                <option>Semua Lembaga</option>
            </select>
            <div class="flex space-x-2">
                <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">Cari</button>
                <button class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Reset</button>
            </div>
        </div>
    </div>
</div>

<!-- Devices Table -->
<div class="overflow-x-auto rounded-lg shadow">
    <table class="min-w-full border border-gray-200 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Perangkat</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Serial Number</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Lembaga</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Pengguna</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Koneksi</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Terakhir Online</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 flex items-center gap-2">
                    <span class="text-purple-600 text-lg"><i class="fas fa-laptop"></i></span>
                    <span class="font-medium">EduBraille 1</span>
                </td>
                <td class="px-4 py-3">EDUE26AA4</td>
                <td class="px-4 py-3">Pengguna Mandiri<br><span class="text-xs text-gray-500">Individu</span></td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 flex items-center justify-center bg-purple-600 text-white rounded-full text-xs">B</span>
                        <div>
                            <p class="font-medium">Budi Santoso</p>
                            <p class="text-xs text-gray-500">budisantoso@gmail.com</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Aktif</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-600">Offline</span>
                </td>
                <td class="px-4 py-3 text-center text-gray-500 italic">Belum pernah</td>
                <td class="px-4 py-3 text-center space-x-2">
                    <button class="text-blue-600 hover:text-blue-800" title="Detail"><i class="fas fa-info-circle"></i></button>
                    <button class="text-green-600 hover:text-green-800" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($devices->hasPages())
<div class="pagination mt-4">
    {{ $devices->links() }}
</div>
@endif

<!-- Summary -->
<div class="mt-2 text-center text-gray-500 text-sm">
    Menampilkan {{ $devices->firstItem() ?? 0 }} - {{ $devices->lastItem() ?? 0 }} dari {{ $devices->total() }} perangkat
</div>

<script>
function pingDevice(deviceId) {
    fetch(`/admin/manajemen-perangkat/${deviceId}/ping`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => alert((data.success ? '✅ ' : '❌ ') + data.message))
    .catch(() => alert('❌ Terjadi kesalahan saat mengirim ping'));
}

function requestStatus(deviceId) {
    fetch(`/admin/manajemen-perangkat/${deviceId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => alert((data.success ? '✅ ' : '❌ ') + data.message))
    .catch(() => alert('❌ Terjadi kesalahan saat meminta status'));
}
</script>
@endsection