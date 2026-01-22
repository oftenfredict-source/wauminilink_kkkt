<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnToFellowshipRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'gender',
        'date_of_birth',
        'phone_number',
        'email',
        'church_branch_id',
        'previously_member',
        'previous_church_branch',
        'period_away',
        'reason_for_leaving',
        'reason_for_returning',
        'declaration_agreed',
        'evangelism_leader_id',
        'pastor_id',
        'status',
        'pastor_comments',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'previously_member' => 'boolean',
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

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
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
