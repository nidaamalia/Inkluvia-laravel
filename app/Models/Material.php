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
        'status',
        'akses',
        'created_by',
        'total_halaman',
        'file_path',
        'braille_data_path'
    ];

    protected $casts = [];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->with('lembaga');
    }

    public function requests()
    {
        return $this->hasMany(MaterialRequest::class);
    }

    public function brailleContents()
    {
        return $this->hasMany(MaterialBrailleContent::class);
    }

    public function pages()
    {
        return $this->hasMany(MaterialPage::class);
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'published' => 'success',
            'review' => 'warning',
            'processing' => 'info',
            'draft' => 'secondary',
            'archived' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    public function getAksesBadgeColorAttribute()
    {
        return match($this->akses) {
            'public' => 'success',
            'premium' => 'warning',
            'restricted' => 'info',
            'private' => 'secondary',
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

    /**
     * Scope untuk mendapatkan materi yang dapat diakses oleh user
     * - Public: semua user dapat akses
     * - Private: hanya creator yang dapat akses
     * - Restricted: hanya user dari lembaga yang sama (kecuali pengguna mandiri)
     */
    public function scopeAccessibleBy($query, $user)
    {
        return $query->where(function($q) use ($user) {
            // Public materials - dapat diakses semua user
            $q->where('akses', 'public')
              // Private materials - hanya creator
              ->orWhere(function($subQ) use ($user) {
                  $subQ->where('akses', 'private')
                       ->where('created_by', $user->id);
              })
              // Restricted materials - hanya dari lembaga yang sama (kecuali pengguna mandiri)
              ->orWhere(function($subQ) use ($user) {
                  $subQ->where('akses', 'restricted');
                  
                  // Jika user memiliki lembaga dan bukan pengguna mandiri
                  if ($user->lembaga_id && $user->lembaga && $user->lembaga->type !== 'Individu') {
                      $subQ->whereHas('creator.lembaga', function($lembagaQuery) use ($user) {
                          $lembagaQuery->where('id', $user->lembaga_id);
                      });
                  } else {
                      // Jika user adalah pengguna mandiri, tidak dapat akses materi restricted
                      $subQ->whereRaw('1 = 0');
                  }
              });
        });
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

    public static function getAksesOptions()
    {
        return [
            'public' => 'Publik',
            'premium' => 'Premium',
            'restricted' => 'Terbatas',
            'private' => 'Privat'
        ];
    }
    
    public function getAksesDisplayAttribute()
    {
        return match($this->akses) {
            'public' => 'Publik',
            'premium' => 'Premium',
            'restricted' => 'Terbatas',
            'private' => 'Privat',
            default => 'Tidak Diketahui'
        };
    }
}