<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityOffering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'community_id',
        'service_id',
        'service_type',
        'offering_type',
        'amount',
        'amount_umoja',
        'amount_jengo',
        'amount_ahadi',
        'amount_other',
        'offering_date',
        'collection_method',
        'reference_number',
        'church_elder_id',
        'evangelism_leader_id',
        'secretary_id',
        'status',
        'handover_to_evangelism_at',
        'handover_to_secretary_at',
        'notes',
        'elder_notes',
        'leader_notes',
        'secretary_notes',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    const TYPE_UMOJA = 'sadaka_umoja';
    const TYPE_JENGO = 'sadaka_jengo';
    const TYPE_AHADI = 'sadaka_ahadi';
    const TYPE_TITHE = 'tithe';
    const TYPE_OTHER = 'other';
    const TYPE_GENERAL = 'general';
    const TYPE_COMBO = 'sunday_offering';

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_umoja' => 'decimal:2',
        'amount_jengo' => 'decimal:2',
        'amount_ahadi' => 'decimal:2',
        'amount_other' => 'decimal:2',
        'offering_date' => 'date',
        'handover_to_evangelism_at' => 'datetime',
        'handover_to_secretary_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function churchElder()
    {
        return $this->belongsTo(User::class, 'church_elder_id');
    }

    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function service()
    {
        return $this->belongsTo(SundayService::class, 'service_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes for easy filtering

    public function scopePendingEvangelism($query)
    {
        return $query->where('status', 'pending_evangelism');
    }

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

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeMidWeekServices($query)
    {
        return $query->whereNotNull('service_id')
            ->whereIn('service_type', ['prayer_meeting', 'bible_study', 'youth_service', 'women_fellowship', 'men_fellowship']);
    }

    public function items()
    {
        return $this->hasMany(CommunityOfferingItem::class);
    }
}
