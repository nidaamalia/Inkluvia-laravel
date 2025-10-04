@extends('layouts.admin')

@section('title', 'Edit Perangkat')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.kelola-perangkat') }}" 
           class="text-primary hover:text-primary-dark transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Perangkat</h1>
            <p class="text-sm text-gray-600 mt-1">Edit data perangkat {{ $device->nama_device }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 sm:p-6">
                    <form method="POST" action="{{ route('admin.kelola-perangkat.update', $device) }}" id="deviceForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nama Perangkat -->
                        <div class="mb-6">
                            <label for="nama_device" class="block text-sm font-semibold text-gray-900 mb-2">
                                Nama Perangkat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nama_device" 
                                   name="nama_device" 
                                   class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('nama_device') border-red-500 @enderror" 
                                   value="{{ old('nama_device', $device->nama_device) }}" 
                                   required 
                                   placeholder="Contoh: EduBraille Lab 1">
                            @error('nama_device')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Serial Number -->
                        <div class="mb-6">
                            <label for="serial_number" class="block text-sm font-semibold text-gray-900 mb-2">
                                Serial Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="serial_number" 
                                   name="serial_number" 
                                   class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('serial_number') border-red-500 @enderror" 
                                   value="{{ old('serial_number', $device->serial_number) }}" 
                                   required
                                   placeholder="Format: EDU + 6 digit hex">
                            <p class="mt-2 text-xs text-gray-500">
                                Format: EDU + 6 digit hex (contoh: EDU1A2B3C)
                            </p>
                            @error('serial_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Lembaga & User -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="lembaga_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Lembaga <span class="text-red-500">*</span>
                                </label>
                                <select id="lembaga_id" 
                                        name="lembaga_id" 
                                        class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white @error('lembaga_id') border-red-500 @enderror" 
                                        required 
                                        onchange="handleLembagaChange()"
                                        style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                    <option value="">Pilih Lembaga</option>
                                    @foreach($lembagas as $lembaga)
                                    <option value="{{ $lembaga->id }}" data-type="{{ $lembaga->type }}" 
                                            {{ old('lembaga_id', $device->lembaga_id) == $lembaga->id ? 'selected' : '' }}>
                                        {{ $lembaga->nama }} ({{ $lembaga->type }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('lembaga_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="user_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Pengguna 
                                    <span id="user_required" class="text-red-500 hidden">*</span>
                                </label>
                                <select id="user_id" 
                                        name="user_id" 
                                        class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white @error('user_id') border-red-500 @enderror"
                                        style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                    <option value="">Pilih Pengguna</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $device->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_lengkap }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                                <p id="user_help" class="mt-2 text-xs text-gray-500 hidden">
                                    Wajib diisi untuk lembaga individu
                                </p>
                                @error('user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Status & Capacity -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status" 
                                        name="status" 
                                        class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white @error('status') border-red-500 @enderror" 
                                        required
                                        style="background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%238B5CF6%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226,9 12,15 18,9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                                    <option value="">Pilih Status</option>
                                    <option value="aktif" {{ old('status', $device->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak_aktif" {{ old('status', $device->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="maintenance" {{ old('status', $device->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="character_capacity" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Jumlah Karakter <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="character_capacity" 
                                       name="character_capacity" 
                                       class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('character_capacity') border-red-500 @enderror" 
                                       value="{{ old('character_capacity', $device->character_capacity ?? 40) }}" 
                                       min="1" 
                                       max="20" 
                                       required
                                       placeholder="Jumlah karakter">
                                <p class="mt-2 text-xs text-gray-500">
                                    Masukkan jumlah karakter yang dapat ditampilkan.
                                </p>
                                @error('character_capacity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-6">
                            <label for="keterangan" class="block text-sm font-semibold text-gray-900 mb-2">
                                Keterangan
                            </label>
                            <textarea id="keterangan" 
                                      name="keterangan" 
                                      class="w-full px-4 py-3 border-2 border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-y @error('keterangan') border-red-500 @enderror" 
                                      rows="3" 
                                      placeholder="Keterangan tambahan tentang perangkat">{{ old('keterangan', $device->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Device Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-bold text-blue-900 mb-3">Informasi Perangkat</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-blue-800">
                                <div>
                                    <strong>Dibuat:</strong> {{ $device->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <strong>Terakhir Update:</strong> {{ $device->updated_at->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <strong>Terakhir Online:</strong> 
                                    {{ $device->last_connection ? $device->last_connection->format('d/m/Y H:i') : 'Belum pernah' }}
                                </div>
                                <div>
                                    <strong>Status Koneksi:</strong> 
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $device->connection_status === 'online' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($device->connection_status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($device->device_info)
                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <strong class="text-xs text-blue-900">Device Info:</strong>
                                <div class="bg-white rounded-lg p-2 mt-2">
                                    <div class="flex flex-wrap gap-3 text-xs">
                                        @foreach($device->device_info as $key => $value)
                                        <div>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.kelola-perangkat') }}" 
                               class="w-full sm:w-auto px-6 py-3 text-center border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>Update Perangkat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-6">
                <div class="p-4 sm:p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-eye text-primary mr-2"></i>
                        Preview Perangkat
                    </h4>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-laptop text-white text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 truncate" id="preview-nama">{{ $device->nama_device }}</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    <code class="bg-gray-200 px-2 py-0.5 rounded" id="preview-serial">{{ $device->serial_number }}</code>
                                </div>
                                <div class="text-xs text-gray-600 mt-2" id="preview-lembaga">{{ $device->lembaga->nama ?? 'Lembaga' }}</div>
                                <div class="text-xs text-gray-600" id="preview-user">{{ $device->user ? '👤 ' . $device->user->nama_lengkap : '' }}</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    <strong>Kapasitas:</strong> <span id="preview-capacity">{{ $device->character_capacity ?? '-' }}</span> karakter
                                </div>
                                <div class="mt-3">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ 
                                        $device->status === 'aktif' ? 'bg-green-100 text-green-700' :
                                        ($device->status === 'tidak_aktif' ? 'bg-red-100 text-red-700' :
                                        ($device->status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-700'))
                                    }}" id="preview-status">{{ ucfirst(str_replace('_', ' ', $device->status)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaInput = document.getElementById('nama_device');
    const serialInput = document.getElementById('serial_number');
    const lembagaSelect = document.getElementById('lembaga_id');
    const userSelect = document.getElementById('user_id');
    const statusSelect = document.getElementById('status');
    const capacityInput = document.getElementById('character_capacity');
    const userRequired = document.getElementById('user_required');
    const userHelp = document.getElementById('user_help');
    
    const previewNama = document.getElementById('preview-nama');
    const previewSerial = document.getElementById('preview-serial');
    const previewLembaga = document.getElementById('preview-lembaga');
    const previewUser = document.getElementById('preview-user');
    const previewStatus = document.getElementById('preview-status');
    const previewCapacity = document.getElementById('preview-capacity');
    
    function updatePreview() {
        previewNama.textContent = namaInput.value || 'Nama Perangkat';
        previewSerial.textContent = serialInput.value || 'Serial Number';
        
        const selectedLembaga = lembagaSelect.options[lembagaSelect.selectedIndex];
        previewLembaga.textContent = selectedLembaga.text !== 'Pilih Lembaga' ? selectedLembaga.text : 'Lembaga';
        
        const selectedUser = userSelect.options[userSelect.selectedIndex];
        if (selectedUser && selectedUser.value) {
            previewUser.textContent = `👤 ${selectedUser.text}`;
        } else {
            previewUser.textContent = '';
        }

        previewCapacity.textContent = capacityInput.value || '-';
        
        const selectedStatus = statusSelect.options[statusSelect.selectedIndex];
        previewStatus.textContent = selectedStatus.text !== 'Pilih Status' ? selectedStatus.text : 'Status';
        
        const statusValue = statusSelect.value;
        previewStatus.className = 'inline-block px-3 py-1 text-xs font-medium rounded-full ' + (
            statusValue === 'aktif' ? 'bg-green-100 text-green-700' :
            statusValue === 'tidak_aktif' ? 'bg-red-100 text-red-700' :
            statusValue === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-700'
        );
    }
    
    namaInput.addEventListener('input', updatePreview);
    serialInput.addEventListener('input', updatePreview);
    lembagaSelect.addEventListener('change', updatePreview);
    userSelect.addEventListener('change', updatePreview);
    statusSelect.addEventListener('change', updatePreview);
    capacityInput.addEventListener('input', updatePreview);
    
    handleLembagaChange();
});

function handleLembagaChange() {
    const lembagaSelect = document.getElementById('lembaga_id');
    const userSelect = document.getElementById('user_id');
    const userRequired = document.getElementById('user_required');
    const userHelp = document.getElementById('user_help');
    
    const selectedOption = lembagaSelect.options[lembagaSelect.selectedIndex];
    const lembagaId = selectedOption.value;
    const lembagaType = selectedOption.getAttribute('data-type');
    
    if (!lembagaId) {
        userRequired.classList.add('hidden');
        userHelp.classList.add('hidden');
        return;
    }
    
    if (lembagaType === 'Individu') {
        userRequired.classList.remove('hidden');
        userHelp.classList.remove('hidden');
        userSelect.required = true;
    } else {
        userRequired.classList.add('hidden');
        userHelp.classList.add('hidden');
        userSelect.required = false;
    }
    
    const currentUserId = userSelect.value;
    
    fetch(`/admin/manajemen-perangkat/users-by-lembaga?lembaga_id=${lembagaId}`)
        .then(response => response.json())
        .then(users => {
            userSelect.innerHTML = '<option value="">Pilih Pengguna</option>';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.nama_lengkap} (${user.email})`;
                
                if (user.id == currentUserId) {
                    option.selected = true;
                }
                
                userSelect.appendChild(option);
            });
            
            // Trigger preview update
            const event = new Event('change');
            userSelect.dispatchEvent(event);
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            userSelect.innerHTML = '<option value="">Error loading users</option>';
        });
}
</script>
@endsection