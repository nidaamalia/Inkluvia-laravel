<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLibraryMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'material_file' => 'required|file|mimetypes:text/plain,text/markdown,application/pdf',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:255',
            'tingkat' => 'nullable|string|max:255',
            'gunakan_ai' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'material_file.required' => 'Silakan pilih berkas materi yang ingin diunggah.',
            'material_file.file' => 'Berkas materi harus berupa file yang valid.',
            'material_file.mimetypes' => 'Format berkas tidak didukung. Gunakan TXT, MD, atau PDF.',
        ];
    }
}
