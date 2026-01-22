<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class BereavementEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'deceased_name',
        'family_details',
        'related_departments',
        'incident_date',
        'contribution_start_date',
        'contribution_end_date',
        'status',
        'notes',
        'fund_usage',
        'created_by',
        'community_id',
        'closed_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'contribution_start_date' => 'date',
        'contribution_end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function contributions()
    {
        return $this->hasMany(BereavementContribution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'open')
            ->where('contribution_end_date', '>=', Carbon::today());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'open')
            ->where('contribution_end_date', '<', Carbon::today());
    }

    // Helper methods
    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isExpired()
    {
        return $this->isOpen() && $this->contribution_end_date < Carbon::today();
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->isClosed()) {
            return 0;
        }
        
        $days = Carbon::today()->diffInDays($this->contribution_end_date, false);
        return max(0, $days);
    }

    public function getTotalContributionsAttribute()
    {
        $sum = $this->contributions()
            ->where('has_contributed', true)
            ->sum('contribution_amount');
        return $sum ?? 0;
    }

    public function getContributorsCountAttribute()
    {
        return $this->contributions()
            ->where('has_contributed', true)
            ->count();
    }

    public function getNonContributorsCountAttribute()
    {
        return $this->contributions()
            ->where('has_contributed', false)
            ->count();
    }

    public function close()
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => Carbon::now(),
        ]);
    }
}

