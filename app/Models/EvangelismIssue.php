<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvangelismIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'evangelism_leader_id',
        'campus_id',
        'community_id',
        'issue_type',
        'priority',
        'title',
        'description',
        'status',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'pastor_comments',
        'pastor_commented_at',
        'pastor_commented_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'pastor_commented_at' => 'datetime',
    ];

    /**
     * Get the evangelism leader who created this issue
     */
    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    /**
     * Get the campus associated with this issue
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the community associated with this issue
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the user who resolved this issue
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get the pastor who commented on this issue
     */
    public function pastorCommenter()
    {
        return $this->belongsTo(User::class, 'pastor_commented_by');
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeAttribute()
    {
        return match($this->priority) {
            'low' => 'bg-secondary',
            'medium' => 'bg-warning',
            'high' => 'bg-danger',
            'urgent' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Get priority display name
     */
    public function getPriorityDisplayAttribute()
    {
        return ucfirst($this->priority ?? 'low');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'open' => 'bg-danger',
            'in_progress' => 'bg-warning',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }
}
