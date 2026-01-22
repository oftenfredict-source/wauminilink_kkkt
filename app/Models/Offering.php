<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offering extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'campus_id',
        'evangelism_leader_id',
        'amount',
        'offering_date',
        'offering_type',
        'service_type',
        'service_id',
        'payment_method',
        'reference_number',
        'notes',
        'recorded_by',
        'is_verified',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'submitted_to_secretary',
        'submitted_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'offering_date' => 'date',
        'is_verified' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('offering_date', [$startDate, $endDate]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('offering_type', $type);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeAnonymous($query)
    {
        return $query->whereNull('member_id');
    }
}
