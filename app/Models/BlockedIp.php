<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_by',
        'blocked_at',
        'unblocked_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'blocked_at' => 'datetime',
        'unblocked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who blocked this IP
     */
    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    /**
     * Scope to get active blocks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if an IP is blocked
     */
    public static function isBlocked(string $ip): bool
    {
        return self::where('ip_address', $ip)
            ->where('is_active', true)
            ->exists();
    }
}


