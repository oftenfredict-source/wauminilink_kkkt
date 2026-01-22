<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvangelismTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'evangelism_leader_id',
        'campus_id',
        'member_id',
        'community_id',
        'task_type',
        'task_title',
        'description',
        'task_date',
        'task_time',
        'location',
        'status',
        'outcome',
        'notes',
        'sent_to_pastor',
        'sent_to_pastor_at',
        'pastor_comments',
        'pastor_commented_at',
        'pastor_commented_by',
    ];

    protected $casts = [
        'task_date' => 'date',
        'task_time' => 'datetime',
        'sent_to_pastor' => 'boolean',
        'sent_to_pastor_at' => 'datetime',
        'pastor_commented_at' => 'datetime',
    ];

    /**
     * Get the evangelism leader who created this task
     */
    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    /**
     * Get the campus associated with this task
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the member associated with this task
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the community associated with this task
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the pastor who commented on this task
     */
    public function pastorCommenter()
    {
        return $this->belongsTo(User::class, 'pastor_commented_by');
    }

    /**
     * Get task type display name
     */
    public function getTaskTypeDisplayAttribute()
    {
        return match($this->task_type) {
            'member_visit' => 'Member Visit',
            'prayer_request' => 'Prayer Request',
            'follow_up' => 'Follow Up',
            'outreach' => 'Outreach',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->task_type)),
        };
    }
}
