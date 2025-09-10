<?php
// app/Models/Device.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_device',
        'serial_number',
        'lembaga_id',
        'status',
        'last_connection',
        'keterangan'
    ];

    protected $casts = [
        'last_connection' => 'datetime',
    ];

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'aktif' => 'success',
            'tidak_aktif' => 'secondary',
            'maintenance' => 'warning',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            'maintenance' => 'Maintenance',
            default => 'Unknown'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByLembaga($query, $lembagaId)
    {
        return $query->where('lembaga_id', $lembagaId);
    }
}