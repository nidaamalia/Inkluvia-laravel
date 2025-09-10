<?php
// app/Models/BrailleContent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrailleContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'karakter',
        'braille_pattern',
        'kategori',
        'deskripsi',
        'urutan',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function getBrailleDotsAttribute()
    {
        $pattern = $this->braille_pattern;
        $dots = [];
        
        for ($i = 0; $i < 6; $i++) {
            $dots[] = $pattern[$i] === '1';
        }
        
        return $dots;
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('karakter');
    }

    public static function getKategoriOptions()
    {
        return [
            'huruf' => 'Huruf Latin',
            'angka' => 'Angka',
            'operator' => 'Operator Matematika',
            'hijaiyah' => 'Huruf Hijaiyah',
            'tanda_baca' => 'Tanda Baca',
            'khusus' => 'Karakter Khusus'
        ];
    }
}