<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'start_date',
        'end_date',
        'is_active',
        'is_pinned',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    /**
     * Get the user who created this announcement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all views for this announcement
     */
    public function views()
    {
        return $this->hasMany(AnnouncementView::class);
    }

    /**
     * Get members who have viewed this announcement
     */
    public function viewedByMembers()
    {
        return $this->belongsToMany(Member::class, 'announcement_views')
            ->withPivot('viewed_at')
            ->withTimestamps();
    }

    /**
     * Check if a specific member has viewed this announcement
     */
    public function isViewedBy($memberId)
    {
        return $this->views()->where('member_id', $memberId)->exists();
    }

    /**
     * Scope to get active announcements
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->toDateString();
        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', $now);
            });
    }

    /**
     * Scope to get pinned announcements
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Check if announcement is currently active
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now()->toDateString();
        
        if ($this->start_date && $this->start_date->toDateString() > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date->toDateString() <= $now) {
            return false;
        }

        return true;
    }
}
