<?php

namespace App\Http\Controllers;

use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Member;
use App\Models\FundingRequest;
use App\Services\BudgetFundingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    protected $budgetFundingService;

    public function __construct(BudgetFundingService $budgetFundingService)
    {
        $this->budgetFundingService = $budgetFundingService;
    }
    /**
     * Display the financial dashboard
     */
    public function dashboard()
    {
        \Log::info('FinanceController@dashboard called');
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->year;
        
        // Get financial summary for current month (only approved records)
        $monthlyTithes = Tithe::whereMonth('tithe_date', $currentMonth->month)
            ->whereYear('tithe_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');
            
        $monthlyOfferings = Offering::whereMonth('offering_date', $currentMonth->month)
            ->whereYear('offering_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');
            
        $monthlyDonations = Donation::whereMonth('donation_date', $currentMonth->month)
            ->whereYear('donation_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');
            
        // Get pledge payments made this month (actual money received from pledges)
        $monthlyPledgePayments = Pledge::whereMonth('updated_at', $currentMonth->month)
            ->whereYear('updated_at', $currentYear)
            ->sum('amount_paid');
            
        // Get expenses for current month - include soft-deleted to preserve financial history
        // Also include expenses from current year if they're paid (not just current month)
        $monthlyExpenses = Expense::withTrashed()
            ->whereYear('expense_date', $currentYear)
            ->where('status', 'paid')
            ->where('approval_status', 'approved')
            ->sum('amount');
        
        // Also get current month expenses separately for display
        $currentMonthExpenses = Expense::withTrashed()
            ->whereMonth('expense_date', $currentMonth->month)
            ->whereYear('expense_date', $currentYear)
            ->where('status', 'paid')
            ->where('approval_status', 'approved')
            ->sum('amount');
        
        \Log::info('Finance Dashboard Expenses Calculation', [
            'current_month' => $currentMonth->format('Y-m'),
            'current_year' => $currentYear,
            'monthly_expenses' => $monthlyExpenses,
            'current_month_expenses' => $currentMonthExpenses,
            'total_paid_expenses_count' => Expense::withTrashed()
                ->where('status', 'paid')
                ->where('approval_status', 'approved')
                ->count()
        ]);
        
        $totalIncome = $monthlyTithes + $monthlyOfferings + $monthlyDonations + $monthlyPledgePayments;
        $netIncome = $totalIncome - $monthlyExpenses;
        
        // Get recent transactions
        $recentTithes = Tithe::with('member')
            ->orderBy('tithe_date', 'desc')
            ->limit(5)
            ->get();
            
        $recentOfferings = Offering::with('member')
            ->orderBy('offering_date', 'desc')
            ->limit(5)
            ->get();
            
        $recentDonations = Donation::with('member')
            ->orderBy('donation_date', 'desc')
            ->limit(5)
            ->get();
        
        // Get budget status
        $currentBudgets = Budget::current()->get();
        
        // Get pledge status
        $activePledges = Pledge::active()->with('member')->get();
        $overduePledges = Pledge::overdue()->with('member')->get();
        
        // Get monthly income trend (last 6 months) - only approved records
        $incomeTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthIncome = Tithe::whereMonth('tithe_date', $month->month)
                ->whereYear('tithe_date', $month->year)
                ->where('approval_status', 'approved')
                ->sum('amount') +
                Offering::whereMonth('offering_date', $month->month)
                ->whereYear('offering_date', $month->year)
                ->where('approval_status', 'approved')
                ->sum('amount') +
                Donation::whereMonth('donation_date', $month->month)
                ->whereYear('donation_date', $month->year)
                ->where('approval_status', 'approved')
                ->sum('amount') +
                Pledge::whereMonth('updated_at', $month->month)
                ->whereYear('updated_at', $month->year)
                ->sum('amount_paid');
                
            $incomeTrend[] = [
                'month' => $month->format('M Y'),
                'income' => $monthIncome
            ];
        }
        
        // Get total members count for the layout
        $totalMembers = Member::count();
        
        return view('finance.dashboard', compact(
            'monthlyTithes',
            'monthlyOfferings', 
            'monthlyDonations',
            'monthlyPledgePayments',
            'monthlyExpenses',
            'totalIncome',
            'netIncome',
            'recentTithes',
            'recentOfferings',
            'recentDonations',
            'currentBudgets',
            'activePledges',
            'overduePledges',
            'incomeTrend',
            'totalMembers'
        ));
    }
    
    /**
     * Display tithes management
     */
    public function tithes(Request $request)
    {
        $query = Tithe::with('member');
        
        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('tithe_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('tithe_date', '<=', $request->date_to);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Ensure we get all records, even if member relationship is missing
        $tithes = $query->orderBy('tithe_date', 'desc')->orderBy('id', 'desc')->paginate(20);
        $members = Member::orderBy('full_name')->get();
        $totalMembers = Member::count();
        
        // Get pastor information for approval messages
        $pastor = \App\Models\User::where('can_approve_finances', true)
            ->orWhere('role', 'pastor')
            ->orWhere('role', 'admin')
            ->first();
        
        return view('finance.tithes', compact('tithes', 'members', 'totalMembers', 'pastor'));
    }
    
    /**
     * Store a new tithe
     */
    public function storeTithe(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'tithe_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_verified' => 'boolean'
        ]);
        
        $validated['recorded_by'] = auth()->user()->name ?? 'System';
        $validated['approval_status'] = 'pending'; // Set to pending for pastor approval
        $validated['is_verified'] = false; // Override any verification status
        
        $tithe = Tithe::create($validated);
        
        // Send notification to pastors about pending tithe
        $this->sendFinancialApprovalNotification('tithe', $tithe);
        
        return redirect()->route('finance.tithes')
            ->with('success', 'Tithe recorded successfully and sent for pastor approval');
    }

    /**
     * Mark tithe as paid (verified) - only by treasurer
     */
    public function markTithePaid(Tithe $tithe)
    {
        // Check if user is treasurer or admin (only treasurers can mark tithes as paid)
        $user = auth()->user();
        if (!$user->isTreasurer() && !$user->isAdmin()) {
            return redirect()->route('finance.tithes')
                ->with('error', 'Only treasurers can mark tithes as paid');
        }
        
        $tithe->update(['is_verified' => true]);
        return redirect()->route('finance.tithes')
            ->with('success', 'Tithe marked as paid');
    }
    
    /**
     * Display offerings management
     */
    public function offerings(Request $request)
    {
        $query = Offering::with('member');
        
        // Apply filters
        if ($request->filled('offering_type')) {
            $query->where('offering_type', $request->offering_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('offering_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('offering_date', '<=', $request->date_to);
        }
        
        $offerings = $query->orderBy('offering_date', 'desc')->paginate(20);
        $members = Member::orderBy('full_name')->get();
        $totalMembers = Member::count();
        
        // Get pastor information for approval messages
        // Try to get from Leader model first (most accurate), then fallback to User model
        $pastor = null;
        $pastorLeader = \App\Models\Leader::with('member')
            ->where('position', 'pastor')
            ->where('is_active', true)
            ->first();
        
        if ($pastorLeader && $pastorLeader->member) {
            // Create a simple object with the pastor's name from member
            $pastor = (object)[
                'name' => $pastorLeader->member->full_name,
                'email' => $pastorLeader->member->email ?? null,
            ];
        } else {
            // Fallback to User model - Priority: pastor role > admin with approval rights
            $pastorUser = \App\Models\User::where('role', 'pastor')
                ->orWhere(function($query) {
                    $query->where('role', 'admin')
                          ->where('can_approve_finances', true);
                })
                ->orWhere('can_approve_finances', true)
                ->orderByRaw("CASE WHEN role = 'pastor' THEN 1 WHEN role = 'admin' THEN 2 ELSE 3 END")
                ->first();
            
            if ($pastorUser) {
                $pastor = $pastorUser;
            }
        }
        
        return view('finance.offerings', compact('offerings', 'members', 'totalMembers', 'pastor'));
    }
    
    /**
     * Store a new offering
     */
    public function storeOffering(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'offering_date' => 'required|date',
            'offering_type' => 'required|string',
            'custom_offering_type' => 'required_if:offering_type,other|nullable|string|max:255',
            'service_type' => 'nullable|string',
            'service_id' => 'nullable|integer',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_verified' => 'boolean'
        ]);
        
        $validated['recorded_by'] = auth()->user()->name ?? 'System';
        $validated['approval_status'] = 'pending'; // Set to pending for pastor approval
        $validated['is_verified'] = false; // Override any verification status
        
        // If offering type is 'other', use the custom offering type
        if ($validated['offering_type'] === 'other' && !empty($validated['custom_offering_type'])) {
            $validated['offering_type'] = $validated['custom_offering_type'];
        }
        
        // Remove custom_offering_type from the data as it's not a database field
        unset($validated['custom_offering_type']);
        
        $offering = Offering::create($validated);
        
        // Send notification to pastors about pending offering
        $this->sendFinancialApprovalNotification('offering', $offering);
        
        return redirect()->route('finance.offerings')
            ->with('success', 'Offering recorded successfully and sent for pastor approval');
    }
    
    /**
     * Display donations management
     */
    public function donations(Request $request)
    {
        $query = Donation::with('member');
        
        // Apply filters
        if ($request->filled('donation_type')) {
            $query->where('donation_type', $request->donation_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('donation_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('donation_date', '<=', $request->date_to);
        }
        
        $donations = $query->orderBy('donation_date', 'desc')->paginate(20);
        $members = Member::orderBy('full_name')->get();
        $totalMembers = Member::count();
        
        // Get pastor information for approval messages
        $pastor = \App\Models\User::where('can_approve_finances', true)
            ->orWhere('role', 'pastor')
            ->orWhere('role', 'admin')
            ->first();
        
        return view('finance.donations', compact('donations', 'members', 'totalMembers', 'pastor'));
    }
    
    /**
     * Store a new donation
     */
    public function storeDonation(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'donor_name' => 'nullable|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:0',
            'donation_date' => 'required|date',
            'donation_type' => 'required|string',
            'custom_donation_type' => 'required_if:donation_type,other|nullable|string|max:255',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_verified' => 'boolean',
            'is_anonymous' => 'boolean'
        ]);
        
        $validated['recorded_by'] = auth()->user()->name ?? 'System';
        $validated['approval_status'] = 'pending'; // Set to pending for pastor approval
        $validated['is_verified'] = false; // Override any verification status
        
        // If neither member_id nor donor_name is provided, set as anonymous donation
        if (empty($validated['member_id']) && empty($validated['donor_name'])) {
            $validated['donor_name'] = 'Anonymous';
            $validated['is_anonymous'] = true;
        }
        
        // If donation type is 'other', use the custom donation type
        if ($validated['donation_type'] === 'other' && !empty($validated['custom_donation_type'])) {
            $validated['donation_type'] = $validated['custom_donation_type'];
        }
        
        // Remove custom_donation_type from the data as it's not a database field
        unset($validated['custom_donation_type']);
        
        $donation = Donation::create($validated);
        
        // Send notification to pastors about pending donation
        $this->sendFinancialApprovalNotification('donation', $donation);
        
        return redirect()->route('finance.donations')
            ->with('success', 'Donation recorded successfully and sent for pastor approval');
    }
    
    /**
     * Display pledges management
     */
    public function pledges(Request $request)
    {
        $query = Pledge::with('member');
        
        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        
        if ($request->filled('pledge_type')) {
            $query->where('pledge_type', $request->pledge_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $pledges = $query->orderBy('pledge_date', 'desc')->paginate(20);
        $members = Member::orderBy('full_name')->get();
        $totalMembers = Member::count();
        
        // Get pastor information for approval messages
        $pastor = \App\Models\User::where('can_approve_finances', true)
            ->orWhere('role', 'pastor')
            ->orWhere('role', 'admin')
            ->first();
        
        return view('finance.pledges', compact('pledges', 'members', 'totalMembers', 'pastor'));
    }
    
    /**
     * Store a new pledge
     */
    public function storePledge(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'pledge_amount' => 'required|numeric|min:0',
            'pledge_date' => 'required|date',
            'due_date' => 'nullable|date|after:pledge_date',
            'pledge_type' => 'required|string',
            'payment_frequency' => 'required|string',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $validated['recorded_by'] = auth()->user()->name ?? 'System';
        $validated['amount_paid'] = 0;
        $validated['status'] = 'active';
        $validated['approval_status'] = 'approved'; // Auto-approve pledge creation (no approval needed)
        $validated['approved_by'] = auth()->id();
        $validated['approved_at'] = now();
        
        $pledge = Pledge::create($validated);
        
        return redirect()->route('finance.pledges')
            ->with('success', 'Pledge recorded successfully');
    }
    
    /**
     * Update pledge payment - creates a PledgePayment record that requires approval
     */
    public function updatePledgePayment(Request $request, Pledge $pledge)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        // Create a PledgePayment record that requires approval
        $pledgePayment = \App\Models\PledgePayment::create([
            'pledge_id' => $pledge->id,
            'amount' => $validated['payment_amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'] ?? 'cash',
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->user()->name ?? 'System',
            'approval_status' => 'pending' // Require approval for payments
        ]);
        
        // Load the pledge relationship with member for notification
        $pledgePayment->load('pledge.member');
        
        // Send notification to pastors about pending pledge payment
        $this->sendFinancialApprovalNotification('pledge_payment', $pledgePayment);
        
        return redirect()->route('finance.pledges')
            ->with('success', 'Pledge payment recorded successfully and sent for pastor approval');
    }
    
    /**
     * Display budgets management
     */
    public function budgets(Request $request)
    {
        $query = Budget::with('lineItems');
        
        // Apply filters
        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }
        
        if ($request->filled('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $budgets = $query->orderBy('fiscal_year', 'desc')
            ->orderBy('start_date', 'desc')
            ->paginate(20);
        $totalMembers = Member::count();
        
        return view('finance.budgets', compact('budgets', 'totalMembers'));
    }
    
    /**
     * Store a new budget
     */
    public function storeBudget(Request $request)
    {
        $validated = $request->validate([
            'budget_name' => 'required|string',
            'budget_type' => 'required|string',
            'purpose' => 'required|string',
            'custom_purpose' => 'required_if:purpose,other|nullable|string|max:255',
            'fiscal_year' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'funding_allocations' => 'nullable|array',
            'funding_allocations.*.offering_type' => 'required_with:funding_allocations|string',
            'funding_allocations.*.amount' => 'required_with:funding_allocations|numeric|min:0',
            'line_items' => 'nullable|array',
            'line_items.*.item_name' => 'required_with:line_items|string',
            'line_items.*.amount' => 'required_with:line_items|numeric|min:0',
            'line_items.*.responsible_person' => 'required_with:line_items|string',
            'line_items.*.notes' => 'nullable|string'
        ]);
        
        // If purpose is "other", use custom_purpose
        if ($validated['purpose'] === 'other' && !empty($validated['custom_purpose'])) {
            $validated['purpose'] = strtolower(str_replace([' ', '-'], '_', $validated['custom_purpose']));
        }
        unset($validated['custom_purpose']);
        
        $validated['created_by'] = auth()->user()->name ?? 'System';
        $validated['allocated_amount'] = 0;
        $validated['spent_amount'] = 0;
        $validated['status'] = 'active';
        $validated['approval_status'] = 'pending';
        
        // Set primary offering type based on purpose (handles custom types)
        $validated['primary_offering_type'] = $this->budgetFundingService->getSuggestedPrimaryOfferingType($validated['purpose']);
        $validated['requires_approval'] = true;
        
        $budget = Budget::create($validated);
        
        // Handle line items if provided (for celebrations/events)
        if ($request->has('line_items') && is_array($request->line_items)) {
            try {
                $order = 0;
                foreach ($request->line_items as $item) {
                    if (!empty($item['item_name']) && isset($item['amount']) && !empty($item['responsible_person'])) {
                        \App\Models\BudgetLineItem::create([
                            'budget_id' => $budget->id,
                            'item_name' => $item['item_name'],
                            'amount' => $item['amount'],
                            'responsible_person' => $item['responsible_person'],
                            'notes' => $item['notes'] ?? null,
                            'order' => $order++
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to save budget line items: ' . $e->getMessage());
                // Don't fail the whole request, just log the error
            }
        }
        
        // Note: Funding allocations are no longer handled during budget creation
        // Budgets are created without pre-allocated funds
        // Funds will be allocated automatically when expenses are created or through manual allocation
        
        // Send notification to pastors about pending budget
        $this->sendFinancialApprovalNotification('budget', $budget);
        
        return redirect()->route('finance.budgets')
            ->with('success', 'Budget created successfully and sent for pastor approval');
    }

    /**
     * Allocate funds to a budget
     */
    public function allocateBudgetFunds(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'allocations' => 'required|array',
            'allocations.*.offering_type' => 'required|string',
            'allocations.*.amount' => 'required|numeric|min:0'
        ]);

        try {
            $allocations = [];
            foreach ($validated['allocations'] as $allocation) {
                if ($allocation['amount'] > 0) {
                    $allocations[$allocation['offering_type']] = $allocation['amount'];
                }
            }

            $this->budgetFundingService->allocateFundsToBudget($budget, $allocations);

            return response()->json([
                'success' => true,
                'message' => 'Funds allocated successfully',
                'funding_summary' => $this->budgetFundingService->getBudgetFundingSummary($budget)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Allocation failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get funding suggestions for a budget
     */
    public function getFundingSuggestions(Request $request, Budget $budget)
    {
        $amount = $request->get('amount', $budget->total_budget);
        $suggestions = $this->budgetFundingService->suggestFundingAllocation($budget, $amount);

        return response()->json($suggestions);
    }

    /**
     * Get funding suggestions for a new budget (before creation)
     */
    public function getNewBudgetFundingSuggestions(Request $request)
    {
        $validated = $request->validate([
            'purpose' => 'required|string',
            'amount' => 'required|numeric|min:0'
        ]);

        // Create a temporary budget object for suggestions
        $tempBudget = new Budget();
        $tempBudget->primary_offering_type = $this->budgetFundingService->getSuggestedPrimaryOfferingType($validated['purpose']);
        $tempBudget->total_budget = $validated['amount'];

        $suggestions = $this->budgetFundingService->suggestFundingAllocation($tempBudget, $validated['amount']);

        return response()->json($suggestions);
    }

    /**
     * Get available offering amounts for funding allocation
     */
    public function getAvailableOfferings()
    {
        $availableAmounts = $this->budgetFundingService->getAvailableAmountsAfterAllocations();
        $allOfferingTypes = $this->budgetFundingService->getAllAvailableOfferingTypes();
        
        return response()->json([
            'available_amounts' => $availableAmounts,
            'total_available' => array_sum($availableAmounts),
            'all_offering_types' => $allOfferingTypes
        ]);
    }

    /**
     * Get fund summary for an offering type (before budget creation)
     */
    public function getOfferingTypeFundSummary(Request $request)
    {
        try {
            $offeringType = $request->get('offering_type');
            
            if (!$offeringType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offering type is required'
                ], 400);
            }
            
            // Get total income from offerings
            $totalIncome = Offering::where('offering_type', $offeringType)
                ->where('approval_status', 'approved')
                ->sum('amount');
            
            // Get all budgets using this offering type
            $allBudgetsWithSameOffering = Budget::where('primary_offering_type', $offeringType)
                ->where('status', 'active')
                ->pluck('id');
            
            // Get total used amount from ALL allocations for budgets with this offering type
            $totalUsedFromAllBudgets = \App\Models\BudgetOfferingAllocation::whereIn('budget_id', $allBudgetsWithSameOffering)
                ->sum('used_amount');
            
            // Get total pending expenses from ALL budgets using this offering type
            $totalPendingFromAllBudgets = (float) \App\Models\Expense::whereIn('budget_id', $allBudgetsWithSameOffering)
                ->where(function($query) {
                    $query->where('status', '!=', 'paid')
                          ->where(function($q) {
                              $q->whereIn('approval_status', ['pending', 'approved'])
                                ->orWhereNull('approval_status');
                          });
                })
                ->sum('amount');
            
            // Calculate available amount
            $usedAmount = $totalUsedFromAllBudgets;
            $pendingExpensesAmount = $totalPendingFromAllBudgets;
            $totalCommitted = $usedAmount + $pendingExpensesAmount;
            $availableAmount = $totalIncome - $totalCommitted;
            
            return response()->json([
                'success' => true,
                'fund_summary' => [
                    'offering_type' => $offeringType,
                    'total_income' => $totalIncome,
                    'used_amount' => $usedAmount,
                    'pending_expenses_amount' => $pendingExpensesAmount,
                    'total_committed' => $totalCommitted,
                    'available_amount' => $availableAmount
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Offering type fund summary error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error calculating fund summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get budget information for expense form
     */
    public function getBudgetInfo(Request $request, $budgetId)
    {
        try {
            $budget = Budget::findOrFail($budgetId);
            $fundingSummary = $this->budgetFundingService->getBudgetFundingSummary($budget);
            
            // Calculate pending expenses (not yet paid - includes both pending and approved expenses)
            $pendingExpensesAmount = \App\Models\Expense::where('budget_id', $budgetId)
                ->where(function($query) {
                    $query->where('status', '!=', 'paid')
                          ->where(function($q) {
                              $q->whereIn('approval_status', ['pending', 'approved'])
                                ->orWhereNull('approval_status'); // Include NULL for backward compatibility
                          });
                })
                ->sum('amount');
            
            // Total committed amount = spent (paid) + pending (approved but not paid)
            $totalCommitted = $budget->spent_amount + $pendingExpensesAmount;
            
            // Remaining amount = total budget - (spent + pending)
            $remainingAmount = $budget->total_budget - $totalCommitted;
            
            return response()->json([
                'success' => true,
                'budget' => [
                    'id' => $budget->id,
                    'name' => $budget->budget_name,
                    'total_budget' => $budget->total_budget,
                    'spent_amount' => $budget->spent_amount,
                    'pending_expenses_amount' => $pendingExpensesAmount,
                    'total_committed' => $totalCommitted,
                    'remaining_amount' => $remainingAmount,
                    'is_fully_funded' => $budget->isFullyFunded(),
                    'funding_percentage' => $budget->funding_percentage,
                    'utilization_percentage' => $budget->utilization_percentage,
                    'funding_summary' => $fundingSummary
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Budget not found'
            ], 404);
        }
    }

    /**
     * Get budget line items
     */
    public function getBudgetLineItems(Request $request, $budgetId)
    {
        try {
            $budget = Budget::with('lineItems')->findOrFail($budgetId);
            
            return response()->json([
                'success' => true,
                'line_items' => $budget->lineItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'amount' => $item->amount,
                        'responsible_person' => $item->responsible_person,
                        'notes' => $item->notes,
                        'order' => $item->order
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Budget not found'
            ], 404);
        }
    }

    /**
     * Get fund summary for a budget
     */
    public function getFundSummary(Request $request, $budgetId)
    {
        try {
            $budget = Budget::findOrFail($budgetId);
            $primaryOfferingType = $budget->primary_offering_type;
            
            if (!$primaryOfferingType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Budget has no primary offering type'
                ], 400);
            }
            
            // Get total income from offerings
            $totalIncome = Offering::where('offering_type', $primaryOfferingType)
                ->where('approval_status', 'approved')
                ->sum('amount');
            
            // IMPORTANT: Get used amount from ALL budgets using this offering type, not just this budget
            // This ensures the fund summary shows the correct available amount across all budgets
            $allBudgetsWithSameOffering = Budget::where('primary_offering_type', $primaryOfferingType)
                ->where('status', 'active')
                ->pluck('id');
            
            // Get total used amount from ALL allocations for budgets with this offering type
            $totalUsedFromAllBudgets = \App\Models\BudgetOfferingAllocation::whereIn('budget_id', $allBudgetsWithSameOffering)
                ->sum('used_amount');
            
            // Get total pending expenses from ALL budgets using this offering type
            $totalPendingFromAllBudgets = (float) \App\Models\Expense::whereIn('budget_id', $allBudgetsWithSameOffering)
                ->where(function($query) {
                    $query->where('status', '!=', 'paid')
                          ->where(function($q) {
                              $q->whereIn('approval_status', ['pending', 'approved'])
                                ->orWhereNull('approval_status'); // Include NULL for backward compatibility
                          });
                })
                ->sum('amount');
            
            // For this specific budget (for display purposes)
            $fundingSummary = $this->budgetFundingService->getBudgetFundingSummary($budget);
            $usedAmountForThisBudget = $fundingSummary['total_used'] ?? 0;
            
            $pendingExpensesForThisBudget = (float) \App\Models\Expense::where('budget_id', $budgetId)
                ->where(function($query) {
                    $query->where('status', '!=', 'paid')
                          ->where(function($q) {
                              $q->whereIn('approval_status', ['pending', 'approved'])
                                ->orWhereNull('approval_status');
                          });
                })
                ->sum('amount');
            
            // Use totals from ALL budgets for the fund summary
            $usedAmount = $totalUsedFromAllBudgets;
            $pendingExpensesAmount = $totalPendingFromAllBudgets;
            
            // Debug logging
            \Log::info('Fund Summary Calculation', [
                'budget_id' => $budgetId,
                'budget_name' => $budget->budget_name,
                'primary_offering_type' => $primaryOfferingType,
                'total_income' => $totalIncome,
                'all_budgets_with_offering' => $allBudgetsWithSameOffering->toArray(),
                'total_used_from_all_budgets' => $totalUsedFromAllBudgets,
                'total_pending_from_all_budgets' => $totalPendingFromAllBudgets,
                'used_amount_for_this_budget' => $usedAmountForThisBudget,
                'pending_expenses_for_this_budget' => $pendingExpensesForThisBudget,
                'calculated_available' => $totalIncome - $usedAmount - $pendingExpensesAmount,
            ]);
            
            // Calculate available amount (subtract both used and pending)
            $availableAmount = $totalIncome - $usedAmount - $pendingExpensesAmount;
            
            // Calculate total committed (used + pending)
            // For display purposes, "used_amount" should show total committed (paid + pending)
            $totalCommitted = $usedAmount + $pendingExpensesAmount;
            
            // Calculate percentages for progress bar
            $usedPercentage = $totalIncome > 0 ? ($usedAmount / $totalIncome) * 100 : 0;
            $pendingPercentage = $totalIncome > 0 ? ($pendingExpensesAmount / $totalIncome) * 100 : 0;
            $committedPercentage = $totalIncome > 0 ? ($totalCommitted / $totalIncome) * 100 : 0;
            $availablePercentage = $totalIncome > 0 ? ($availableAmount / $totalIncome) * 100 : 0;
            
            return response()->json([
                'success' => true,
                'fund_summary' => [
                    'offering_type' => $primaryOfferingType,
                    'total_income' => $totalIncome,
                    'used_amount' => $totalCommitted, // Show total committed (paid + pending) as "used"
                    'paid_amount' => $usedAmount, // Actual paid amount
                    'pending_expenses_amount' => $pendingExpensesAmount,
                    'total_committed' => $totalCommitted,
                    'available_amount' => $availableAmount,
                    'used_percentage' => round($committedPercentage, 2), // Show committed percentage
                    'paid_percentage' => round($usedPercentage, 2),
                    'pending_percentage' => round($pendingPercentage, 2),
                    'committed_percentage' => round($committedPercentage, 2),
                    'available_percentage' => round($availablePercentage, 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Fund summary calculation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error calculating fund summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fund breakdown for expense amount
     */
    public function getFundBreakdown(Request $request, $budgetId)
    {
        try {
            $budget = Budget::findOrFail($budgetId);
            $expenseAmount = $request->get('amount', 0);
            
            if ($expenseAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid expense amount'
                ], 400);
            }
            
            // Get current available funds
            $currentFunds = [];
            $totalAvailable = 0;
            
            if ($budget->isFullyFunded()) {
                // Budget is funded - get from allocations (accounting for pending expenses)
                $fundingSummary = $this->budgetFundingService->getBudgetFundingSummary($budget);
                foreach ($fundingSummary['breakdown'] as $breakdown) {
                    // Use remaining amount which already accounts for pending expenses
                    $available = $breakdown['remaining'] ?? 0;
                    if ($available > 0) {
                        $currentFunds[$breakdown['offering_type']] = $available;
                        $totalAvailable += $available;
                    }
                }
            } else {
                // Budget is not funded - get from available offering funds
                $availableOfferings = $this->budgetFundingService->getAvailableAmountsAfterAllocations();
                $primaryOfferingType = $budget->primary_offering_type;
                
                if ($primaryOfferingType && isset($availableOfferings[$primaryOfferingType])) {
                    $currentFunds[$primaryOfferingType] = $availableOfferings[$primaryOfferingType];
                    $totalAvailable = $availableOfferings[$primaryOfferingType];
                }
            }
            
            // Calculate how the expense would be paid from current funds
            $fundAllocation = $this->calculateFundAllocation($budget, $expenseAmount, $currentFunds);
            
            // Calculate shortfall
            $shortfall = max(0, $expenseAmount - $totalAvailable);
            
            // Calculate how much from primary offering will be used
            $primaryOfferingUsed = 0;
            $primaryOfferingAvailable = 0;
            if ($budget->primary_offering_type) {
                $primaryOfferingAvailable = $currentFunds[$budget->primary_offering_type] ?? 0;
                // Use the full available amount from primary offering (up to expense amount)
                $primaryOfferingUsed = min($primaryOfferingAvailable, $expenseAmount);
            }
            
            // Calculate remaining shortfall after using primary offering
            $remainingShortfall = max(0, $expenseAmount - $primaryOfferingUsed);
            
            // Get available offering types for manual selection when insufficient
            $availableOfferingTypes = [];
            if ($remainingShortfall > 0) {
                $availableOfferingTypes = $this->budgetFundingService->getAvailableAmountsAfterAllocations();
                // Remove the primary offering type from available options (it's already being used)
                unset($availableOfferingTypes[$budget->primary_offering_type]);
            }
            
            return response()->json([
                'success' => true,
                'current_funds' => $currentFunds,
                'fund_allocation' => $fundAllocation, // How the expense will be paid from current funds
                'total_available' => $totalAvailable,
                'expense_amount' => $expenseAmount,
                'shortfall' => $shortfall,
                'primary_offering_type' => $budget->primary_offering_type,
                'primary_offering_available' => $primaryOfferingAvailable,
                'primary_offering_used' => $primaryOfferingUsed, // Amount that will be used from primary offering
                'remaining_shortfall' => $remainingShortfall, // Shortfall after using primary offering
                'available_offering_types' => $availableOfferingTypes, // Available types for manual selection
                'is_sufficient' => $totalAvailable >= $expenseAmount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Fund breakdown calculation error: ' . $e->getMessage());
            \Log::error('Budget ID: ' . $budgetId . ', Amount: ' . $expenseAmount);
            \Log::error('Budget funded: ' . ($budget->isFullyFunded() ? 'Yes' : 'No'));
            \Log::error('Primary offering type: ' . $budget->primary_offering_type);
            
            return response()->json([
                'success' => false,
                'message' => 'Error calculating fund breakdown: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate how an expense amount would be allocated from available funds
     * Uses ONLY primary offering type for normal payments
     */
    private function calculateFundAllocation($budget, $expenseAmount, $currentFunds)
    {
        $allocation = [];
        $remainingAmount = $expenseAmount;
        
        // ONLY use primary offering type for the expense payment
        if ($budget->primary_offering_type && isset($currentFunds[$budget->primary_offering_type])) {
            $primaryAvailable = $currentFunds[$budget->primary_offering_type];
            $primaryAllocation = min($remainingAmount, $primaryAvailable);
            
            if ($primaryAllocation > 0) {
                $allocation[] = [
                    'offering_type' => $budget->primary_offering_type,
                    'amount' => $primaryAllocation,
                    'is_primary' => true
                ];
                $remainingAmount -= $primaryAllocation;
            }
        }
        
        // Note: We don't automatically allocate from other offering types
        // This will be handled manually by the user when there's insufficient funds
        
        return $allocation;
    }

    /**
     * Allocate additional funding to budget
     */
    private function allocateAdditionalFunding($budget, $additionalFunding)
    {
        try {
            $allocations = [];
            foreach ($additionalFunding as $funding) {
                if ($funding['amount'] > 0) {
                    $allocations[$funding['offering_type']] = $funding['amount'];
                }
            }

            if (!empty($allocations)) {
                $this->budgetFundingService->allocateFundsToBudget($budget, $allocations);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to allocate additional funding: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing budget
     */
    public function updateBudget(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'budget_name' => 'required|string',
            'budget_type' => 'required|string',
            'fiscal_year' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,completed'
        ]);

        $budget->update($validated);
        return redirect()->route('finance.budgets')
            ->with('success', 'Budget updated successfully');
    }

    /**
     * Delete a budget
     */
    public function destroyBudget(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('finance.budgets')
            ->with('success', 'Budget deleted successfully');
    }
    
    /**
     * Display expenses management
     */
    public function expenses(Request $request)
    {
        $query = Expense::with('budget');
        
        // Apply filters
        if ($request->filled('expense_category')) {
            $query->where('expense_category', $request->expense_category);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        $budgets = Budget::active()->approved()->get();
        
        // Calculate pending expenses for each budget (includes both pending and approved expenses)
        $budgetIds = $budgets->pluck('id');
        $pendingExpensesByBudget = Expense::whereIn('budget_id', $budgetIds)
            ->where(function($query) {
                $query->where('status', '!=', 'paid')
                      ->where(function($q) {
                          $q->whereIn('approval_status', ['pending', 'approved'])
                            ->orWhereNull('approval_status'); // Include NULL for backward compatibility
                      });
            })
            ->groupBy('budget_id')
            ->selectRaw('budget_id, SUM(amount) as pending_amount')
            ->pluck('pending_amount', 'budget_id');
        
        // Add pending expenses data to each budget
        $budgets->each(function($budget) use ($pendingExpensesByBudget) {
            $budget->pending_expenses_amount = $pendingExpensesByBudget[$budget->id] ?? 0;
            $budget->total_committed = $budget->spent_amount + $budget->pending_expenses_amount;
            $budget->remaining_with_pending = $budget->total_budget - $budget->total_committed;
        });
        
        $totalMembers = Member::count();
        
        // Get pastor information for approval messages
        $pastor = \App\Models\User::where('can_approve_finances', true)
            ->orWhere('role', 'pastor')
            ->orWhere('role', 'admin')
            ->first();
        
        return view('finance.expenses', compact('expenses', 'budgets', 'totalMembers', 'pastor'));
    }
    
    /**
     * Store a new expense
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'budget_id' => 'nullable|exists:budgets,id',
            'expense_category' => 'required|string',
            'expense_name' => 'required|string',
            'amount' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    // If budget is selected, validate that expense amount doesn't exceed budget total
                    if ($request->filled('budget_id')) {
                        $budget = Budget::find($request->budget_id);
                        if ($budget) {
                            // Check if expense amount itself exceeds budget total
                            if ($value > $budget->total_budget) {
                                $fail("The expense amount (TZS " . number_format($value) . ") cannot exceed the budget total amount (TZS " . number_format($budget->total_budget) . ").");
                            }
                            
                            // Also check if expense would exceed budget when combined with already spent
                            $currentSpent = $budget->spent_amount;
                            $newTotalSpent = $currentSpent + $value;
                            
                            if ($newTotalSpent > $budget->total_budget) {
                                $remainingBudget = $budget->total_budget - $currentSpent;
                                $fail("The expense amount (TZS " . number_format($value) . ") would exceed the budget limit. Remaining budget: TZS " . number_format($remainingBudget) . ". Budget total: TZS " . number_format($budget->total_budget) . ".");
                            }
                        }
                    }
                }
            ],
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'additional_funding' => 'nullable|array',
            'additional_funding.*.offering_type' => 'required_with:additional_funding|string',
            'additional_funding.*.amount' => 'required_with:additional_funding|numeric|min:0'
        ]);
        
        $validated['recorded_by'] = auth()->user()->name ?? 'System';
        $validated['status'] = 'pending';
        $validated['approval_status'] = 'pending'; // Set to pending for pastor approval
        
        $expense = Expense::create($validated);
        
        // Refresh the expense to ensure all fields are loaded correctly
        $expense->refresh();
        
        // Log expense creation for debugging
        \Log::info('Expense created for approval notification', [
            'expense_id' => $expense->id,
            'amount' => $expense->amount,
            'expense_name' => $expense->expense_name,
            'budget_id' => $expense->budget_id
        ]);
        
        $requiresFundingApproval = false;
        $fundingRequest = null;
        $fundBreakdown = null;
        
        // Check if user provided additional funding data
        $additionalFunding = $request->input('additional_funding', []);
        $hasAdditionalFunding = !empty($additionalFunding) && is_array($additionalFunding);
        
        // If expense is linked to a budget, validate budget constraints
        if ($expense->budget_id) {
            $budget = Budget::find($expense->budget_id);
            if ($budget) {
                try {
                    // Additional validation (already done in validation rules, but keeping for safety)
                    $currentSpent = $budget->spent_amount;
                    $newTotalSpent = $currentSpent + $expense->amount;
                    
                    if ($newTotalSpent > $budget->total_budget) {
                        $remainingBudget = $budget->total_budget - $currentSpent;
                        return redirect()->back()
                            ->withInput()
                            ->with('error', "Expense amount (TZS " . number_format($expense->amount) . ") would exceed budget limit. Remaining budget: TZS " . number_format($remainingBudget));
                    }
                    
                    // If user provided additional funding, use it to create fund breakdown
                    if ($hasAdditionalFunding) {
                        $totalAdditionalFunding = 0;
                        $fundBreakdown = [];
                        
                        foreach ($additionalFunding as $funding) {
                            if (!empty($funding['offering_type']) && !empty($funding['amount'])) {
                                $amount = floatval($funding['amount']);
                                
                                // Ensure amount doesn't exceed expense amount (safety check)
                                // Each offering type should only contribute its actual allocated amount
                                if ($amount > 0 && $amount <= $expense->amount) {
                                    $totalAdditionalFunding += $amount;
                                    $fundBreakdown[] = [
                                        'offering_type' => $funding['offering_type'],
                                        'amount' => $amount, // Store actual amount used from this offering type
                                        'is_primary' => isset($funding['is_primary']) ? (bool)$funding['is_primary'] : false
                                    ];
                                }
                            }
                        }
                        
                        // Verify that total funding matches expense amount (with small tolerance for rounding)
                        $fundingTotal = array_sum(array_column($fundBreakdown, 'amount'));
                        $difference = abs($fundingTotal - $expense->amount);
                        
                        \Log::info('Additional funding provided by user', [
                            'expense_id' => $expense->id,
                            'expense_amount' => $expense->amount,
                            'total_additional_funding' => $totalAdditionalFunding,
                            'funding_total' => $fundingTotal,
                            'difference' => $difference,
                            'fund_breakdown' => $fundBreakdown
                        ]);
                        
                        // Store fund breakdown in expense for pastor review
                        // Each entry contains the actual amount used from that specific offering type
                        $expense->update([
                            'approval_notes' => 'Fund allocation with additional funding: ' . json_encode($fundBreakdown)
                        ]);
                        
                        // Send notification to pastors about pending expense with fund breakdown
                        $this->sendFinancialApprovalNotification('expense', $expense, $fundBreakdown);
                        
                        return redirect()->route('finance.expenses')
                            ->with('success', 'Expense recorded successfully with additional funding sources and sent for pastor approval.');
                    }
                    
                    // Check if budget is funded
                    if ($budget->isFullyFunded()) {
                        // Budget is funded - check allocated funds
                        $fundingSummary = $this->budgetFundingService->getBudgetFundingSummary($budget);
                        $availableFunds = $fundingSummary['remaining_allocated'];
                        
                        if ($availableFunds >= $expense->amount) {
                            // Sufficient allocated funds exist - show fund breakdown and send for approval
                            $fundBreakdown = $this->calculateFundBreakdown($budget, $expense->amount);
                            
                            // Store fund breakdown in expense for pastor review
                            $expense->update([
                                'approval_notes' => 'Fund allocation: ' . json_encode($fundBreakdown)
                            ]);
                            
                            // Send notification to pastors about pending expense with fund breakdown
                            $this->sendFinancialApprovalNotification('expense', $expense, $fundBreakdown);
                            
                            return redirect()->route('finance.expenses')
                                ->with('success', 'Expense recorded successfully and sent for pastor approval with fund allocation details.');
                        } else {
                            // Insufficient allocated funds - create funding request
                            $requiresFundingApproval = true;
                            $fundingRequest = $this->createFundingRequest($expense, $budget, $fundingSummary);
                        }
                    } else {
                        // Budget is not funded - check if primary offering type has sufficient funds
                        $primaryOfferingType = $budget->primary_offering_type;
                        $availableOfferings = $this->budgetFundingService->getAvailableAmountsAfterAllocations();
                        $primaryAvailable = $availableOfferings[$primaryOfferingType] ?? 0;
                        
                        \Log::info('Budget not funded - checking primary offering', [
                            'budget_id' => $budget->id,
                            'primary_offering_type' => $primaryOfferingType,
                            'primary_available' => $primaryAvailable,
                            'expense_amount' => $expense->amount,
                            'available_offerings' => $availableOfferings,
                            'budget_name' => $budget->budget_name,
                            'budget_purpose' => $budget->purpose
                        ]);
                        
                        if ($primaryAvailable >= $expense->amount) {
                            // Sufficient funds in primary offering type - allocate and approve
                            $this->budgetFundingService->allocateFundsToBudget($budget, [$primaryOfferingType => $expense->amount]);
                            
                            $fundBreakdown = [[
                                'offering_type' => $primaryOfferingType,
                                'amount' => $expense->amount,
                                'is_primary' => true
                            ]];
                            
                            $expense->update([
                                'approval_notes' => 'Fund allocation: ' . json_encode($fundBreakdown)
                            ]);
                            
                            $this->sendFinancialApprovalNotification('expense', $expense, $fundBreakdown);
                            
                            return redirect()->route('finance.expenses')
                                ->with('success', 'Expense recorded successfully and sent for pastor approval with fund allocation details.');
                        } else {
                            // Insufficient funds in primary offering type - create funding request
                            $requiresFundingApproval = true;
                            $fundingRequest = $this->createFundingRequest($expense, $budget, null);
                        }
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error checking budget constraints: ' . $e->getMessage());
                }
            }
        }
        
        if ($requiresFundingApproval && $fundingRequest) {
            // Send notification about funding request instead of expense approval
            $this->sendFundingRequestNotification($fundingRequest);
            
            return redirect()->route('finance.expenses')
                ->with('info', 'Expense recorded but requires additional funding approval. Funding request has been sent to pastors.');
        } else {
            // Send notification to pastors about pending expense (for non-budget expenses)
            $this->sendFinancialApprovalNotification('expense', $expense);
            
            return redirect()->route('finance.expenses')
                ->with('success', 'Expense recorded successfully and sent for pastor approval');
        }
    }

    /**
     * Update an existing expense
     */
    public function updateExpense(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'budget_id' => 'nullable|exists:budgets,id',
            'expense_category' => 'required|string',
            'expense_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,paid'
        ]);

        $expense->update($validated);

        return redirect()->route('finance.expenses')
            ->with('success', 'Expense updated successfully');
    }
    
    /**
     * Mark expense as paid (only after pastor approval, only by treasurer)
     */
    public function markExpensePaid(Expense $expense)
    {
        // Check if user is treasurer or admin (only treasurers can mark expenses as paid)
        $user = auth()->user();
        if (!$user->isTreasurer() && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only treasurers can mark expenses as paid'
            ], 403);
        }
        
        // Check if expense is approved by pastor
        if ($expense->approval_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Expense must be approved by pastor before marking as paid'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            // Check if expense has additional funding information
            $hasAdditionalFunding = false;
            $additionalFunding = [];
            
            if ($expense->approval_notes) {
                if (strpos($expense->approval_notes, 'additional funding') !== false || 
                    strpos($expense->approval_notes, 'Fund allocation with additional funding') !== false) {
                    // Try to extract JSON from approval_notes
                    if (preg_match('/Fund allocation[^:]*:\s*(\[.*\])/s', $expense->approval_notes, $matches) || 
                        preg_match('/:\s*(\[.*\])/s', $expense->approval_notes, $matches)) {
                        try {
                            $fundBreakdown = json_decode($matches[1], true);
                            if (is_array($fundBreakdown) && !empty($fundBreakdown)) {
                                $hasAdditionalFunding = true;
                                $additionalFunding = $fundBreakdown;
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Failed to parse additional funding from approval_notes', [
                                'expense_id' => $expense->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            // Final validation before marking as paid
            if ($expense->budget_id) {
                $budget = Budget::find($expense->budget_id);
                if ($budget) {
                    // Check if marking this expense as paid would exceed budget
                    $currentSpent = $budget->spent_amount;
                    $newTotalSpent = $currentSpent + $expense->amount;
                    
                    if ($newTotalSpent > $budget->total_budget) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot mark as paid: Would exceed budget limit'
                        ], 400);
                    }
                    
                    // If expense has additional funding, allocate those funds first
                    $additionalFundingAllocations = [];
                    if ($hasAdditionalFunding && !empty($additionalFunding)) {
                        // Allocate additional funding to the budget
                        $allocations = [];
                        foreach ($additionalFunding as $funding) {
                            if (!empty($funding['offering_type']) && !empty($funding['amount'])) {
                                $offeringType = $funding['offering_type'];
                                $amount = floatval($funding['amount']);
                                $allocations[$offeringType] = ($allocations[$offeringType] ?? 0) + $amount;
                                
                                // Track which allocations were created for this expense
                                $additionalFundingAllocations[$offeringType] = ($additionalFundingAllocations[$offeringType] ?? 0) + $amount;
                            }
                        }
                        
                        if (!empty($allocations)) {
                            \Log::info('Allocating additional funding for expense payment', [
                                'expense_id' => $expense->id,
                                'allocations' => $allocations
                            ]);
                            
                            $this->budgetFundingService->allocateFundsToBudget($budget, $allocations);
                            
                            // Refresh budget to get updated allocations
                            $budget->refresh();
                        }
                    }
                    
                    // Since expense is approved by pastor, funding has been verified
                    // Only do a basic check - if budget is fully funded, ensure we have allocations
                    // But don't block if pastor has already approved (they've verified funding)
                    if ($budget->isFullyFunded()) {
                        $fundingSummary = $this->budgetFundingService->getBudgetFundingSummary($budget);
                        $remainingAllocated = $fundingSummary['remaining_allocated'];
                        
                        // If we still don't have enough after allocating additional funding, 
                        // and there are no allocations at all, try to allocate from primary offering type
                        if ($remainingAllocated < $expense->amount) {
                            // Check if we have any allocations
                            $hasAllocations = $budget->offeringAllocations()->exists();
                            
                            if (!$hasAllocations && $budget->primary_offering_type) {
                                // Try to allocate from primary offering type as a fallback
                                $availableOfferings = $this->budgetFundingService->getAvailableAmountsAfterAllocations();
                                $primaryAvailable = $availableOfferings[$budget->primary_offering_type] ?? 0;
                                
                                if ($primaryAvailable >= $expense->amount) {
                                    $this->budgetFundingService->allocateFundsToBudget($budget, [
                                        $budget->primary_offering_type => $expense->amount
                                    ]);
                                    $budget->refresh();
                                }
                            }
                            
                            // Final check - if still insufficient, log warning but proceed since pastor approved
                            $fundingSummary = $this->budgetFundingService->getBudgetFundingSummary($budget);
                            $remainingAllocated = $fundingSummary['remaining_allocated'];
                            
                            if ($remainingAllocated < $expense->amount) {
                                \Log::warning('Insufficient allocated funds but proceeding because expense is pastor-approved', [
                                    'expense_id' => $expense->id,
                                    'expense_amount' => $expense->amount,
                                    'remaining_allocated' => $remainingAllocated,
                                    'has_additional_funding' => $hasAdditionalFunding
                                ]);
                                // Don't block - pastor has already approved, funding is verified
                            }
                        }
                    }
                }
            }
            
            $expense->update(['status' => 'paid']);
            
            // Update budget spent amount and deduct from offering allocations
            if ($expense->budget_id) {
                $budget = Budget::find($expense->budget_id);
                if ($budget) {
                    $budget->increment('spent_amount', $expense->amount);
                    
                    // Deduct from offering allocations if there are any allocations
                    $hasAllocations = $budget->offeringAllocations()->exists();
                    if ($hasAllocations) {
                        try {
                            // If we have additional funding allocations, use those first, then use remaining allocations
                            if (!empty($additionalFundingAllocations)) {
                                $this->deductExpenseWithSpecificAllocations($budget, $expense->amount, $additionalFundingAllocations);
                            } else {
                                $this->budgetFundingService->deductExpenseFromAllocations($budget, $expense->amount);
                            }
                        } catch (\Exception $e) {
                            // If deduction fails, log but don't block since pastor approved
                            \Log::warning('Failed to deduct from allocations but proceeding because expense is pastor-approved', [
                                'expense_id' => $expense->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Expense marked as paid successfully' . ($hasAdditionalFunding ? ' (Additional funding allocated)' : '')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Failed to mark expense as paid', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark expense as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an expense
     */
    public function destroyExpense(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('finance.expenses')
            ->with('success', 'Expense deleted successfully');
    }

    /**
     * Send notification to pastors about pending financial approval
     */
    private function sendFinancialApprovalNotification($type, $record, $fundBreakdown = null)
    {
        try {
            // Get all users who can approve finances (pastors)
            $pastors = \App\Models\User::where('can_approve_finances', true)
                ->orWhere('role', 'pastor')
                ->orWhere('role', 'admin')
                ->get();

            if ($pastors->isEmpty()) {
                \Log::warning('No pastors found to send financial approval notification');
                return;
            }

            // Handle pledge payments differently (member is accessed via pledge relationship)
            $memberName = 'General Member';
            $date = $record->created_at;
            $amount = 0;
            
            if ($type === 'pledge_payment') {
                // For pledge payments, member is accessed via pledge relationship
                $memberName = $record->pledge->member->full_name ?? 'General Member';
                $date = $record->payment_date ?? $record->created_at;
                $amount = $record->amount ?? 0;
            } elseif ($type === 'budget') {
                // For budgets, use total_budget
                $amount = $record->total_budget ?? 0;
                $memberName = 'Budget: ' . ($record->budget_name ?? 'N/A');
            } elseif ($type === 'expense') {
                // For expenses, ensure amount is properly retrieved
                $amount = $record->amount ?? 0;
                // Expenses might not have a member, use expense name instead
                $memberName = $record->expense_name ?? 'General Expense';
            } else {
                // For other types (tithe, offering, donation, pledge), member is directly on the record
                $memberName = $record->member->full_name ?? 'General Member';
                $date = $record->offering_date ?? $record->tithe_date ?? $record->donation_date ?? $record->expense_date ?? $record->created_at;
                $amount = $record->amount ?? 0;
            }
            
            // Log for debugging if amount is 0
            if ($amount == 0) {
                \Log::warning('Financial approval notification with zero amount', [
                    'type' => $type,
                    'record_id' => $record->id,
                    'record_class' => get_class($record),
                    'record_data' => $record->toArray()
                ]);
            }
            
            // Create notification data
            $notificationData = [
                'type' => $type,
                'record_id' => $record->id,
                'amount' => $amount,
                'date' => $date,
                'recorded_by' => $record->recorded_by ?? 'System',
                'member_name' => $memberName,
                'fund_breakdown' => $fundBreakdown,
                'created_at' => now()
            ];

            // Send notification to each pastor
            foreach ($pastors as $pastor) {
                try {
                    $pastor->notify(new \App\Notifications\FinancialApprovalNotification($notificationData));
                    \Log::info("Financial approval notification sent to pastor", [
                        'pastor_id' => $pastor->id,
                        'pastor_name' => $pastor->name,
                        'type' => $type,
                        'record_id' => $record->id,
                        'has_fund_breakdown' => !is_null($fundBreakdown)
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Failed to send financial approval notification to pastor {$pastor->id}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send financial approval notification: ' . $e->getMessage());
        }
    }

    /**
     * Create a funding request for insufficient funds
     */
    private function createFundingRequest($expense, $budget, $fundingSummary = null)
    {
        try {
            $requestedAmount = $expense->amount;
            $availableAmount = $fundingSummary ? $fundingSummary['remaining_allocated'] : 0;
            $shortfallAmount = $requestedAmount - $availableAmount;

            // Get suggested allocations from other offering types
            $suggestedAllocations = $this->budgetFundingService->suggestFundingAllocation($budget, $shortfallAmount);

            $fundingRequest = FundingRequest::create([
                'expense_id' => $expense->id,
                'budget_id' => $budget->id,
                'requested_amount' => $requestedAmount,
                'available_amount' => $availableAmount,
                'shortfall_amount' => $shortfallAmount,
                'reason' => $budget->isFullyFunded() 
                    ? "Insufficient allocated funds for expense: {$expense->expense_name}"
                    : "Budget not fully funded. Current funding: {$budget->funding_percentage}%",
                'suggested_allocations' => $suggestedAllocations['suggestions'],
                'status' => 'pending'
            ]);

            return $fundingRequest;

        } catch (\Exception $e) {
            \Log::error('Failed to create funding request: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Deduct expense from specific allocations (prioritizing additional funding allocations)
     */
    private function deductExpenseWithSpecificAllocations(Budget $budget, $expenseAmount, array $preferredAllocations)
    {
        DB::beginTransaction();
        
        try {
            $remainingAmount = $expenseAmount;
            
            // First, deduct from preferred allocations (additional funding sources)
            foreach ($preferredAllocations as $offeringType => $preferredAmount) {
                if ($remainingAmount <= 0) break;
                
                // Find allocations for this offering type that have available funds
                $allocations = $budget->offeringAllocations()
                    ->where('offering_type', $offeringType)
                    ->whereRaw('allocated_amount > used_amount')
                    ->orderBy('created_at', 'asc') // Use oldest allocations first
                    ->get();
                
                foreach ($allocations as $allocation) {
                    if ($remainingAmount <= 0) break;
                    
                    $availableInAllocation = $allocation->allocated_amount - $allocation->used_amount;
                    $deductionAmount = min($remainingAmount, $availableInAllocation, $preferredAmount);
                    
                    if ($deductionAmount > 0) {
                        $allocation->increment('used_amount', $deductionAmount);
                        $remainingAmount -= $deductionAmount;
                        $preferredAmount -= $deductionAmount;
                        
                        \Log::info("Deducted {$deductionAmount} from {$offeringType} allocation for expense", [
                            'allocation_id' => $allocation->id,
                            'remaining_expense' => $remainingAmount
                        ]);
                    }
                }
            }
            
            // If there's still remaining amount, deduct from other allocations
            if ($remainingAmount > 0) {
                $otherAllocations = $budget->offeringAllocations()
                    ->whereNotIn('offering_type', array_keys($preferredAllocations))
                    ->whereRaw('allocated_amount > used_amount')
                    ->orderBy('is_primary', 'desc')
                    ->orderBy('allocated_amount', 'desc')
                    ->get();
                
                foreach ($otherAllocations as $allocation) {
                    if ($remainingAmount <= 0) break;
                    
                    $availableInAllocation = $allocation->allocated_amount - $allocation->used_amount;
                    $deductionAmount = min($remainingAmount, $availableInAllocation);
                    
                    if ($deductionAmount > 0) {
                        $allocation->increment('used_amount', $deductionAmount);
                        $remainingAmount -= $deductionAmount;
                        
                        \Log::info("Deducted {$deductionAmount} from {$allocation->offering_type} allocation (secondary) for expense", [
                            'allocation_id' => $allocation->id,
                            'remaining_expense' => $remainingAmount
                        ]);
                    }
                }
            }
            
            if ($remainingAmount > 0) {
                throw new \Exception("Insufficient allocated funds to cover expense. Remaining: {$remainingAmount}");
            }
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Expense deduction with specific allocations failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Calculate fund breakdown for expense payment
     */
    private function calculateFundBreakdown($budget, $expenseAmount)
    {
        $allocations = $budget->offeringAllocations()
            ->whereRaw('allocated_amount > used_amount')
            ->orderBy('is_primary', 'desc')
            ->orderBy('allocated_amount', 'desc')
            ->get();

        $breakdown = [];
        $remainingAmount = $expenseAmount;

        foreach ($allocations as $allocation) {
            if ($remainingAmount <= 0) break;

            $availableInAllocation = $allocation->allocated_amount - $allocation->used_amount;
            $deductionAmount = min($remainingAmount, $availableInAllocation);

            if ($deductionAmount > 0) {
                $breakdown[] = [
                    'offering_type' => $allocation->offering_type,
                    'amount' => $deductionAmount,
                    'is_primary' => $allocation->is_primary
                ];
                $remainingAmount -= $deductionAmount;
            }
        }

        return $breakdown;
    }

    /**
     * Send notification about funding request
     */
    private function sendFundingRequestNotification($fundingRequest)
    {
        try {
            // Get all users who can approve finances (pastors)
            $pastors = \App\Models\User::where('can_approve_finances', true)
                ->orWhere('role', 'pastor')
                ->orWhere('role', 'admin')
                ->get();

            if ($pastors->isEmpty()) {
                \Log::warning('No pastors found to send funding request notification');
                return;
            }

            // Create notification data
            $notificationData = [
                'type' => 'funding_request',
                'request_id' => $fundingRequest->id,
                'expense_name' => $fundingRequest->expense->expense_name,
                'budget_name' => $fundingRequest->budget->budget_name,
                'requested_amount' => $fundingRequest->requested_amount,
                'available_amount' => $fundingRequest->available_amount,
                'shortfall_amount' => $fundingRequest->shortfall_amount,
                'reason' => $fundingRequest->reason,
                'suggested_allocations' => $fundingRequest->suggested_allocations,
                'created_at' => now()
            ];

            // Send notification to each pastor
            foreach ($pastors as $pastor) {
                try {
                    $pastor->notify(new \App\Notifications\FundingRequestNotification($notificationData));
                    \Log::info("Funding request notification sent to pastor", [
                        'pastor_id' => $pastor->id,
                        'pastor_name' => $pastor->name,
                        'request_id' => $fundingRequest->id
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Failed to send funding request notification to pastor {$pastor->id}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send funding request notification: ' . $e->getMessage());
        }
    }
}
