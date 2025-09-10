@extends('layouts.admin')

@section('title', 'Edit Lembaga')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('admin.manajemen-lembaga') }}" class="btn btn-secondary" style="margin-right: 1rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div>
            <h1 class="page-title">Edit Lembaga</h1>
            <p class="page-subtitle">Edit data lembaga {{ $lembaga->nama }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.manajemen-lembaga.update', $lembaga) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nama" class="form-label">Nama Lembaga <span style="color: var(--danger);">*</span></label>
            <input type="text" id="nama" name="nama" class="form-input" 
                   value="{{ old('nama', $lembaga->nama) }}" required 
                   placeholder="Masukkan nama lembaga">
        </div>
        
        <div class="form-group">
            <label for="type" class="form-label">Type Lembaga <span style="color: var(--danger);">*</span></label>
            <input type="text" id="type" name="type" class="form-input" 
                   value="{{ old('type', $lembaga->type) }}" required 
                   placeholder="Contoh: Sekolah, Universitas, Yayasan, dll"
                   list="type-suggestions">
            <datalist id="type-suggestions">
                <option value="Sekolah Dasar">
                <option value="Sekolah Menengah Pertama">
                <option value="Sekolah Menengah Atas">
                <option value="Universitas">
                <option value="Institut">
                <option value="Akademi">
                <option value="Yayasan">
                <option value="Panti Sosial">
                <option value="Rumah Sakit">
                <option value="Klinik">
                <option value="Pusat Rehabilitasi">
                <option value="Organisasi Non-Profit">
            </datalist>
        </div>
        
        <div class="form-group">
            <label for="alamat" class="form-label">Alamat <span style="color: var(--danger);">*</span></label>
            <textarea id="alamat" name="alamat" class="form-input" rows="4" 
                      required placeholder="Masukkan alamat lengkap lembaga">{{ old('alamat', $lembaga->alamat) }}</textarea>
        </div>
        
        <!-- Lembaga Info -->
        <div style="background: var(--gray-50); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <h4 style="margin-bottom: 0.5rem; color: var(--text-dark);">Informasi Lembaga</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem; color: var(--text-light);">
                <div>
                    <strong>Dibuat:</strong> {{ $lembaga->created_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Terakhir Update:</strong> {{ $lembaga->updated_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Total Pengguna:</strong> {{ $lembaga->users()->count() }} pengguna
                </div>
            </div>
        </div>
        
        <!-- Preview Card -->
        <div style="background: var(--gray-50); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 1rem; color: var(--text-dark); display: flex; align-items: center;">
                <i class="fas fa-eye" style="margin-right: 0.5rem;"></i>
                Preview Lembaga
            </h4>
            <div style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
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
                        <div style="font-weight: 600;" id="preview-nama">{{ $lembaga->nama }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-light);">
                            <span class="badge badge-primary" id="preview-type">{{ $lembaga->type }}</span>
                        </div>
                        <div style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.25rem;" id="preview-alamat">{{ $lembaga->alamat }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users in this Lembaga -->
        @if($lembaga->users()->count() > 0)
        <div style="background: var(--gray-50); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 1rem; color: var(--text-dark); display: flex; align-items: center;">
                <i class="fas fa-users" style="margin-right: 0.5rem;"></i>
                Pengguna di Lembaga Ini ({{ $lembaga->users()->count() }})
            </h4>
            <div style="display: grid; gap: 0.5rem; max-height: 200px; overflow-y: auto;">
                @foreach($lembaga->users()->limit(10)->get() as $user)
                <div style="background: white; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center;">
                        <div class="user-avatar" style="width: 24px; height: 24px; font-size: 0.625rem; margin-right: 0.5rem;">
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $user->nama_lengkap }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);">{{ $user->email }}</div>
                        </div>
                    </div>
                    <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-success' }}" style="font-size: 0.625rem;">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @endforeach
                @if($lembaga->users()->count() > 10)
                <div style="text-align: center; color: var(--text-light); font-size: 0.875rem; padding: 0.5rem;">
                    dan {{ $lembaga->users()->count() - 10 }} pengguna lainnya...
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('admin.manajemen-lembaga') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Lembaga
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaInput = document.getElementById('nama');
    const typeInput = document.getElementById('type');
    const alamatInput = document.getElementById('alamat');
    
    const previewNama = document.getElementById('preview-nama');
    const previewType = document.getElementById('preview-type');
    const previewAlamat = document.getElementById('preview-alamat');
    
    function updatePreview() {
        previewNama.textContent = namaInput.value || 'Nama Lembaga';
        previewType.textContent = typeInput.value || 'Type';
        previewAlamat.textContent = alamatInput.value || 'Alamat lembaga';
    }
    
    namaInput.addEventListener('input', updatePreview);
    typeInput.addEventListener('input', updatePreview);
    alamatInput.addEventListener('input', updatePreview);
});
</script>
@endsection