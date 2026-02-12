<?php

namespace App\Http\Controllers;

use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\Member;
use App\Models\Leader;
use App\Models\FundingRequest;
use App\Models\CommunityOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialApprovalController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level
    }

    /**
     * Check if user can view approval dashboard (secretary can view but not approve)
     */
    private function checkViewPermission()
    {
        if (!auth()->check()) {
            abort(401, 'Please log in to access this page.');
        }

        $user = auth()->user();

        // Secretary, Pastor, Admin can view
        $canView = false;

        if ($user->role === 'secretary') {
            $canView = true;
        }

        if ($user->role === 'pastor') {
            $canView = true;
        }

        if ($user->role === 'admin') {
            $canView = true;
        }

        if ($user->can_approve_finances) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, 'Unauthorized access. Only Secretaries, Pastors and authorized users can view financial approval records.');
        }
    }

    /**
     * Check if user can approve financial records (secretary, pastor/admin)
     */
    private function checkApprovalPermission()
    {
        if (!auth()->check()) {
            abort(401, 'Please log in to access this page.');
        }

        $user = auth()->user();

        // Simple permission check - secretary, pastor/admin can approve
        $canApprove = false;

        // Check if user is secretary
        if ($user->role === 'secretary') {
            $canApprove = true;
        }

        // Check if user has explicit approval permission
        if ($user->can_approve_finances) {
            $canApprove = true;
        }

        // Check if user is pastor
        if ($user->role === 'pastor') {
            $canApprove = true;
        }

        // Check if user is admin
        if ($user->role === 'admin') {
            $canApprove = true;
        }

        if (!$canApprove) {
            abort(403, 'Unauthorized access. Only Secretaries, Pastors and authorized users can approve financial records.');
        }
    }

    /**
     * Display the approval dashboard (Secretary can view, Pastor/Admin can approve)
     */
    public function dashboard()
    {
        $this->checkViewPermission();
        $today = Carbon::today();

        // Get pending records (show all pending, not just today's)
        $pendingTithes = Tithe::with(['member', 'approver', 'evangelismLeader', 'campus'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingOfferings = Offering::with(['member', 'approver', 'evangelismLeader', 'campus'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingDonations = Donation::with(['member', 'approver'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get expenses that have sufficient funds or have additional funding provided
        // Show all pending expenses, not just today's
        $pendingExpenses = Expense::with(['budget', 'approver'])
            ->where('approval_status', 'pending')
            ->where(function ($query) {
                $query->whereNull('budget_id') // Non-budget expenses are always fundable
                    ->orWhere('approval_notes', 'LIKE', '%additional funding%') // Include expenses with additional funding
                    ->orWhere('approval_notes', 'LIKE', '%Fund allocation with additional funding%') // Include expenses with additional funding
                    ->orWhereHas('budget', function ($budgetQuery) {
                        // Only include budget expenses that have sufficient allocated funds
                        $budgetQuery->whereRaw('
                              (SELECT COALESCE(SUM(allocated_amount - used_amount), 0) 
                               FROM budget_offering_allocations 
                               WHERE budget_id = budgets.id) >= expenses.amount
                          ');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingBudgets = Budget::with(['approver'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending pledge payments (not pledges themselves)
        $pendingPledgePayments = PledgePayment::with(['pledge.member', 'approver'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending community offerings (from Evangelism Leader, ready for Secretary)
        $pendingCommunityOfferings = CommunityOffering::with(['community', 'service', 'evangelismLeader', 'churchElder'])
            ->where('status', 'pending_secretary')
            ->orderBy('handover_to_evangelism_at', 'asc')
            ->get();

        // Get pending funding requests
        $pendingFundingRequests = FundingRequest::with(['expense', 'budget', 'approver'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get summary statistics
        $totalPending = $pendingTithes->count() + $pendingOfferings->count() +
            $pendingDonations->count() + $pendingExpenses->count() +
            $pendingBudgets->count() + $pendingPledgePayments->count() +
            $pendingCommunityOfferings->count() + $pendingFundingRequests->count();

        $totalPendingAmount = $pendingTithes->sum('amount') + $pendingOfferings->sum('amount') +
            $pendingDonations->sum('amount') + $pendingExpenses->sum('amount') +
            $pendingPledgePayments->sum('amount') + $pendingCommunityOfferings->sum('amount') +
            $pendingFundingRequests->sum('requested_amount');

        // Get recent approvals (last 7 days)
        $recentApprovals = collect();

        $recentApprovals = $recentApprovals->merge(
            Tithe::with(['member', 'approver'])
                ->where('approval_status', 'approved')
                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->map(function ($item) {
                    $item->type = 'Tithe';
                    $item->date = $item->tithe_date;
                    return $item;
                })
        );

        $recentApprovals = $recentApprovals->merge(
            Offering::with(['member', 'approver'])
                ->where('approval_status', 'approved')
                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->map(function ($item) {
                    $item->type = 'Offering';
                    $item->date = $item->offering_date;
                    return $item;
                })
        );

        $recentApprovals = $recentApprovals->merge(
            Donation::with(['member', 'approver'])
                ->where('approval_status', 'approved')
                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->map(function ($item) {
                    $item->type = 'Donation';
                    $item->date = $item->donation_date;
                    return $item;
                })
        );

        $recentApprovals = $recentApprovals->merge(
            PledgePayment::with(['pledge.member', 'approver'])
                ->where('approval_status', 'approved')
                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->map(function ($item) {
                    $item->type = 'Pledge Payment';
                    $item->date = $item->payment_date;
                    $item->member = $item->pledge->member ?? null;
                    $item->amount = $item->amount;
                    return $item;
                })
        );

        $recentApprovals = $recentApprovals->sortByDesc('approved_at')->take(10);

        // Process recent approvals to get approver display names from Leader/Member relationship
        $recentApprovals = $recentApprovals->map(function ($record) {
            if ($record->approver) {
                $approverUser = $record->approver;
                $approverDisplayName = $approverUser->name; // Default to user's name

                // Try to find the member by email match
                $member = Member::where('email', $approverUser->email)->first();

                if ($member) {
                    // Check if this member is assigned as a pastor
                    $pastorLeader = Leader::with('member')
                        ->where('member_id', $member->id)
                        ->where('position', 'pastor')
                        ->where('is_active', true)
                        ->first();

                    if ($pastorLeader && $pastorLeader->member) {
                        $approverDisplayName = $pastorLeader->member->full_name;
                    } else {
                        // If not assigned as pastor but member exists, use member's name
                        $approverDisplayName = $member->full_name;
                    }
                } else {
                    // If no member found by email, check if user is a pastor and get active pastor
                    if ($approverUser->isPastor()) {
                        $activePastor = Leader::with('member')
                            ->where('position', 'pastor')
                            ->where('is_active', true)
                            ->first();

                        if ($activePastor && $activePastor->member) {
                            $approverDisplayName = $activePastor->member->full_name;
                        }
                    }
                }

                // Add the display name as an attribute
                $record->approver_display_name = $approverDisplayName;
            } else {
                $record->approver_display_name = 'System';
            }

            return $record;
        });

        // Get the current authenticated user
        $currentUser = auth()->user();

        // Get the approver name - try to get from Leader/Member relationship first
        $approverName = $currentUser->name; // Default to user's name

        // If user is a pastor, try to get the name from Leader/Member relationship
        if ($currentUser->isPastor()) {
            // Try to find the member by email match
            $member = Member::where('email', $currentUser->email)->first();

            if ($member) {
                // Check if this member is assigned as a pastor
                $pastorLeader = Leader::with('member')
                    ->where('member_id', $member->id)
                    ->where('position', 'pastor')
                    ->where('is_active', true)
                    ->first();

                if ($pastorLeader && $pastorLeader->member) {
                    $approverName = $pastorLeader->member->full_name;
                } else {
                    // If not assigned as pastor but member exists, use member's name
                    $approverName = $member->full_name;
                }
            } else {
                // If no member found by email, try to get the active pastor from leaders table
                $activePastor = Leader::with('member')
                    ->where('position', 'pastor')
                    ->where('is_active', true)
                    ->first();

                if ($activePastor && $activePastor->member) {
                    $approverName = $activePastor->member->full_name;
                }
            }
        } else {
            // For non-pastor users, try to find member by email
            $member = Member::where('email', $currentUser->email)->first();
            if ($member) {
                $approverName = $member->full_name;
            }
        }

        // Check if current user can approve (for view display)
        $canApprove = auth()->user()->canApproveFinances();

        return view('finance.approval.dashboard', compact(
            'pendingTithes',
            'pendingOfferings',
            'pendingDonations',
            'pendingExpenses',
            'pendingBudgets',
            'pendingPledgePayments',
            'pendingCommunityOfferings',
            'pendingFundingRequests',
            'totalPending',
            'totalPendingAmount',
            'recentApprovals',
            'today',
            'currentUser',
            'approverName',
            'canApprove'
        ));
    }

    /**
     * Approve a financial record
     */
    public function approve(Request $request)
    {
        $this->checkApprovalPermission();
        $request->validate([
            'type' => 'required|in:tithe,offering,donation,expense,budget,pledge,pledge_payment',
            'id' => 'required|integer',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        // Handle pledge payments separately
        if ($request->type === 'pledge_payment') {
            $record = PledgePayment::with('pledge.member')->findOrFail($request->id);

            $record->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes
            ]);

            // Update the pledge's amount_paid when payment is approved
            $pledge = $record->pledge;
            $newAmountPaid = $pledge->amount_paid + $record->amount;
            $pledge->update([
                'amount_paid' => $newAmountPaid,
                'status' => $newAmountPaid >= $pledge->pledge_amount ? 'completed' : 'active'
            ]);

            // Send notification to member
            if ($pledge->member) {
                $this->sendMemberApprovalNotification($record, 'pledge_payment');
            }

            return response()->json([
                'success' => true,
                'message' => 'Pledge payment approved successfully'
            ]);
        }

        $model = $this->getModel($request->type);

        // Only load member relationship for models that have it
        $query = $model->newQuery();
        if (in_array($request->type, ['tithe', 'offering', 'pledge', 'donation'])) {
            $query->with('member');
        }
        $record = $query->findOrFail($request->id);

        // For expenses, preserve existing fund breakdown in approval_notes if it exists
        $approvalNotes = $request->approval_notes;
        if ($request->type === 'expense' && $record->approval_notes) {
            // Check if existing approval_notes contains fund breakdown
            if (
                strpos($record->approval_notes, 'Fund allocation') !== false ||
                strpos($record->approval_notes, 'additional funding') !== false
            ) {
                // Preserve the original fund breakdown
                $approvalNotes = $record->approval_notes;
                // If user provided additional notes, append them
                if ($request->approval_notes && trim($request->approval_notes) !== '') {
                    $approvalNotes = $record->approval_notes . "\n\nPastor Notes: " . $request->approval_notes;
                }
            }
        }

        $record->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $approvalNotes
        ]);

        // Donations and offerings are kept separate - no automatic conversion
        // Reports will show both separately and combined totals when needed

        // Send notification to member if it's a member-related financial record
        if (in_array($request->type, ['tithe', 'offering', 'pledge', 'donation']) && $record->member) {
            $this->sendMemberApprovalNotification($record, $request->type);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type) . ' approved successfully'
        ]);
    }

    /**
     * Create an offering from an approved donation
     */
    private function createOfferingFromDonation(Donation $donation)
    {
        // Map donation types to offering types
        $offeringTypeMapping = [
            'building' => 'building_fund',
            'general' => 'general',
            'mission' => 'general', // Mission donations go to general offering
            'special' => 'special',
            'thanksgiving' => 'thanksgiving',
        ];

        // Get the offering type from mapping, or use the donation type if it's a custom type
        $donationType = strtolower($donation->donation_type);
        $offeringType = $offeringTypeMapping[$donationType] ?? $donation->donation_type;

        // If it's a custom donation type that doesn't match standard offering types,
        // check if it matches any existing offering type (case-insensitive)
        if (!isset($offeringTypeMapping[$donationType])) {
            $existingOfferingType = Offering::whereRaw('LOWER(offering_type) = ?', [strtolower($donation->donation_type)])
                ->where('approval_status', 'approved')
                ->value('offering_type');

            if ($existingOfferingType) {
                $offeringType = $existingOfferingType;
            } else {
                // Default to general if no match found
                $offeringType = 'general';
            }
        }

        // Check if an offering was already created from this donation
        // We'll check by looking for an offering with the same amount, date, and notes containing donation ID
        $existingOffering = Offering::where('amount', $donation->amount)
            ->where('offering_date', $donation->donation_date)
            ->where('offering_type', $offeringType)
            ->where(function ($query) use ($donation) {
                $query->where('notes', 'like', '%Donation ID: ' . $donation->id . '%')
                    ->orWhere('notes', 'like', '%From Donation #' . $donation->id . '%');
            })
            ->first();

        if ($existingOffering) {
            // Offering already exists, skip creation
            \Log::info('Offering already exists for donation', [
                'donation_id' => $donation->id,
                'offering_id' => $existingOffering->id
            ]);
            return;
        }

        // Create the offering
        $offering = Offering::create([
            'member_id' => $donation->member_id, // Can be null for non-member donations
            'amount' => $donation->amount,
            'offering_date' => $donation->donation_date,
            'offering_type' => $offeringType,
            'payment_method' => $donation->payment_method ?? 'cash',
            'reference_number' => $donation->reference_number,
            'notes' => ($donation->notes ? $donation->notes . "\n\n" : '') .
                'From Donation #' . $donation->id .
                ($donation->donor_name && !$donation->member_id ? ' (Donor: ' . $donation->donor_name . ')' : ''),
            'recorded_by' => $donation->recorded_by ?? auth()->user()->name ?? 'System',
            'is_verified' => false, // Treasurer will verify later
            'approval_status' => 'approved', // Auto-approved since donation was approved
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => 'Auto-created from approved donation #' . $donation->id
        ]);

        \Log::info('Created offering from donation', [
            'donation_id' => $donation->id,
            'offering_id' => $offering->id,
            'offering_type' => $offeringType,
            'amount' => $donation->amount
        ]);

        return $offering;
    }

    /**
     * Reject a financial record
     */
    public function reject(Request $request)
    {
        $this->checkApprovalPermission();
        $request->validate([
            'type' => 'required|in:tithe,offering,donation,expense,budget,pledge,pledge_payment',
            'id' => 'required|integer',
            'rejection_reason' => 'required|string|max:1000'
        ]);

        // Handle pledge payments separately
        if ($request->type === 'pledge_payment') {
            $record = PledgePayment::findOrFail($request->id);

            $record->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pledge payment rejected successfully'
            ]);
        }

        $model = $this->getModel($request->type);

        // Only load member relationship for models that have it
        $query = $model->newQuery();
        if (in_array($request->type, ['tithe', 'offering', 'pledge', 'donation'])) {
            $query->with('member');
        }
        $record = $query->findOrFail($request->id);

        $record->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type) . ' rejected successfully'
        ]);
    }

    /**
     * Bulk approve multiple records
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'records' => 'required|array',
            'records.*.type' => 'required|in:tithe,offering,donation,expense,budget,pledge,pledge_payment',
            'records.*.id' => 'required|integer',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        $approvedCount = 0;

        DB::transaction(function () use ($request, &$approvedCount) {
            foreach ($request->records as $recordData) {
                // Handle pledge payments separately
                if ($recordData['type'] === 'pledge_payment') {
                    $record = PledgePayment::with('pledge.member')->findOrFail($recordData['id']);

                    $record->update([
                        'approval_status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'approval_notes' => $request->approval_notes
                    ]);

                    // Update the pledge's amount_paid when payment is approved
                    $pledge = $record->pledge;
                    $newAmountPaid = $pledge->amount_paid + $record->amount;
                    $pledge->update([
                        'amount_paid' => $newAmountPaid,
                        'status' => $newAmountPaid >= $pledge->pledge_amount ? 'completed' : 'active'
                    ]);

                    // Send notification to member
                    if ($pledge->member) {
                        $this->sendMemberApprovalNotification($record, 'pledge_payment');
                    }

                    $approvedCount++;
                    continue;
                }

                $model = $this->getModel($recordData['type']);

                // Only load member relationship for models that have it
                $query = $model->newQuery();
                if (in_array($recordData['type'], ['tithe', 'offering', 'pledge', 'donation'])) {
                    $query->with('member');
                }
                $record = $query->findOrFail($recordData['id']);

                $record->update([
                    'approval_status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_notes' => $request->approval_notes
                ]);

                // Donations and offerings are kept separate - no automatic conversion
                // Reports will show both separately and combined totals when needed

                // Send notification to member if it's a member-related financial record
                if (in_array($recordData['type'], ['tithe', 'offering', 'pledge', 'donation']) && $record->member) {
                    $this->sendMemberApprovalNotification($record, $recordData['type']);
                }

                $approvedCount++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$approvedCount} records"
        ]);
    }

    /**
     * Get pending records by type
     */
    public function pendingByType($type)
    {
        $model = $this->getModel($type);

        // Only load member relationship for models that have it
        $query = $model->newQuery();
        if (in_array($type, ['tithe', 'offering', 'pledge', 'donation'])) {
            $query->with(['member', 'approver']);
        } else {
            $query->with('approver');
        }

        $records = $query->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('finance.approval.pending', compact('records', 'type'));
    }

    /**
     * Get approved records by type
     */
    public function approvedByType($type)
    {
        $model = $this->getModel($type);

        // Only load member relationship for models that have it
        $query = $model->newQuery();
        if (in_array($type, ['tithe', 'offering', 'pledge', 'donation'])) {
            $query->with(['member', 'approver']);
        } else {
            $query->with('approver');
        }

        $records = $query->where('approval_status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return view('finance.approval.approved', compact('records', 'type'));
    }

    /**
     * Get rejected records by type
     */
    public function rejectedByType($type)
    {
        $model = $this->getModel($type);

        // Only load member relationship for models that have it
        $query = $model->newQuery();
        if (in_array($type, ['tithe', 'offering', 'pledge', 'donation'])) {
            $query->with(['member', 'approver']);
        } else {
            $query->with('approver');
        }

        $records = $query->where('approval_status', 'rejected')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return view('finance.approval.rejected', compact('records', 'type'));
    }

    /**
     * View details for a specific record
     */
    public function viewDetails($type, $id)
    {
        $this->checkApprovalPermission();

        // Handle pledge payments separately
        if ($type === 'pledge_payment') {
            $record = PledgePayment::with(['pledge.member', 'approver'])->findOrFail($id);
            $recordDate = $record->payment_date ? \Carbon\Carbon::parse($record->payment_date) : null;
        } else {
            $model = $this->getModel($type);

            // Only load member relationship for models that have it
            $query = $model->newQuery();
            if (in_array($type, ['tithe', 'offering', 'pledge', 'donation'])) {
                $query->with(['member', 'approver']);
            } else {
                $query->with('approver');
            }
            $record = $query->findOrFail($id);

            // Get the appropriate date field based on record type
            if ($type === 'budget') {
                $recordDate = $record->start_date ? \Carbon\Carbon::parse($record->start_date) : ($record->created_at ? \Carbon\Carbon::parse($record->created_at) : null);
            } else {
                $recordDate = $record->offering_date ?? $record->tithe_date ?? $record->donation_date ?? $record->expense_date ?? $record->pledge_date ?? $record->created_at;
            }
        }

        // Format the data for display
        $data = [
            'type' => ucfirst($type === 'pledge_payment' ? 'Pledge Payment' : $type),
            'amount' => ($type === 'budget' ? ($record->total_budget ?? 0) : ($record->amount ?? 0)),
            'date' => $recordDate ? (is_string($recordDate) ? $recordDate : (is_object($recordDate) && method_exists($recordDate, 'format') ? $recordDate->format('M d, Y') : $recordDate)) : null,
            'member_name' => ($type === 'pledge_payment' ? ($record->pledge->member->full_name ?? null) : ($record->member->full_name ?? null)),
            'donor_name' => $record->donor_name ?? null,
            'recorded_by' => ($type === 'budget' ? ($this->getRecordedByForBudget($record) ?? 'System') : ($record->recorded_by ?? 'System')),
            'created_at' => $record->created_at ? $record->created_at->format('M d, Y H:i') : null,
            'notes' => ($type === 'budget' ? ($record->description ?? null) : ($record->notes ?? null)),
            'approval_status' => $record->approval_status ?? 'pending',
            'approved_by' => $record->approver->name ?? null,
            'approved_at' => $record->approved_at ? $record->approved_at->format('M d, Y H:i') : null,
        ];

        // Add type-specific fields
        if ($type === 'offering') {
            $data['offering_type'] = $record->offering_type ?? null;
            $data['service_type'] = $record->service_type ?? null;
            $data['payment_method'] = $record->payment_method ?? null;
            $data['reference_number'] = $record->reference_number ?? null;
        } elseif ($type === 'tithe') {
            $data['payment_method'] = $record->payment_method ?? null;
            $data['reference_number'] = $record->reference_number ?? null;
        } elseif ($type === 'donation') {
            $data['donation_type'] = $record->donation_type ?? null;
            $data['payment_method'] = $record->payment_method ?? null;
            $data['reference_number'] = $record->reference_number ?? null;
            $data['purpose'] = $record->purpose ?? null;
        } elseif ($type === 'expense') {
            $data['description'] = $record->description ?? null;
            $data['vendor'] = $record->vendor ?? null;
            $data['payment_method'] = $record->payment_method ?? null;
            $data['reference_number'] = $record->reference_number ?? null;
            $data['receipt_number'] = $record->receipt_number ?? null;
            $data['expense_category'] = $record->expense_category ?? null;
            $data['budget_id'] = $record->budget_id ?? null;
            $data['budget_name'] = $record->budget->budget_name ?? null;

            // Parse additional funding from approval_notes
            $data['additional_funding'] = null;
            if ($record->approval_notes) {
                // Check if approval_notes contains additional funding information
                if (
                    strpos($record->approval_notes, 'additional funding') !== false ||
                    strpos($record->approval_notes, 'Fund allocation with additional funding') !== false
                ) {
                    // Try to extract JSON from approval_notes
                    $notes = $record->approval_notes;

                    // Try to find JSON array pattern - match from "Fund allocation" or ":" to the end
                    if (
                        preg_match('/Fund allocation[^:]*:\s*(\[.*\])/s', $notes, $matches) ||
                        preg_match('/:\s*(\[.*\])/s', $notes, $matches)
                    ) {
                        try {
                            $fundBreakdown = json_decode($matches[1], true);
                            if (is_array($fundBreakdown) && !empty($fundBreakdown)) {
                                $data['additional_funding'] = $fundBreakdown;
                            }
                        } catch (\Exception $e) {
                            // If JSON parsing fails, log for debugging
                            \Log::warning('Failed to parse additional funding from approval_notes', [
                                'expense_id' => $record->id,
                                'notes' => $notes,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        } elseif ($type === 'budget') {
            $data['budget_name'] = $record->budget_name ?? null;
            $data['budget_type'] = $record->budget_type ?? null;
            $data['category'] = $record->category ?? null;
            $data['description'] = $record->description ?? null;
            $data['purpose'] = $record->purpose ?? null;
            $data['start_date'] = $record->start_date ? $record->start_date->format('M d, Y') : null;
            $data['end_date'] = $record->end_date ? $record->end_date->format('M d, Y') : null;
            $data['fiscal_year'] = $record->fiscal_year ?? null;
            $data['total_budget'] = $record->total_budget ?? 0;
            $data['allocated_amount'] = $record->allocated_amount ?? 0;
            $data['spent_amount'] = $record->spent_amount ?? 0;
        } elseif ($type === 'pledge') {
            $data['pledge_type'] = $record->pledge_type ?? null;
            $data['pledge_amount'] = $record->pledge_amount ?? null;
            $data['amount_paid'] = $record->amount_paid ?? 0;
            $data['due_date'] = $record->due_date ?? null;
            $data['payment_frequency'] = $record->payment_frequency ?? null;
            $data['purpose'] = $record->purpose ?? null;
        } elseif ($type === 'pledge_payment') {
            $data['pledge_type'] = $record->pledge->pledge_type ?? null;
            $data['pledge_amount'] = $record->pledge->pledge_amount ?? null;
            $data['payment_method'] = $record->payment_method ?? null;
            $data['reference_number'] = $record->reference_number ?? null;
            $data['purpose'] = $record->pledge->purpose ?? null;
        }

        return response()->json($data);
    }

    /**
     * Get recorded by information for budget
     */
    private function getRecordedByForBudget($budget)
    {
        if ($budget->created_by) {
            // Try to get user name from created_by
            $user = \App\Models\User::find($budget->created_by);
            if ($user) {
                // Try to get member name from user email
                $member = \App\Models\Member::where('email', $user->email)->first();
                if ($member) {
                    return $member->full_name;
                }
                return $user->name;
            }
        }
        return 'System';
    }

    /**
     * Get financial summary for a specific date
     */
    public function dailySummary(Request $request)
    {
        $date = $request->get('date', Carbon::today());
        $date = Carbon::parse($date);

        $summary = [
            'date' => $date->format('Y-m-d'),
            'tithes' => [
                'total' => Tithe::whereDate('tithe_date', $date)->where('approval_status', 'approved')->sum('amount'),
                'count' => Tithe::whereDate('tithe_date', $date)->where('approval_status', 'approved')->count(),
                'pending' => Tithe::whereDate('tithe_date', $date)->where('approval_status', 'pending')->count()
            ],
            'offerings' => [
                'total' => Offering::whereDate('offering_date', $date)->where('approval_status', 'approved')->sum('amount'),
                'count' => Offering::whereDate('offering_date', $date)->where('approval_status', 'approved')->count(),
                'pending' => Offering::whereDate('offering_date', $date)->where('approval_status', 'pending')->count()
            ],
            'donations' => [
                'total' => Donation::whereDate('donation_date', $date)->where('approval_status', 'approved')->sum('amount'),
                'count' => Donation::whereDate('donation_date', $date)->where('approval_status', 'approved')->count(),
                'pending' => Donation::whereDate('donation_date', $date)->where('approval_status', 'pending')->count()
            ],
            'expenses' => [
                'total' => Expense::whereDate('expense_date', $date)->where('approval_status', 'approved')->sum('amount'),
                'count' => Expense::whereDate('expense_date', $date)->where('approval_status', 'approved')->count(),
                'pending' => Expense::whereDate('expense_date', $date)->where('approval_status', 'pending')->count()
            ]
        ];

        $summary['total_income'] = $summary['tithes']['total'] + $summary['offerings']['total'] + $summary['donations']['total'];
        $summary['net_income'] = $summary['total_income'] - $summary['expenses']['total'];
        $summary['total_pending'] = $summary['tithes']['pending'] + $summary['offerings']['pending'] +
            $summary['donations']['pending'] + $summary['expenses']['pending'];

        return response()->json($summary);
    }

    /**
     * Export pending records
     */
    public function exportPending()
    {
        $this->checkApprovalPermission();

        // Simple CSV export for now
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pending_records.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Type', 'Amount', 'Date', 'Member', 'Status']);

            // Add pending records
            $today = Carbon::today();

            $pendingTithes = Tithe::with('member')
                ->where('approval_status', 'pending')
                ->whereDate('tithe_date', $today)
                ->get();

            foreach ($pendingTithes as $record) {
                fputcsv($file, [
                    'Tithe',
                    $record->amount,
                    $record->tithe_date->format('Y-m-d'),
                    $record->member ? $record->member->full_name : 'N/A',
                    'Pending'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send approval notification to member
     */
    private function sendMemberApprovalNotification($record, $type)
    {
        try {
            $member = $record->member;

            if (!$member) {
                \Log::warning("No member found for {$type} record ID: {$record->id}");
                return;
            }

            // Prepare notification data
            $paymentType = $this->getPaymentTypeName($type);

            // For offerings, use specific offering type if available
            if ($type === 'offering' && isset($record->offering_type)) {
                $paymentType = $this->getOfferingTypeName($record->offering_type);
            }

            $notificationData = [
                'payment_type' => $paymentType,
                'amount' => $this->getAmount($record, $type),
                'payment_date' => $this->getPaymentDate($record, $type),
            ];

            // Send notification to member
            $notification = new \App\Notifications\PaymentApprovalNotification($notificationData);
            $member->notify($notification);

            // Send SMS notification directly
            $notification->sendSmsNotification($member);

            \Log::info("Payment approval notification sent to member", [
                'member_id' => $member->id,
                'member_name' => $member->full_name,
                'type' => $type,
                'record_id' => $record->id,
                'amount' => $notificationData['amount']
            ]);

        } catch (\Exception $e) {
            \Log::error("Failed to send payment approval notification to member: " . $e->getMessage(), [
                'member_id' => $member->id ?? 'unknown',
                'type' => $type,
                'record_id' => $record->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Get payment type name for display
     */
    private function getPaymentTypeName($type)
    {
        switch ($type) {
            case 'tithe':
                return 'Zaka';
            case 'offering':
                return 'Sadaka';
            case 'pledge':
                return 'Ahadi';
            case 'pledge_payment':
                return 'Malipo ya Ahadi';
            case 'donation':
                return 'Michango';
            default:
                return ucfirst($type);
        }
    }

    /**
     * Get specific offering type name in Swahili
     */
    private function getOfferingTypeName($offeringType)
    {
        switch ($offeringType) {
            case 'general':
                return 'Sadaka ya Kawaida';
            case 'special':
                return 'Sadaka ya Maalum';
            case 'thanksgiving':
                return 'Sadaka ya Shukrani';
            case 'building_fund':
                return 'Sadaka ya Ujenzi';
            case 'other':
                return 'Sadaka Nyingine';
            default:
                return 'Sadaka';
        }
    }

    /**
     * Get amount from record based on type
     */
    private function getAmount($record, $type)
    {
        switch ($type) {
            case 'tithe':
                return $record->amount;
            case 'offering':
                return $record->amount;
            case 'pledge':
                return $record->pledge_amount;
            case 'pledge_payment':
                return $record->amount;
            case 'donation':
                return $record->amount;
            default:
                return $record->amount ?? 0;
        }
    }

    /**
     * Get payment date from record based on type
     */
    private function getPaymentDate($record, $type)
    {
        switch ($type) {
            case 'tithe':
                return $record->tithe_date;
            case 'offering':
                return $record->offering_date;
            case 'pledge':
                return $record->pledge_date;
            case 'pledge_payment':
                return $record->payment_date;
            case 'donation':
                return $record->donation_date;
            default:
                return $record->created_at;
        }
    }

    /**
     * Get the appropriate model based on type
     */
    private function getModel($type)
    {
        switch ($type) {
            case 'tithe':
                return new Tithe();
            case 'offering':
                return new Offering();
            case 'donation':
                return new Donation();
            case 'expense':
                return new Expense();
            case 'budget':
                return new Budget();
            case 'pledge':
                return new Pledge();
            default:
                throw new \InvalidArgumentException('Invalid record type');
        }
    }

    /**
     * Display funding requests dashboard
     */
    public function fundingRequests()
    {
        $this->checkApprovalPermission();

        $pendingRequests = FundingRequest::with(['expense', 'budget'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $approvedRequests = FundingRequest::with(['expense', 'budget', 'approver'])
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        $rejectedRequests = FundingRequest::with(['expense', 'budget', 'approver'])
            ->where('status', 'rejected')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return view('finance.approval.funding-requests', compact(
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests'
        ));
    }

    /**
     * Approve a funding request
     */
    public function approveFundingRequest(Request $request, FundingRequest $fundingRequest)
    {
        $this->checkApprovalPermission();

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
            'allocations' => 'required|array',
            'allocations.*.offering_type' => 'required|string',
            'allocations.*.amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Update funding request
            $fundingRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes
            ]);

            // Allocate additional funds to budget
            $allocations = [];
            foreach ($request->allocations as $allocation) {
                if ($allocation['amount'] > 0) {
                    $allocations[$allocation['offering_type']] = $allocation['amount'];
                }
            }

            if (!empty($allocations)) {
                $budgetFundingService = app(\App\Services\BudgetFundingService::class);
                $budgetFundingService->allocateFundsToBudget($fundingRequest->budget, $allocations);
            }

            // Now approve the expense
            $fundingRequest->expense->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => 'Approved with additional funding allocation'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funding request approved and additional funds allocated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve funding request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a funding request
     */
    public function rejectFundingRequest(Request $request, FundingRequest $fundingRequest)
    {
        $this->checkApprovalPermission();

        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Update funding request
            $fundingRequest->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

            // Reject the expense as well
            $fundingRequest->expense->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => 'Rejected due to insufficient funding: ' . $request->rejection_reason
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funding request rejected successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject funding request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get funding request details
     */
    public function getFundingRequestDetails(FundingRequest $fundingRequest)
    {
        $this->checkApprovalPermission();

        $fundingRequest->load(['expense', 'budget', 'approver']);

        return response()->json([
            'success' => true,
            'funding_request' => $fundingRequest
        ]);
    }
}