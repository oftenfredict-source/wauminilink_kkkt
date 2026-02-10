<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetOfferingAllocation;
use App\Models\Offering;
use App\Models\CommunityOffering;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetFundingService
{
    /**
     * Get available offering amounts by type
     */
    public function getAvailableOfferingAmounts()
    {
        // Standard individual offerings
        $individualOfferings = Offering::selectRaw('offering_type, SUM(amount) as total_amount')
            ->where('approval_status', 'approved')
            ->groupBy('offering_type')
            ->pluck('total_amount', 'offering_type')
            ->toArray();

        // Community offerings (collections from communities/mitaa)
        $communityTotals = CommunityOffering::selectRaw('
                SUM(amount) as general,
                SUM(amount_umoja) as thanksgiving,
                SUM(amount_jengo) as building_fund,
                SUM(amount_other) as other
            ')
            ->where('status', 'completed')
            ->first();

        // Merge the two sources
        $combined = $individualOfferings;

        if ($communityTotals) {
            $combined['general'] = ($combined['general'] ?? 0) + ($communityTotals->general ?? 0);
            $combined['thanksgiving'] = ($combined['thanksgiving'] ?? 0) + ($communityTotals->thanksgiving ?? 0);
            $combined['building_fund'] = ($combined['building_fund'] ?? 0) + ($communityTotals->building_fund ?? 0);
            $combined['other'] = ($combined['other'] ?? 0) + ($communityTotals->other ?? 0);
        }

        return $combined;
    }

    /**
     * Get available amounts after considering existing allocations
     * Available = Total Offerings - Allocated Amount
     * Once funds are allocated to a budget, they are committed and should be subtracted from available,
     * regardless of whether they've been used yet. The "used" amount is just tracking how much of the
     * allocated amount has been spent, but the allocated amount itself is what reduces availability.
     */
    public function getAvailableAmountsAfterAllocations()
    {
        $totalOfferings = $this->getAvailableOfferingAmounts();

        // Get allocated amounts by offering type
        // Once allocated, funds are committed and should be subtracted from available
        $allocationData = BudgetOfferingAllocation::selectRaw('offering_type, SUM(allocated_amount) as total_allocated')
            ->whereHas('budget', function ($query) {
                $query->where('status', 'active');
            })
            ->groupBy('offering_type')
            ->get();

        $availableAmounts = [];
        foreach ($totalOfferings as $type => $total) {
            $allocation = $allocationData->firstWhere('offering_type', $type);
            if ($allocation) {
                // Available = Total - Allocated
                // Example: Total = 400,000, Allocated = 200,000, Used = 200,000
                // Available = 400,000 - 200,000 = 200,000 (correct)
                $availableAmounts[$type] = max(0, $total - $allocation->total_allocated);
            } else {
                // No allocations for this type, so all offerings are available
                $availableAmounts[$type] = $total;
            }
        }

        return $availableAmounts;
    }

    /**
     * Allocate funds to a budget from specific offering types
     */
    public function allocateFundsToBudget(Budget $budget, array $allocations)
    {
        DB::beginTransaction();

        try {
            $totalAllocated = 0;
            $allocationsCreated = [];

            foreach ($allocations as $offeringType => $amount) {
                if ($amount <= 0)
                    continue;

                $availableAmounts = $this->getAvailableAmountsAfterAllocations();
                $availableForType = $availableAmounts[$offeringType] ?? 0;

                if ($amount > $availableForType) {
                    throw new \Exception("Insufficient funds in {$offeringType}. Available: {$availableForType}, Requested: {$amount}");
                }

                $allocation = BudgetOfferingAllocation::create([
                    'budget_id' => $budget->id,
                    'offering_type' => $offeringType,
                    'allocated_amount' => $amount,
                    'available_amount' => $availableForType,
                    'is_primary' => $offeringType === $budget->primary_offering_type,
                    'notes' => "Allocated for {$budget->budget_name}"
                ]);

                $allocationsCreated[] = $allocation;
                $totalAllocated += $amount;
            }

            // Update budget allocated amount
            $budget->update(['allocated_amount' => $totalAllocated]);

            DB::commit();
            return $allocationsCreated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Budget funding allocation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Suggest funding allocation based on budget purpose and available funds
     */
    public function suggestFundingAllocation(Budget $budget, $requestedAmount = null)
    {
        $amount = $requestedAmount ?? $budget->total_budget;
        $availableAmounts = $this->getAvailableAmountsAfterAllocations();

        $suggestions = [];
        $remainingAmount = $amount;

        // First, try to allocate from primary offering type
        if ($budget->primary_offering_type && isset($availableAmounts[$budget->primary_offering_type])) {
            $primaryAvailable = $availableAmounts[$budget->primary_offering_type];
            $primaryAllocation = min($remainingAmount, $primaryAvailable);

            if ($primaryAllocation > 0) {
                $suggestions[$budget->primary_offering_type] = $primaryAllocation;
                $remainingAmount -= $primaryAllocation;
            }
        }

        // If still need more funds, suggest from other offering types
        if ($remainingAmount > 0) {
            foreach ($availableAmounts as $offeringType => $available) {
                if ($offeringType === $budget->primary_offering_type)
                    continue;
                if ($remainingAmount <= 0)
                    break;

                $allocation = min($remainingAmount, $available);
                if ($allocation > 0) {
                    $suggestions[$offeringType] = $allocation;
                    $remainingAmount -= $allocation;
                }
            }
        }

        return [
            'suggestions' => $suggestions,
            'remaining_amount' => $remainingAmount,
            'is_fully_fundable' => $remainingAmount <= 0,
            'available_amounts' => $availableAmounts
        ];
    }

    /**
     * Deduct expense amount from appropriate offering allocations
     */
    public function deductExpenseFromAllocations(Budget $budget, $expenseAmount)
    {
        DB::beginTransaction();

        try {
            $remainingAmount = $expenseAmount;
            $allocations = $budget->offeringAllocations()
                ->whereRaw('allocated_amount > used_amount')
                ->orderBy('is_primary', 'desc')
                ->orderBy('allocated_amount', 'desc')
                ->get();

            foreach ($allocations as $allocation) {
                if ($remainingAmount <= 0)
                    break;

                $availableInAllocation = $allocation->allocated_amount - $allocation->used_amount;
                $deductionAmount = min($remainingAmount, $availableInAllocation);

                $allocation->increment('used_amount', $deductionAmount);
                $remainingAmount -= $deductionAmount;

                Log::info("Deducted {$deductionAmount} from {$allocation->offering_type} for budget {$budget->budget_name}");
            }

            if ($remainingAmount > 0) {
                throw new \Exception("Insufficient allocated funds to cover expense. Remaining: {$remainingAmount}");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense deduction failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get funding summary for a budget
     */
    public function getBudgetFundingSummary(Budget $budget)
    {
        $allocations = $budget->offeringAllocations;

        // Calculate pending expenses (not yet paid - includes both pending and approved expenses)
        $pendingExpensesAmount = \App\Models\Expense::where('budget_id', $budget->id)
            ->where(function ($query) {
                $query->where('status', '!=', 'paid')
                    ->where(function ($q) {
                        $q->whereIn('approval_status', ['pending', 'approved'])
                            ->orWhereNull('approval_status'); // Include NULL for backward compatibility
                    });
            })
            ->sum('amount');

        // Calculate how much of pending expenses would come from each allocation
        // This is an estimate - we'll deduct proportionally from allocations
        $totalAllocated = $allocations->sum('allocated_amount');
        $totalUsed = $allocations->sum('used_amount');
        $totalRemainingAllocated = $totalAllocated - $totalUsed;

        // Calculate pending expenses that would use allocated funds
        $pendingFromAllocations = min($pendingExpensesAmount, $totalRemainingAllocated);

        $summary = [
            'total_budget' => $budget->total_budget,
            'total_allocated' => $totalAllocated,
            'total_used' => $totalUsed,
            'pending_expenses' => $pendingExpensesAmount,
            'remaining_allocated' => $totalRemainingAllocated - $pendingFromAllocations, // Subtract pending expenses
            'funding_percentage' => $budget->funding_percentage,
            'is_fully_funded' => $budget->isFullyFunded(),
            'breakdown' => $allocations->map(function ($allocation) use ($pendingFromAllocations, $totalRemainingAllocated) {
                // Calculate pending amount for this allocation proportionally
                $allocationRemaining = $allocation->allocated_amount - $allocation->used_amount;
                $pendingForThisAllocation = 0;
                if ($totalRemainingAllocated > 0 && $pendingFromAllocations > 0) {
                    $pendingForThisAllocation = ($allocationRemaining / $totalRemainingAllocated) * $pendingFromAllocations;
                }

                return [
                    'offering_type' => $allocation->offering_type,
                    'allocated' => $allocation->allocated_amount,
                    'used' => $allocation->used_amount,
                    'pending' => $pendingForThisAllocation,
                    'remaining' => max(0, $allocationRemaining - $pendingForThisAllocation), // Available after pending
                    'is_primary' => $allocation->is_primary,
                    'utilization_percentage' => $allocation->utilization_percentage
                ];
            })
        ];

        return $summary;
    }

    /**
     * Get offering type mapping for budget purposes
     */
    public function getOfferingTypeMapping()
    {
        return [
            'building' => 'building_fund',
            'ministry' => 'general',
            'operations' => 'general',
            'special_events' => 'special',
            'thanksgiving' => 'thanksgiving',
            'missions' => 'general',
            'youth' => 'general',
            'children' => 'general',
            'worship' => 'general',
            'outreach' => 'general'
        ];
    }

    /**
     * Get suggested primary offering type based on budget purpose
     */
    public function getSuggestedPrimaryOfferingType($purpose)
    {
        $mapping = $this->getOfferingTypeMapping();

        // Check if purpose exists in mapping
        if (isset($mapping[$purpose])) {
            return $mapping[$purpose];
        }

        // Check if purpose matches a custom offering type (case-insensitive)
        $availableOfferingTypes = Offering::select('offering_type')
            ->where('approval_status', 'approved')
            ->distinct()
            ->pluck('offering_type')
            ->toArray();

        // Normalize purpose to match offering type format
        $normalizedPurpose = strtolower(str_replace([' ', '-'], '_', $purpose));

        // Check if normalized purpose exists as an offering type
        foreach ($availableOfferingTypes as $offeringType) {
            $normalizedOfferingType = strtolower(str_replace([' ', '-'], '_', $offeringType));
            if ($normalizedPurpose === $normalizedOfferingType) {
                return $offeringType; // Return the actual offering type from database
            }
        }

        // Default to general if no match found
        return 'general';
    }

    /**
     * Get all available offering types (including custom types)
     */
    public function getAllAvailableOfferingTypes()
    {
        return Offering::select('offering_type')
            ->where('approval_status', 'approved')
            ->distinct()
            ->orderBy('offering_type')
            ->pluck('offering_type')
            ->toArray();
    }
}



