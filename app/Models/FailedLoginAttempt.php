<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'mac_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'device_name',
        'failure_reason',
        'ip_blocked',
        'ip_blocked_at',
        'ip_unblocked_at',
    ];

    protected $casts = [
        'ip_blocked' => 'boolean',
        'ip_blocked_at' => 'datetime',
        'ip_unblocked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to filter by IP address
     */
    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope to get blocked IPs
     */
    public function scopeBlocked($query)
    {
        return $query->where('ip_blocked', true);
    }

    /**
     * Scope to get recent attempts
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}


