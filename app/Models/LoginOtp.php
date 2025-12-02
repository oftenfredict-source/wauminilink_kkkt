<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp_code',
        'email',
        'ip_address',
        'user_agent',
        'expires_at',
        'is_used',
        'used_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
        'attempts' => 'integer',
    ];

    /**
     * Get the user that owns this OTP
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Increment verification attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Clean up expired OTPs (static method for cleanup)
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now())
            ->orWhere(function ($query) {
                $query->where('is_used', true)
                      ->where('used_at', '<', now()->subDays(1));
            })
            ->delete();
    }
}



