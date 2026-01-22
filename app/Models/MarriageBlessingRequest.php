<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarriageBlessingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'husband_full_name',
        'wife_full_name',
        'phone_number',
        'email',
        'church_branch_id',
        'marriage_type',
        'marriage_date',
        'place_of_marriage',
        'marriage_certificate_number',
        'both_spouses_members',
        'membership_duration',
        'attended_marriage_counseling',
        'reason_for_blessing',
        'declaration_agreed',
        'evangelism_leader_id',
        'pastor_id',
        'status',
        'pastor_comments',
        'scheduled_blessing_date',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'scheduled_blessing_date' => 'date',
        'both_spouses_members' => 'boolean',
        'attended_marriage_counseling' => 'boolean',
        'declaration_agreed' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    public function pastor()
    {
        return $this->belongsTo(User::class, 'pastor_id');
    }

    public function churchBranch()
    {
        return $this->belongsTo(Campus::class, 'church_branch_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCounselingRequired($query)
    {
        return $query->where('status', 'counseling_required');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function requiresCounseling()
    {
        return $this->status === 'counseling_required';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
