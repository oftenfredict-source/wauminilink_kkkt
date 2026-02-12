<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetOfferingAllocation;
use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\PledgePayment;
use App\Models\AhadiPledge;
use App\Models\OfferingCollectionItem;
use App\Models\CommunityOfferingItem;
use App\Models\Tithe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetFundingService
{
    /**
     * Get available offering amounts by type
     */
    public function getAvailableOfferingAmounts()
    {
        $categories = [
            'injili' => 0,
            'umoja' => 0,
            'majengo' => 0,
            'other' => 0
        ];

        $individualOfferings = [];

        // 1. Standard individual offerings (approved only)
        $offerings = Offering::where('approval_status', 'approved')->get();
        foreach ($offerings as $offering) {
            $type = $offering->offering_type;
            $cat = $this->getCategoryForOfferingType($type);
            $categories[$cat] += $offering->amount;
            $individualOfferings[$type] = ($individualOfferings[$type] ?? 0) + $offering->amount;
        }

        // 2. Tithes (approved only)
        $titheSum = Tithe::where('approval_status', 'approved')->sum('amount');
        $categories['injili'] += $titheSum;
        $individualOfferings['tithe'] = ($individualOfferings['tithe'] ?? 0) + $titheSum;

        // 3. Pledge Payments (Individual Ahadi payments)
        $pledgePayments = PledgePayment::sum('amount');
        $categories['injili'] += $pledgePayments;
        $individualOfferings['Ahadi ya Bwana'] = ($individualOfferings['Ahadi ya Bwana'] ?? 0) + $pledgePayments;

        // 4. Ahadi Pledges (Fulfilled physical or cash ahadi from another table)
        $ahadiPledges = AhadiPledge::where(function ($q) {
            $q->where('item_type', 'LIKE', '%Cash%')->orWhere('item_type', 'LIKE', '%Fedha%');
        })->sum('quantity_fulfilled');
        $categories['injili'] += $ahadiPledges;
        $individualOfferings['Ahadi ya Bwana'] = ($individualOfferings['Ahadi ya Bwana'] ?? 0) + $ahadiPledges;

        // 5. Offering Collection Items (from Sunday sessions)
        $collectionItems = OfferingCollectionItem::selectRaw('
                SUM(amount_pledge) as ahadi,
                SUM(amount_unity) as unity,
                SUM(amount_building) as building,
                SUM(amount_other) as other
            ')
            ->whereHas('session', function ($q) {
                $q->where('status', 'received');
            })
            ->first();

        if ($collectionItems) {
            $categories['injili'] += ($collectionItems->ahadi ?? 0);
            $categories['umoja'] += ($collectionItems->unity ?? 0);
            $categories['majengo'] += ($collectionItems->building ?? 0);
            $categories['injili'] += ($collectionItems->other ?? 0);

            $individualOfferings['Ahadi ya Bwana'] = ($individualOfferings['Ahadi ya Bwana'] ?? 0) + ($collectionItems->ahadi ?? 0);
            $individualOfferings['umoja'] = ($individualOfferings['umoja'] ?? 0) + ($collectionItems->unity ?? 0);
            $individualOfferings['building_fund'] = ($individualOfferings['building_fund'] ?? 0) + ($collectionItems->building ?? 0);
            $individualOfferings['other_collection'] = ($individualOfferings['other_collection'] ?? 0) + ($collectionItems->other ?? 0);
        }

        // 6. Community Offerings
        // Use CommunityOfferingItem for breakdown components
        $communityItemTotals = CommunityOfferingItem::selectRaw('
                SUM(amount_ahadi) as ahadi,
                SUM(amount_jengo) as jengo,
                SUM(amount_umoja) as umoja
            ')
            ->whereHas('offering', function ($q) {
                $q->where('status', 'completed');
            })
            ->first();

        if ($communityItemTotals) {
            $categories['injili'] += ($communityItemTotals->ahadi ?? 0);
            $categories['majengo'] += ($communityItemTotals->jengo ?? 0);
            $categories['umoja'] += ($communityItemTotals->umoja ?? 0);

            $individualOfferings['Ahadi ya Bwana'] = ($individualOfferings['Ahadi ya Bwana'] ?? 0) + ($communityItemTotals->ahadi ?? 0);
            $individualOfferings['building_fund'] = ($individualOfferings['building_fund'] ?? 0) + ($communityItemTotals->jengo ?? 0);
            $individualOfferings['umoja'] = ($individualOfferings['umoja'] ?? 0) + ($communityItemTotals->umoja ?? 0);
        }

        // Use CommunityOffering Summary for residuals and specialized types
        $communityOfferings = CommunityOffering::where('status', 'completed')->get();
        foreach ($communityOfferings as $co) {
            $type = $co->offering_type ?? 'general';
            $cat = $this->getCategoryForOfferingType($type);

            // Calculate residual (Total - known components) to avoid double counting
            // Umoja, Jengo and Ahadi are already handled via Items table
            $parts = ($co->amount_umoja ?? 0) + ($co->amount_jengo ?? 0) + ($co->amount_ahadi ?? 0);
            $residual = ($co->amount ?? 0) - $parts;

            if ($residual > 0) {
                // Categorize residual based on the record type
                $categories[$cat] += $residual;

                // Track for individual breakdown return
                $sourceName = $type === 'general' ? 'general_collection' : $type;
                $individualOfferings[$sourceName] = ($individualOfferings[$sourceName] ?? 0) + $residual;
            }
        }

        return array_merge($individualOfferings, $categories);
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
        $mapping = $this->getIncomeCategoryMapping();
        $categories = array_keys($mapping);

        // Get allocated amounts by offering type
        $allocationData = BudgetOfferingAllocation::selectRaw('offering_type, SUM(allocated_amount) as total_allocated')
            ->whereHas('budget', function ($query) {
                $query->where('status', 'active');
            })
            ->groupBy('offering_type')
            ->get();

        $availableAmounts = [];

        // Strategy: First calculate available for each individual type, 
        // then aggregate those remaining amounts into categories.

        // 1. Calculate available for each individual type
        foreach ($totalOfferings as $type => $total) {
            if (in_array($type, $categories))
                continue; // Skip categories for now

            $totalAllocated = $allocationData->where('offering_type', $type)->sum('total_allocated');

            // Also consider if this type was allocated as part of its parent category
            $category = $this->getCategoryForOfferingType($type);
            $categoryAllocated = $allocationData->where('offering_type', $category)->sum('total_allocated');

            // This is tricky: if a category is allocated, we don't know which individual type it "uses".
            // For safety, we treat category-level allocations as reducing the availability of ALL its types proportionally 
            // OR we just subtract from the category total.
            // Simplified approach: Primary availability calculation happens at the type level.
            $availableAmounts[$type] = max(0, $total - $totalAllocated);
        }

        // 2. Calculate available for categories by summing available individual types
        foreach ($totalOfferings as $type => $amount) {
            // Skip if this is one of our main accumulators (the categories themselves)
            if (in_array($type, $categories))
                continue;

            $cat = $this->getCategoryForOfferingType($type);

            // Get allocated amount for this specific type
            $typeAllocated = $allocationData->where('offering_type', $type)->sum('total_allocated');

            // Add remaining amount to the category total
            // We accumulate the net available from this type into the category
            // This works because we initialized the categories (injili/umoja/etc) with their base values in getAvailableOfferingAmounts
            // BUT getAvailableOfferingAmounts ALREADY added these to $categories['injili'] etc. if they were caught there.

            // Wait, getAvailableOfferingAmounts (lines 20-127) ALREADY sums up $categories['injili'] etc.
            // Let's look at getAvailableOfferingAmounts again.
            // It splits individualOfferings AND sums into $categories.

            // So $totalOfferings contains BOTH the individual breakdowns AND the category totals (lines 126: array_merge).
            // Example: [ 'KOLEKTI' => 90000, 'injili' => 225000 (sum of everything identified as injili) ]

            // The problem is that getAvailableOfferingAmounts MIGHT have missed adding 'KOLEKTI' to 'injili' 
            // if strict mapping was used *there*.

            // Let's check getAvailableOfferingAmounts lines 105-124 (CommunityOfferings).
            // It does: $cat = $this->getCategoryForOfferingType($type); $categories[$cat] += $residual;
            // This uses the robust dynamic categorization. So 'KOLEKTI' WAS added to 'injili' in $totalOfferings['injili'].

            // So `totalOfferings['injili']` SHOULD be 135,000.
            // Debug output said: [injili] => 135000.
            // So the INPUT to this function is correct.

            // The filtering logic I am replacing (lines 174-187) was RE-CALCULATING the category totals 
            // by summing up the individual components found in the mapping, effectively discarding the
            // valid work done by getAvailableOfferingAmounts.

            // The correct approach is:
            // The category total from getAvailableOfferingAmounts represents the GROSS INCOME for that category.
            // We just need to subtract allocations.

            // BUT, we need to subtract allocations for *individual types* belonging to that category too.
            // Total Available Injili = (Gross Injili) - (Allocations to 'injili') - (Allocations to 'tithe') - (Allocations to 'KOLEKTI') ...

            // So we need to iterate ALL allocations, identify if they belong to this category, and subtract them.
        }

        // Simpler implementation:
        // 1. Start with the Gross Totals from input.
        foreach ($categories as $cat) {
            $availableAmounts[$cat] = $totalOfferings[$cat] ?? 0;
        }

        // 2. Subtract ALL allocations from their respective categories
        foreach ($allocationData as $allocation) {
            $type = $allocation->offering_type;
            $amount = $allocation->total_allocated;

            // If the allocation type IS a category, subtract from that category
            if (isset($availableAmounts[$type])) {
                $availableAmounts[$type] -= $amount;
            } else {
                // If it's a specific type, find its category and subtract
                $cat = $this->getCategoryForOfferingType($type);
                if (isset($availableAmounts[$cat])) {
                    $availableAmounts[$cat] -= $amount;
                }
            }
        }

        // 3. Ensure no negatives
        foreach ($availableAmounts as $cat => $val) {
            $availableAmounts[$cat] = max(0, $val);
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

    /**
     * Get income category mapping
     */
    public function getIncomeCategoryMapping()
    {
        return [
            'injili' => [
                'Ahadi ya Bwana',
                'sadaka_ahadi',
                'ahadi',
                'kolekti',
                'sadaka_kawaida',
                'general',
                'sunday_school',
                's/school',
                'thanksgiving',
                'shukrani',
                'malimbuko',
                'mavuno',
                'huruma',
                'fungu_la_kumi',
                'tithe',
                'fungu la kumi',
                'Lord\'s Supper',
                'chakula_cha_bwana'
            ],
            'umoja' => [
                'Sadaka ya Umoja',
                'sadaka_umoja',
                'umoja'
            ],
            'majengo' => [
                'building_fund',
                'sadaka_jengo',
                'Ahadi ya Jengo'
            ],
            'other' => [
                'other',
                'mengineyo',
                'special_offering'
            ]
        ];
    }

    /**
     * Get category for a specific offering type
     */
    public function getCategoryForOfferingType($type)
    {
        if (!$type)
            return 'injili';

        $mapping = $this->getIncomeCategoryMapping();

        foreach ($mapping as $category => $types) {
            // Case-insensitive search
            if (in_array(strtolower($type), array_map('strtolower', $types))) {
                return $category;
            }
        }

        $typeLower = strtolower($type);

        // Majengo fallbacks
        if (stripos($typeLower, 'jengo') !== false || stripos($typeLower, 'building') !== false || $typeLower === 'sadaka_jengo') {
            return 'majengo';
        }

        // Umoja fallbacks
        if (stripos($typeLower, 'umoja') !== false || $typeLower === 'sadaka_umoja' || $typeLower === 'unity') {
            return 'umoja';
        }

        // Other (Vikundi) fallbacks
        if (stripos($typeLower, 'kwaya') !== false || stripos($typeLower, 'jumuiya') !== false || stripos($typeLower, 'group') !== false) {
            return 'other';
        }

        // Default to Injili
        return 'injili';
    }

    /**
     * Get suggested primary offering type (category) based on budget purpose or type
     */
    public function getSuggestedPrimaryOfferingType($purpose, $budgetType = null)
    {
        // If budget type is provided, it's the most reliable category
        if ($budgetType && in_array(strtolower($budgetType), ['injili', 'umoja', 'majengo', 'other'])) {
            return strtolower($budgetType);
        }

        $mapping = $this->getOfferingTypeMapping();

        // Check if purpose exists in mapping
        if (isset($mapping[$purpose])) {
            $offeringType = $mapping[$purpose];
            return $this->getCategoryForOfferingType($offeringType);
        }

        // Check if purpose matches a custom offering type
        $availableOfferingTypes = $this->getAllAvailableOfferingTypes();
        $normalizedPurpose = strtolower(str_replace([' ', '-'], '_', $purpose));

        foreach ($availableOfferingTypes as $offeringType) {
            $normalizedOfferingType = strtolower(str_replace([' ', '-'], '_', $offeringType));
            if ($normalizedPurpose === $normalizedOfferingType) {
                return $this->getCategoryForOfferingType($offeringType);
            }
        }

        return 'injili'; // Default
    }
}



