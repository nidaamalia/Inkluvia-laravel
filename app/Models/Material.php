<?php

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
        'kelas',
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

    public function scopeByKelas($query, $kelas)
    {
        return $query->where('kelas', $kelas);
    }

    /**
     * Scope untuk mendapatkan materi yang dapat diakses oleh user di perpustakaan
     * 
     * RULES:
     * 1. Materi dari ADMIN → Tampil untuk SEMUA user
     * 2. Materi dari USER:
     *    - Private: TIDAK tampil di perpustakaan
     *    - Public: Tampil hanya untuk user dari lembaga yang sama
     */
    public function scopeAccessibleBy($query, $user)
    {
        return $query->where(function($q) use ($user) {
            // 1. Materi dari ADMIN - tampil untuk semua user
            $q->whereHas('creator', function($creatorQuery) {
                $creatorQuery->where('role', 'admin');
            });
            
            // 2. Materi PUBLIC dari USER dengan lembaga yang sama
            if ($user->lembaga_id && $user->lembaga && $user->lembaga->type !== 'Individu') {
                $q->orWhere(function($subQ) use ($user) {
                    $subQ->where('akses', 'public')
                         ->whereHas('creator', function($creatorQuery) use ($user) {
                             $creatorQuery->where('role', 'user')
                                          ->where('lembaga_id', $user->lembaga_id);
                         });
                });
            }
        });
    }

    public static function getKategoriOptions()
    {
        return [
            'matematika' => 'Matematika',
            'bahasa_indonesia' => 'Bahasa Indonesia',
            'bahasa_inggris' => 'Bahasa Inggris',
            'ipa' => 'IPA',
            'ips' => 'IPS',
            'agama' => 'Agama',
            'seni_budaya' => 'Seni Budaya',
            'penjas' => 'Pendidikan Jasmani',
            'umum' => 'Umum'
        ];
    }

    public static function getTingkatOptions()
    {
        return [
            'sd' => 'SD',
            'smp' => 'SMP',
            'sma' => 'SMA',
            'umum' => 'Umum'
        ];
    }

    public static function getKelasOptions()
    {
        return [
            
            // SD
            '1' => 'Kelas 1',
            '2' => 'Kelas 2',
            '3' => 'Kelas 3',
            '4' => 'Kelas 4',
            '5' => 'Kelas 5',
            '6' => 'Kelas 6',
            
            // SMP
            '7' => 'Kelas 7',
            '8' => 'Kelas 8',
            '9' => 'Kelas 9',
            
            // SMA
            '10' => 'Kelas 10',
            '11' => 'Kelas 11',
            '12' => 'Kelas 12',
            
            // Umum
            'semua' => 'Semua Kelas'
        ];
    }

    public static function getAksesOptions()
    {
        return [
            'private' => 'Privat (Hanya Saya)',
            'public' => 'Publik (Lembaga Saya)',
        ];
    }
    
    public function getAksesDisplayAttribute()
    {
        return match($this->akses) {
            'public' => 'Publik (Lembaga)',
            'private' => 'Privat',
            default => 'Tidak Diketahui'
        };
    }
    
    public function getKelasDisplayAttribute()
    {
        $kelasOptions = self::getKelasOptions();
        return $kelasOptions[$this->kelas] ?? $this->kelas ?? '-';
    }
}