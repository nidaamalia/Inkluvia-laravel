<?php
// app/Models/Material.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'tingkat',
        'file_path',
        'braille_data_path',
        'total_halaman',
        'status',
        'akses',
        'created_by',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requests()
    {
        return $this->hasMany(MaterialRequest::class);
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'published' => 'success',
            'review' => 'warning',
            'draft' => 'secondary',
            'archived' => 'danger',
            default => 'secondary'
        };
    }

    public function getAksesBadgeColorAttribute()
    {
        return match($this->akses) {
            'public' => 'success',
            'premium' => 'warning',
            'restricted' => 'danger',
            default => 'secondary'
        };
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    public static function getKategoriOptions()
    {
        return [
            'matematika' => 'Matematika',
            'bahasa' => 'Bahasa',
            'ipa' => 'IPA',
            'ips' => 'IPS',
            'agama' => 'Agama',
            'umum' => 'Umum'
        ];
    }

    public static function getTingkatOptions()
    {
        return [
            'paud' => 'PAUD',
            'sd' => 'SD',
            'smp' => 'SMP',
            'sma' => 'SMA',
            'perguruan_tinggi' => 'Perguruan Tinggi',
            'umum' => 'Umum'
        ];
    }
}