<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchOffering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campus_id',
        'service_id',
        'amount',
        'offering_date',
        'collection_method',
        'reference_number',
        'church_elder_id',
        'evangelism_leader_id',
        'secretary_id',
        'status',
        'handover_to_secretary_at',
        'notes',
        'leader_notes',
        'secretary_notes',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'offering_date' => 'date',
        'handover_to_secretary_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function churchElder()
    {
        return $this->belongsTo(User::class, 'church_elder_id');
    }

    public function service()
    {
        return $this->belongsTo(SundayService::class, 'service_id');
    }

    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes

    public function scopePendingSecretary($query)
    {
        return $query->where('status', 'pending_secretary');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
