<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'budget_name',
        'budget_type',
        'purpose',
        'primary_offering_type',
        'requires_approval',
        'fiscal_year',
        'start_date',
        'end_date',
        'total_budget',
        'allocated_amount',
        'spent_amount',
        'description',
        'status',
        'created_by',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason'
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'fiscal_year' => 'integer',
        'approved_at' => 'datetime',
        'requires_approval' => 'boolean',
    ];

    // Relationships
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function offeringAllocations()
    {
        return $this->hasMany(BudgetOfferingAllocation::class);
    }

    public function primaryOfferingAllocation()
    {
        return $this->hasOne(BudgetOfferingAllocation::class)->where('is_primary', true);
    }

    public function lineItems()
    {
        return $this->hasMany(BudgetLineItem::class)->orderBy('order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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

    public function scopeByFiscalYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('budget_type', $type);
    }

    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('status', 'active');
    }

    // Accessors

    /**
     * Get validated spent amount (Paid expenses)
     */
    public function getSpentAmountAttribute($value)
    {
        // Calculate spent amount from paid expenses
        // Only count expenses that are both approved and paid
        return $this->expenses()
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Get pending spent amount (Approved but not Paid expenses)
     * These are committed funds
     */
    public function getPendingSpentAmountAttribute()
    {
        return $this->expenses()
            ->where('status', '!=', 'paid')
            ->where(function ($q) {
                $q->where('approval_status', 'approved')
                    ->orWhere('approval_status', 'pending');
            })
            ->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_budget - $this->spent_amount;
    }

    public function getUtilizationPercentageAttribute()
    {
        if ($this->total_budget == 0)
            return 0;

        // Utilization should probably include committed funds to avoid overspending
        // But for now, let's keep it based on actual spent for the progress bar, 
        // maybe add a secondary bar? 
        // For now, standard behavior: spent / total
        return round(($this->spent_amount / $this->total_budget) * 100, 2);
    }

    public function getIsOverBudgetAttribute()
    {
        return $this->spent_amount > $this->total_budget;
    }

    public function getIsNearLimitAttribute()
    {
        $threshold = $this->total_budget * 0.9; // 90% threshold
        return $this->spent_amount >= $threshold && $this->spent_amount < $this->total_budget;
    }

    // Funding-related methods
    public function getTotalAllocatedFromOfferingsAttribute()
    {
        return $this->offeringAllocations()->sum('allocated_amount');
    }

    public function getTotalUsedFromOfferingsAttribute()
    {
        return $this->offeringAllocations()->sum('used_amount');
    }

    public function getRemainingFromOfferingsAttribute()
    {
        return $this->total_allocated_from_offerings - $this->total_used_from_offerings;
    }

    public function isFullyFunded()
    {
        return $this->total_allocated_from_offerings >= $this->total_budget;
    }

    public function getFundingPercentageAttribute()
    {
        if ($this->total_budget == 0)
            return 0;
        return round(($this->total_allocated_from_offerings / $this->total_budget) * 100, 2);
    }

    public function getOfferingTypeBreakdown()
    {
        return $this->offeringAllocations()
            ->selectRaw('offering_type, SUM(allocated_amount) as total_allocated, SUM(used_amount) as total_used')
            ->groupBy('offering_type')
            ->get();
    }
}
