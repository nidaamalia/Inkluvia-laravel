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
                    <p class="text-xl font-bold">{{ $stats['total_devices'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-400 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-wifi text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Online</p>
                    <p class="text-xl font-bold">{{ $stats['online_devices'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-blue-400 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Aktif</p>
                    <p class="text-xl font-bold">{{ $stats['active_devices'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 text-white p-4 rounded-xl shadow">
            <div class="flex items-center space-x-3">
                <i class="fas fa-tools text-2xl"></i>
                <div>
                    <p class="text-sm opacity-80">Maintenance</p>
                    <p class="text-xl font-bold">{{ $stats['maintenance_devices'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-xl shadow mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari perangkat..."
                   class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
            <select name="status" class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            <select name="connection" class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
                <option value="">Semua Koneksi</option>
                <option value="online" {{ request('connection') === 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ request('connection') === 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
            <select name="lembaga" class="border rounded-lg px-3 py-2 w-full focus:ring-primary focus:border-primary">
                <option value="">Semua Lembaga</option>
                @foreach($lembagas as $lembaga)
                <option value="{{ $lembaga->id }}" {{ (string) request('lembaga') === (string) $lembaga->id ? 'selected' : '' }}>
                    {{ $lembaga->nama }} ({{ $lembaga->type }})
                </option>
                @endforeach
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">Cari</button>
                <a href="{{ route('admin.kelola-perangkat') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 flex items-center justify-center">Reset</a>
            </div>
        </form>
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
            @forelse($devices as $device)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 flex items-center gap-2">
                    <span class="text-purple-600 text-lg"><i class="fas fa-laptop"></i></span>
                    <div>
                        <p class="font-medium text-gray-900">{{ $device->nama_device }}</p>
                        <p class="text-xs text-gray-500">Dibuat {{ $device->created_at?->format('d M Y') }}</p>
                    </div>
                </td>
                <td class="px-4 py-3 font-mono text-sm">{{ $device->serial_number }}</td>
                <td class="px-4 py-3">
                    @if($device->lembaga)
                        <div class="font-medium text-gray-900">{{ $device->lembaga->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $device->lembaga->type }}</div>
                    @else
                        <span class="text-xs text-gray-500 italic">Tidak terhubung</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($device->user)
                        <div class="font-medium text-gray-900">{{ $device->user->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500">{{ $device->user->email }}</div>
                    @else
                        <span class="text-xs text-gray-500 italic">Belum ada pengguna</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @php
                        $statusMap = [
                            'aktif' => 'bg-green-100 text-green-700',
                            'tidak_aktif' => 'bg-red-100 text-red-700',
                            'maintenance' => 'bg-yellow-100 text-yellow-700'
                        ];
                        $statusClass = $statusMap[$device->status] ?? 'bg-gray-200 text-gray-700';
                    @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    @php
                        $connection = $device->connection_status;
                        $connectionClass = $connection === 'online' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                    @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $connectionClass }}">
                        {{ ucfirst($connection) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-gray-600">
                    {{ $device->last_connection ? $device->last_connection->diffForHumans() : 'Belum pernah' }}
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" class="text-blue-600 hover:text-blue-800"
                                onclick="requestStatus({{ $device->id }})" title="Request status">
                            <i class="fas fa-satellite-dish"></i>
                        </button>
                        <button type="button" class="text-indigo-600 hover:text-indigo-800"
                                onclick="pingDevice({{ $device->id }})" title="Ping">
                            <i class="fas fa-broadcast-tower"></i>
                        </button>
                        <a href="{{ route('admin.kelola-perangkat.edit', $device) }}" class="text-green-600 hover:text-green-800" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.kelola-perangkat.destroy', $device) }}" method="POST" class="inline" onsubmit="return confirm('Hapus perangkat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    Belum ada perangkat. Klik "Tambah Perangkat" untuk menambahkan perangkat baru.
                </td>
            </tr>
            @endforelse
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