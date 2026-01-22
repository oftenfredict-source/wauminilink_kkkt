<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvangelismReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'evangelism_leader_id',
        'campus_id',
        'community_id',
        'title',
        'content',
        'report_date',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the evangelism leader who created this report
     */
    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    /**
     * Get the campus associated with this report
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the community associated with this report
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the user who reviewed this report
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
