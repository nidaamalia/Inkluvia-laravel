<?php
// app/Models/MaterialRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_materi',
        'deskripsi',
        'kategori',
        'tingkat',
        'prioritas',
        'status',
        'requested_by',
        'assigned_to',
        'material_id',
        'admin_notes',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who made the request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the admin user assigned to handle this request
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the material created from this request (if any)
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
    
    /**
     * Scope a query to only include requests made by a specific user
     */
    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }
    
    /**
     * Scope a query to only include requests with a specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Status constants for better code readability
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // Priority constants
    public const PRIORITY_LOW = 'rendah';
    public const PRIORITY_MEDIUM = 'sedang';
    public const PRIORITY_HIGH = 'tinggi';

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary'
        };
    }
    
    /**
     * Get the display name for the status
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => $this->status
        };
    }

    public function getPrioritasBadgeColorAttribute()
    {
        return match($this->prioritas) {
            'tinggi' => 'danger',
            'sedang' => 'warning',
            'rendah' => 'success',
            default => 'secondary'
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    /**
     * Get all available status options with their display names
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak'
        ];
    }
    
    /**
     * Get all available priority options with their display names
     */
    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'Rendah',
            self::PRIORITY_MEDIUM => 'Sedang',
            self::PRIORITY_HIGH => 'Tinggi'
        ];
    }

    public static function getPrioritasOptions()
    {
        return [
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi'
        ];
    }
}