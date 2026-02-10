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
use App\Models\EvangelismTask;
use App\Models\EvangelismIssue;
use App\Models\EvangelismReport;
use App\Models\ChurchElderTask;
use App\Models\ChurchElderIssue;
use App\Models\ParishWorkerActivity;
use App\Models\ParishWorkerReport;
use App\Models\CandleAction;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PastorDashboardController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level
    }

    private function checkPastorPermission()
    {
        if (!auth()->check()) {
            abort(401, 'Please log in to access this page.');
        }

        $user = auth()->user();
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Only Pastors can access this dashboard.');
        }
    }

    /**
     * Display the Pastor dashboard
     */
    public function index()
    {
        $this->checkPastorPermission();

        $today = Carbon::today();
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->year;

        // Get pending records for today
        $pendingTithes = Tithe::with(['member', 'approver'])
            ->where('approval_status', 'pending')
            ->whereDate('tithe_date', $today)
            ->count();

        $pendingOfferings = Offering::with(['member', 'approver'])
            ->where('approval_status', 'pending')
            ->whereDate('offering_date', $today)
            ->count();

        $pendingDonations = Donation::with(['member', 'approver'])
            ->where('approval_status', 'pending')
            ->whereDate('donation_date', $today)
            ->count();

        $pendingExpenses = Expense::with(['budget', 'approver'])
            ->where('approval_status', 'pending')
            ->whereDate('expense_date', $today)
            ->count();

        $pendingBudgets = Budget::with(['approver'])
            ->where('approval_status', 'pending')
            ->whereDate('created_at', $today)
            ->count();

        $pendingPledges = Pledge::with(['member', 'approver'])
            ->where('approval_status', 'pending')
            ->whereDate('pledge_date', $today)
            ->count();

        // Get pending pledge payments (actual payments that need approval)
        $pendingPledgePayments = PledgePayment::with(['pledge.member', 'approver'])
            ->where('approval_status', 'pending')
            ->whereDate('payment_date', $today)
            ->count();

        // Get pending child to member transitions
        $pendingTransitions = \App\Models\ChildToMemberTransition::where('status', 'pending')->count();

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

        $monthlyExpenses = Expense::whereMonth('expense_date', $currentMonth->month)
            ->whereYear('expense_date', $currentYear)
            ->where('status', 'paid')
            ->where('approval_status', 'approved')
            ->sum('amount');

        $monthlyPledges = Pledge::whereMonth('pledge_date', $currentMonth->month)
            ->whereYear('pledge_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('pledge_amount');

        $totalIncome = $monthlyTithes + $monthlyOfferings + $monthlyDonations + $monthlyPledges;
        $netIncome = $totalIncome - $monthlyExpenses;

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
            Pledge::with(['member', 'approver'])
                ->where('approval_status', 'approved')
                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->map(function ($item) {
                    $item->type = 'Pledge';
                    $item->date = $item->pledge_date;
                    return $item;
                })
        );

        // Add pledge payments to recent approvals
        $recentApprovals = $recentApprovals->merge(
            PledgePayment::with(['pledge.member', 'approver'])
                ->where('approval_status', 'approved')
                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->map(function ($item) {
                    $item->type = 'Pledge Payment';
                    $item->date = $item->payment_date;
                    // $item->amount is already set correctly on the model
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

        // Get total members count (including children who are church members)
        $adultMembers = Member::count();
        $childMembers = \App\Models\Child::where('is_church_member', true)->count();
        $totalMembers = $adultMembers + $childMembers;

        // Get pastor information from leaders table
        // Filter out leaders without valid member relationships
        $pastor = Leader::with('member')
            ->where('position', 'pastor')
            ->where('is_active', true)
            ->get()
            ->filter(function ($leader) {
                return $leader->member !== null;
            })
            ->first();

        // Get pending amount (including pledge payments)
        $pendingAmount = Tithe::where('approval_status', 'pending')
            ->whereDate('tithe_date', $today)
            ->sum('amount') +
            Offering::where('approval_status', 'pending')
                ->whereDate('offering_date', $today)
                ->sum('amount') +
            Donation::where('approval_status', 'pending')
                ->whereDate('donation_date', $today)
                ->sum('amount') +
            Pledge::where('approval_status', 'pending')
                ->whereDate('pledge_date', $today)
                ->sum('pledge_amount') +
            PledgePayment::where('approval_status', 'pending')
                ->whereDate('payment_date', $today)
                ->sum('amount');

        // Get tasks from Evangelism Leaders and Church Elders
        $evangelismTasks = EvangelismTask::with(['evangelismLeader', 'campus', 'community', 'member'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $churchElderTasks = ChurchElderTask::with(['churchElder', 'community', 'member'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get issues from Evangelism Leaders and Church Elders
        $evangelismIssues = EvangelismIssue::with(['evangelismLeader', 'campus', 'community'])
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $churchElderIssues = ChurchElderIssue::with(['churchElder', 'community'])
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get reports from Evangelism Leaders
        $evangelismReports = EvangelismReport::with(['evangelismLeader', 'campus', 'community'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get reports from Parish Workers
        $parishWorkerReports = ParishWorkerReport::with(['user', 'campus'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get activities from Parish Workers
        $parishWorkerActivities = ParishWorkerActivity::with(['user', 'campus'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Count totals
        $totalEvangelismTasks = EvangelismTask::count();
        $totalChurchElderTasks = ChurchElderTask::count();
        $totalEvangelismIssues = EvangelismIssue::whereIn('status', ['open', 'in_progress'])->count();
        $totalChurchElderIssues = ChurchElderIssue::whereIn('status', ['open', 'in_progress'])->count();
        $totalEvangelismReports = EvangelismReport::count();
        $totalParishWorkerReports = ParishWorkerReport::count();
        $totalParishWorkerActivities = ParishWorkerActivity::count();

        // Get recent candle actions
        $parishWorkerCandleActions = CandleAction::with(['user', 'campus'])
            ->orderBy('action_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalCandlePurchased = CandleAction::where('action_type', 'purchase')->sum('quantity');
        $totalCandleDistributed = CandleAction::where('action_type', 'distribution')->sum('quantity');
        $candleStockOnHand = $totalCandlePurchased - $totalCandleDistributed;

        // Get unread bereavement notifications
        $user = auth()->user();
        $bereavementNotifications = $user->unreadNotifications()
            ->where('type', 'App\Notifications\BereavementNotification')
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug logging
        \Log::info('Pastor dashboard - Fetching notifications', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'notifications_count' => $bereavementNotifications->count(),
            'all_unread_count' => $user->unreadNotifications()->count(),
            'notification_types' => $user->unreadNotifications()->pluck('type')->unique()->toArray()
        ]);

        return view('pastor.dashboard', compact(
            'pendingTithes',
            'pendingOfferings',
            'pendingDonations',
            'pendingExpenses',
            'pendingBudgets',
            'pendingPledges',
            'pendingPledgePayments',
            'pendingTransitions',
            'pendingAmount',
            'monthlyTithes',
            'monthlyOfferings',
            'monthlyDonations',
            'monthlyPledges',
            'monthlyExpenses',
            'totalIncome',
            'netIncome',
            'recentApprovals',
            'totalMembers',
            'pastor',
            'today',
            'evangelismTasks',
            'churchElderTasks',
            'evangelismIssues',
            'churchElderIssues',
            'evangelismReports',
            'totalEvangelismTasks',
            'totalChurchElderTasks',
            'totalEvangelismIssues',
            'totalChurchElderIssues',
            'totalEvangelismReports',
            'totalParishWorkerReports',
            'totalParishWorkerActivities',
            'parishWorkerReports',
            'parishWorkerActivities',
            'parishWorkerCandleActions',
            'totalCandlePurchased',
            'totalCandleDistributed',
            'candleStockOnHand',
            'bereavementNotifications'
        ));
    }

    /**
     * Get new bereavement notifications (for real-time polling)
     */
    public function getBereavementNotifications()
    {
        $this->checkPastorPermission();

        $user = auth()->user();
        $lastCheck = request()->input('last_check', now()->subMinutes(5)->toDateTimeString());

        // Get unread bereavement notifications created after last check
        $newNotifications = $user->unreadNotifications()
            ->where('type', 'App\Notifications\BereavementNotification')
            ->where('created_at', '>', $lastCheck)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                return [
                    'id' => $notification->id,
                    'deceased_name' => $data['deceased_name'] ?? 'Bereavement Event',
                    'incident_date' => $data['incident_date'] ?? null,
                    'campus_name' => $data['campus_name'] ?? 'Unknown Campus',
                    'message' => $data['message'] ?? 'A new bereavement event has been created.',
                    'bereavement_event_id' => $data['bereavement_event_id'] ?? null,
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $newNotifications,
            'count' => $newNotifications->count(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Display all tasks from Evangelism Leaders and Church Elders
     */
    public function allTasks()
    {
        $this->checkPastorPermission();

        $evangelismTasks = EvangelismTask::with(['evangelismLeader', 'campus', 'community', 'member'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'evangelism_page');

        $churchElderTasks = ChurchElderTask::with(['churchElder', 'community', 'member'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'elder_page');

        return view('pastor.tasks.index', compact('evangelismTasks', 'churchElderTasks'));
    }

    /**
     * Display all issues from Evangelism Leaders and Church Elders
     */
    public function allIssues()
    {
        $this->checkPastorPermission();

        $evangelismIssues = EvangelismIssue::with(['evangelismLeader', 'campus', 'community'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'evangelism_page');

        $churchElderIssues = ChurchElderIssue::with(['churchElder', 'community'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'elder_page');

        return view('pastor.issues.index', compact('evangelismIssues', 'churchElderIssues'));
    }

    /**
     * Display all reports from Evangelism Leaders
     */
    public function allReports()
    {
        $this->checkPastorPermission();

        $evangelismReports = EvangelismReport::with(['evangelismLeader', 'campus', 'community'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'evangelism_page');

        $parishWorkerReports = ParishWorkerReport::with(['user', 'campus'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'parish_worker_page');

        return view('pastor.reports.index', compact('evangelismReports', 'parishWorkerReports'));
    }

    /**
     * Show Evangelism Leader Task
     */
    public function showEvangelismTask(EvangelismTask $task)
    {
        $this->checkPastorPermission();

        $task->load(['evangelismLeader', 'campus', 'community', 'member', 'pastorCommenter']);

        return view('pastor.tasks.show-evangelism', compact('task'));
    }

    /**
     * Show Church Elder Task
     */
    public function showChurchElderTask(ChurchElderTask $task)
    {
        $this->checkPastorPermission();

        $task->load(['churchElder', 'community', 'member', 'pastorCommenter']);

        return view('pastor.tasks.show-elder', compact('task'));
    }

    /**
     * Comment on Evangelism Leader Task
     */
    public function commentEvangelismTask(Request $request, EvangelismTask $task)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'pastor_comments' => 'required|string|min:10|max:2000',
        ], [
            'pastor_comments.required' => 'Please provide your comments or suggestions.',
            'pastor_comments.min' => 'Comments must be at least 10 characters.',
        ]);

        $task->update([
            'pastor_comments' => $validated['pastor_comments'],
            'pastor_commented_by' => auth()->id(),
            'pastor_commented_at' => now(),
        ]);

        return back()->with('success', 'Your comments have been added successfully.');
    }

    /**
     * Comment on Church Elder Task
     */
    public function commentChurchElderTask(Request $request, ChurchElderTask $task)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'pastor_comments' => 'required|string|min:10|max:2000',
        ], [
            'pastor_comments.required' => 'Please provide your comments or suggestions.',
            'pastor_comments.min' => 'Comments must be at least 10 characters.',
        ]);

        $task->update([
            'pastor_comments' => $validated['pastor_comments'],
            'pastor_commented_by' => auth()->id(),
            'pastor_commented_at' => now(),
        ]);

        return back()->with('success', 'Your comments have been added successfully.');
    }

    /**
     * Show Evangelism Leader Issue
     */
    public function showEvangelismIssue(EvangelismIssue $issue)
    {
        $this->checkPastorPermission();

        $issue->load(['evangelismLeader', 'campus', 'community', 'pastorCommenter']);

        return view('pastor.issues.show-evangelism', compact('issue'));
    }

    /**
     * Show Church Elder Issue
     */
    public function showChurchElderIssue(ChurchElderIssue $issue)
    {
        $this->checkPastorPermission();

        $issue->load(['churchElder', 'community', 'pastorCommenter']);

        return view('pastor.issues.show-elder', compact('issue'));
    }

    /**
     * Comment on Evangelism Leader Issue
     */
    public function commentEvangelismIssue(Request $request, EvangelismIssue $issue)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'pastor_comments' => 'required|string|min:10|max:2000',
        ], [
            'pastor_comments.required' => 'Please provide your comments or suggestions.',
            'pastor_comments.min' => 'Comments must be at least 10 characters.',
        ]);

        $issue->update([
            'pastor_comments' => $validated['pastor_comments'],
            'pastor_commented_by' => auth()->id(),
            'pastor_commented_at' => now(),
        ]);

        return back()->with('success', 'Your comments have been added successfully.');
    }

    /**
     * Comment on Church Elder Issue
     */
    public function commentChurchElderIssue(Request $request, ChurchElderIssue $issue)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'pastor_comments' => 'required|string|min:10|max:2000',
        ], [
            'pastor_comments.required' => 'Please provide your comments or suggestions.',
            'pastor_comments.min' => 'Comments must be at least 10 characters.',
        ]);

        $issue->update([
            'pastor_comments' => $validated['pastor_comments'],
            'pastor_commented_by' => auth()->id(),
            'pastor_commented_at' => now(),
        ]);

        return back()->with('success', 'Your comments have been added successfully.');
    }

    /**
     * Update Evangelism Issue Status
     */
    public function updateEvangelismIssueStatus(Request $request, EvangelismIssue $issue)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        // If status is resolved, set resolved_by and resolved_at
        if ($validated['status'] === 'resolved') {
            $updateData['resolved_by'] = auth()->id();
            $updateData['resolved_at'] = now();
            if (!empty($validated['resolution_notes'])) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }
        } elseif ($validated['status'] === 'closed') {
            // If closing, also set resolved info if not already set
            if (!$issue->resolved_by) {
                $updateData['resolved_by'] = auth()->id();
                $updateData['resolved_at'] = now();
            }
            if (!empty($validated['resolution_notes'])) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }
        }

        $issue->update($updateData);

        return back()->with('success', 'Issue status updated successfully.');
    }

    /**
     * Update Church Elder Issue Status
     */
    public function updateChurchElderIssueStatus(Request $request, ChurchElderIssue $issue)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        // If status is resolved, set resolved_by and resolved_at
        if ($validated['status'] === 'resolved') {
            $updateData['resolved_by'] = auth()->id();
            $updateData['resolved_at'] = now();
            if (!empty($validated['resolution_notes'])) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }
        } elseif ($validated['status'] === 'closed') {
            // If closing, also set resolved info if not already set
            if (!$issue->resolved_by) {
                $updateData['resolved_by'] = auth()->id();
                $updateData['resolved_at'] = now();
            }
            if (!empty($validated['resolution_notes'])) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }
        }

        $issue->update($updateData);

        return back()->with('success', 'Issue status updated successfully.');
    }

    /**
     * Display all Parish Worker activities with real-time filtering
     */
    public function allParishWorkerActivities(Request $request)
    {
        $this->checkPastorPermission();

        $query = ParishWorkerActivity::with(['user', 'campus']);

        // Filter by Campus (Street/Mtaa)
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        // Filter by Activity Type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        $activities = $query->orderBy('activity_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $campuses = Campus::where('is_active', true)->get();

        return view('pastor.parish-worker.activities', compact('activities', 'campuses'));
    }

    /**
     * Display all Parish Worker performance reports
     */
    public function allParishWorkerReports(Request $request)
    {
        $this->checkPastorPermission();

        $query = ParishWorkerReport::with(['user', 'campus']);

        // Filter by Campus
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        // Filter by Parish Worker
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $reports = $query->orderBy('submitted_at', 'desc')
            ->paginate(20);

        $campuses = Campus::where('is_active', true)->get();

        // Get all Parish Workers
        $parishWorkers = User::where('role', 'parish_worker')->get();

        return view('pastor.parish-worker.reports', compact('reports', 'campuses', 'parishWorkers'));
    }

    /**
     * Display all Candle Inventory actions
     */
    public function allCandleActions(Request $request)
    {
        $this->checkPastorPermission();

        $query = CandleAction::with(['user', 'campus']);

        // Filter by Campus
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        // Filter by Action Type
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('action_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('action_date', '<=', $request->date_to);
        }

        $actions = $query->orderBy('action_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $campuses = Campus::where('is_active', true)->get();

        // Calculate stats
        $onHand = CandleAction::where('action_type', 'purchase')->sum('quantity') -
            CandleAction::where('action_type', 'distribution')->sum('quantity');
        $purchased = CandleAction::where('action_type', 'purchase')->sum('quantity');
        $distributed = CandleAction::where('action_type', 'distribution')->sum('quantity');

        return view('pastor.parish-worker.candles', compact('actions', 'campuses', 'onHand', 'purchased', 'distributed'));
    }

    /**
     * Show Parish Worker Report
     */
    public function showParishWorkerReport(ParishWorkerReport $report)
    {
        $this->checkPastorPermission();

        $report->load(['user', 'campus', 'reviewer']);

        return view('pastor.parish-worker.show-report', compact('report'));
    }

    /**
     * Comment on Parish Worker Report
     */
    public function commentParishWorkerReport(Request $request, ParishWorkerReport $report)
    {
        $this->checkPastorPermission();

        $validated = $request->validate([
            'pastor_comments' => 'required|string|min:10|max:2000',
        ]);

        $report->update([
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'status' => 'reviewed',
        ]);

        return back()->with('success', 'Your comments have been added and report marked as reviewed.');
    }
}