@extends('layouts.admin')

@section('title', 'Tambah Lembaga Baru')

@section('content')
<div class="w-full px-6">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.manajemen-lembaga') }}" 
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
            ← Kembali
        </a>
        <h1 class="text-2xl font-bold">Tambah Lembaga Baru</h1>
    </div>

    <form action="{{ route('admin.manajemen-lembaga') }}" method="POST" class="bg-white shadow-md rounded-xl p-6 w-full">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nama Lembaga -->
            <div class="col-span-2">
                <label class="block text-gray-700 font-medium mb-2">Nama Lembaga *</label>
                <input type="text" name="nama" 
                       placeholder="Masukkan nama lembaga"
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300">
            </div>

            <!-- Type Lembaga -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">Type Lembaga *</label>
                <select name="type" 
                        class="w-full px-4 py-2 border rounded-lg bg-white shadow-sm focus:ring focus:ring-purple-300">
                    <option disabled selected>Pilih Type Lembaga</option>
                    <option value="Sekolah">Sekolah</option>
                    <option value="Lembaga">Lembaga</option>
                    <option value="Individu">Individu</option>
                </select>
            </div>

            <!-- Alamat -->
            <div class="col-span-2">
                <label class="block text-gray-700 font-medium mb-2">Alamat *</label>
                <textarea name="alamat" rows="3"
                          placeholder="Masukkan alamat lembaga"
                          class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300"></textarea>
            </div>

            <!-- Deskripsi -->
            <div class="col-span-2">
                <label class="block text-gray-700 font-medium mb-2">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="3"
                          placeholder="Tuliskan deskripsi lembaga"
                          class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300"></textarea>
            </div>

            <!-- Email Lembaga -->
            <div class="col-span-2">
                <label class="block text-gray-700 font-medium mb-2">Email Lembaga (untuk kirim kunci)</label>
                <input type="email" name="email" 
                       placeholder="contoh: admin@lembaga.id"
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-purple-300">
            </div>
            
            <!-- Kirim email berisi kunci setelah simpan -->
            <div class="col-span-2 flex items-center space-x-2">
                <input id="send_key" type="checkbox" name="send_key" value="1" class="h-4 w-4 text-purple-600 border-gray-300 rounded">
                <label for="send_key" class="text-gray-700">Kirim email berisi kunci login setelah disimpan</label>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end mt-6 space-x-3">
            <a href="{{ route('admin.manajemen-lembaga') }}" 
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Batal</a>
            <button type="submit" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Simpan Lembaga
            </button>
        </div>
    </form>
</div>
@endsection