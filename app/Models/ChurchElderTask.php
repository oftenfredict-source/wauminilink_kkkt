<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChurchElderTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'church_elder_id',
        'community_id',
        'member_id',
        'task_type',
        'task_title',
        'description',
        'task_date',
        'task_time',
        'location',
        'status',
        'outcome',
        'notes',
        'pastor_comments',
        'pastor_commented_at',
        'pastor_commented_by',
    ];

    protected $casts = [
        'task_date' => 'date',
        'task_time' => 'datetime',
        'pastor_commented_at' => 'datetime',
    ];

    /**
     * Get the church elder who created this task
     */
    public function churchElder()
    {
        return $this->belongsTo(User::class, 'church_elder_id');
    }

    /**
     * Get the community associated with this task
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the member associated with this task
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
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
