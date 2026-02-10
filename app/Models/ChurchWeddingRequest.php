<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChurchWeddingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'groom_full_name',
        'groom_date_of_birth',
        'groom_phone_number',
        'groom_email',
        'bride_full_name',
        'bride_date_of_birth',
        'bride_phone_number',
        'bride_email',
        'church_branch_id',
        'both_baptized',
        'both_confirmed',
        'membership_duration',
        'pastor_catechist_name',
        'preferred_wedding_date',
        'preferred_church',
        'expected_guests',
        'attended_premarital_counseling',
        'groom_baptism_certificate_path',
        'bride_baptism_certificate_path',
        'groom_confirmation_certificate_path',
        'bride_confirmation_certificate_path',
        'groom_birth_certificate_path',
        'bride_birth_certificate_path',
        'marriage_notice_path',
        'declaration_agreed',
        'evangelism_leader_id',
        'pastor_id',
        'status',
        'pastor_comments',
        'wedding_approval_date',
        'confirmed_wedding_date',
        'scheduled_meeting_date',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'groom_date_of_birth' => 'date',
        'bride_date_of_birth' => 'date',
        'preferred_wedding_date' => 'date',
        'wedding_approval_date' => 'date',
        'confirmed_wedding_date' => 'date',
        'both_baptized' => 'boolean',
        'both_confirmed' => 'boolean',
        'attended_premarital_counseling' => 'boolean',
        'declaration_agreed' => 'boolean',
        'expected_guests' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'scheduled_meeting_date' => 'datetime',
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

    public function scopeDocumentsRequired($query)
    {
        return $query->where('status', 'documents_required');
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

    public function requiresDocuments()
    {
        return $this->status === 'documents_required';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }
}
