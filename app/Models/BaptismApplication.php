<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaptismApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'gender',
        'date_of_birth',
        'age',
        'phone_number',
        'email',
        'residential_address',
        'church_branch_id',
        'community_id',
        'previously_baptized',
        'previous_church_name',
        'previous_baptism_date',
        'attended_baptism_classes',
        'church_attendance_duration',
        'pastor_catechist_name',
        'marital_status',
        'parent_guardian_name',
        'guardian_phone',
        'guardian_email',
        'family_religious_background',
        'reason_for_baptism',
        'declaration_agreed',
        'photo_path',
        'recommendation_letter_path',
        'evangelism_leader_id',
        'pastor_id',
        'status',
        'pastor_comments',
        'scheduled_baptism_date',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'previous_baptism_date' => 'date',
        'scheduled_baptism_date' => 'date',
        'previously_baptized' => 'boolean',
        'attended_baptism_classes' => 'boolean',
        'declaration_agreed' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'age' => 'integer',
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

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
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

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
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

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
