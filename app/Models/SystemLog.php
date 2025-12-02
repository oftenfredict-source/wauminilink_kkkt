<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level',
        'category',
        'action',
        'message',
        'context',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'device_name',
        'mac_address',
        'screen_resolution',
        'timezone',
        'language',
        'device_properties',
        'route',
        'method',
    ];

    protected $casts = [
        'context' => 'array',
        'device_properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by level
     */
    public function scopeLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}


