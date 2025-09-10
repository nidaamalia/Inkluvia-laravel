<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'type',
        'alamat',
        'deskripsi',
    ];

    /**
     * Relasi ke User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Accessor: icon berdasarkan tipe
     */
    public function getIconAttribute()
    {
        switch ($this->type) {
            case 'Individu':
                return 'fas fa-user';
            case 'Sekolah':
                return 'fas fa-school';
            case 'Lembaga':
                return 'fas fa-building';
            default:
                return 'fas fa-building';
        }
    }

    /**
     * Accessor: warna badge berdasarkan tipe
     */
    public function getBadgeColorAttribute()
    {
        switch ($this->type) {
            case 'Individu':
                return 'bg-success';
            case 'Sekolah':
                return 'bg-primary';
            case 'Lembaga':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Scope: filter berdasarkan tipe
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: search nama, alamat, deskripsi
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%");
        });
    }
}