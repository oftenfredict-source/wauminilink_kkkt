<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Offering;
use App\Models\Member;
use App\Models\ServiceAttendance;
use App\Models\SundayService;
use App\Models\ChurchElderTask;
use App\Models\ChurchElderIssue;
use App\Models\CommunityOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChurchElderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard for church elder
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Check if user is a church elder
        if (!$user->isChurchElder()) {
            abort(403, 'You are not authorized to access this page.');
        }

        // Get communities where user is elder
        $communities = $user->elderCommunities();
        
        if ($communities->isEmpty()) {
            return redirect()->route('home')
                ->with('error', 'You are not assigned to any community as a church elder.');
        }

        // For now, show the first community (can be enhanced to show all or let user select)
        $community = $communities->first();
        $community->load(['campus', 'members', 'churchElder.member']);

        // Get community statistics
        $stats = $this->getCommunityStats($community);
        
        // Load user's member information for personal info display
        $user->load('member');
        
        // Get recent tasks for this community and elder
        $recentTasks = ChurchElderTask::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->with(['member', 'community'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get recent issues for this community and elder
        $recentIssues = ChurchElderIssue::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->with(['community'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get pending tasks count
        $pendingTasks = ChurchElderTask::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        // Get open issues count
        $openIssues = ChurchElderIssue::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        return view('church-elder.dashboard', compact('community', 'communities', 'stats', 'user', 'recentTasks', 'recentIssues', 'pendingTasks', 'openIssues'));
    }

    /**
     * Show community information
     */
    public function showCommunity(Community $community)
    {
        $user = auth()->user();
        
        // Verify user is elder of this community
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view this community.');
        }

        $community->load(['campus', 'members', 'churchElder.member']);
        $stats = $this->getCommunityStats($community);

        return view('church-elder.community', compact('community', 'stats'));
    }

    /**
     * Show service reporting page
     */
    public function services(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view this community.');
        }

        // Get services created by this church elder (via church_elder_id which is member_id)
        $services = SundayService::where('church_elder_id', $user->member_id)
            ->with(['coordinator', 'churchElder', 'attendances.member'])
            ->orderBy('service_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get community offerings linked to services for this community
        $serviceOfferings = CommunityOffering::where('community_id', $community->id)
            ->whereNotNull('service_id')
            ->with('service')
            ->get()
            ->keyBy('service_id');

        // Get recent attendance records grouped by date
        $recentAttendances = ServiceAttendance::whereHas('member', function($query) use ($community) {
            $query->where('community_id', $community->id);
        })
        ->with('member')
        ->orderBy('attended_at', 'desc')
        ->limit(50)
        ->get()
        ->groupBy(function($attendance) {
            return \Carbon\Carbon::parse($attendance->attended_at)->format('Y-m-d');
        });

        return view('church-elder.services', compact('community', 'services', 'recentAttendances', 'serviceOfferings'));
    }

    /**
     * Show offering recording page
     */
    public function offerings(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view this community.');
        }

        // Get community members for dropdown
        $members = $community->members()->orderBy('full_name')->get();

        // Get recent offerings for this community
        $recentOfferings = Offering::whereHas('member', function($query) use ($community) {
            $query->where('community_id', $community->id);
        })
        ->with('member')
        ->orderBy('offering_date', 'desc')
        ->limit(50)
        ->get();

        // Get offering statistics
        $offeringStats = $this->getOfferingStats($community);

        return view('church-elder.offerings', compact('community', 'members', 'recentOfferings', 'offeringStats'));
    }

    /**
     * Store a new offering
     */
    public function storeOffering(Request $request, Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to record offerings for this community.'
            ], 403);
        }

        $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'offering_date' => 'required|date',
            'offering_type' => 'required|string|in:general,special,thanksgiving,building_fund,other',
            'payment_method' => 'required|string|in:cash,mobile_money,bank_transfer,other',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify member belongs to community if member_id is provided
        if ($request->member_id) {
            $member = Member::find($request->member_id);
            if (!$member || $member->community_id !== $community->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member does not belong to this community.'
                ], 422);
            }
        }

        try {
            $offering = Offering::create([
                'member_id' => $request->member_id,
                'amount' => $request->amount,
                'offering_date' => $request->offering_date,
                'offering_type' => $request->offering_type,
                'service_type' => 'community_service',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'recorded_by' => $user->name,
                'is_verified' => false,
                'approval_status' => 'pending',
            ]);

            Log::info('Offering recorded by church elder', [
                'offering_id' => $offering->id,
                'community_id' => $community->id,
                'elder_id' => $user->id,
                'amount' => $offering->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Offering recorded successfully. It will be reviewed by the treasurer.',
                'offering' => $offering->load('member')
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording offering', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record offering: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show community finance page
     */
    public function finance(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view this community.');
        }

        // Get community-specific financial data
        $offeringStats = $this->getOfferingStats($community);
        
        // Get recent offerings for this community
        $recentOfferings = Offering::whereHas('member', function($query) use ($community) {
            $query->where('community_id', $community->id);
        })
        ->with('member')
        ->orderBy('offering_date', 'desc')
        ->limit(20)
        ->get();

        // Get community offerings (mid-week)
        $communityOfferings = CommunityOffering::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->with(['service', 'community'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Calculate totals
        $totalCommunityOfferings = $communityOfferings->sum('amount');
        $totalGeneralOfferings = $recentOfferings->sum('amount');

        return view('church-elder.finance', compact(
            'community', 
            'offeringStats', 
            'recentOfferings', 
            'communityOfferings',
            'totalCommunityOfferings',
            'totalGeneralOfferings'
        ));
    }

    /**
     * Show reports page
     */
    public function reports(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view this community.');
        }

        // Get date range (default to current month)
        $startDate = request()->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = request()->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get statistics
        $stats = $this->getCommunityStats($community, $start, $end);
        $offeringStats = $this->getOfferingStats($community, $start, $end);

        // Get attendance statistics
        $attendanceStats = ServiceAttendance::whereHas('member', function($query) use ($community) {
            $query->where('community_id', $community->id);
        })
        ->whereBetween('attended_at', [$start, $end])
        ->select(DB::raw('DATE(attended_at) as attendance_date'), DB::raw('COUNT(*) as attendance_count'))
        ->groupBy(DB::raw('DATE(attended_at)'))
        ->orderBy('attendance_date', 'desc')
        ->get();

        return view('church-elder.reports', compact('community', 'stats', 'offeringStats', 'attendanceStats', 'startDate', 'endDate'));
    }

    /**
     * Helper: Check if user is elder of community
     */
    private function isElderOfCommunity($user, Community $community): bool
    {
        if (!$user->isChurchElder()) {
            return false;
        }

        $elderCommunities = $user->elderCommunities();
        return $elderCommunities->contains('id', $community->id);
    }

    /**
     * Get community statistics
     */
    private function getCommunityStats(Community $community, $startDate = null, $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        return [
            'total_members' => $community->members()->count(),
            'active_members' => $community->members()->where('membership_type', 'permanent')->count(),
            'total_offerings' => Offering::whereHas('member', function($query) use ($community) {
                $query->where('community_id', $community->id);
            })
            ->whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->sum('amount'),
            'pending_offerings' => Offering::whereHas('member', function($query) use ($community) {
                $query->where('community_id', $community->id);
            })
            ->whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'pending')
            ->sum('amount'),
            'total_attendance' => ServiceAttendance::whereHas('member', function($query) use ($community) {
                $query->where('community_id', $community->id);
            })
            ->whereBetween('attended_at', [$start, $end])
            ->count(),
        ];
    }

    /**
     * Get offering statistics
     */
    private function getOfferingStats(Community $community, $startDate = null, $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        $offerings = Offering::whereHas('member', function($query) use ($community) {
            $query->where('community_id', $community->id);
        })
        ->whereBetween('offering_date', [$start, $end]);

        return [
            'total' => (clone $offerings)->where('approval_status', 'approved')->sum('amount'),
            'pending' => (clone $offerings)->where('approval_status', 'pending')->sum('amount'),
            'count' => (clone $offerings)->where('approval_status', 'approved')->count(),
            'by_type' => (clone $offerings)->where('approval_status', 'approved')
                ->select('offering_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('offering_type')
                ->get(),
        ];
    }

    /**
     * Show create service form
     */
    public function createService(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to create services for this community.');
        }

        $members = $community->members()->orderBy('full_name')->get();

        return view('church-elder.create-service', compact('community', 'members'));
    }

    /**
     * Store a new service
     */
    public function storeService(Request $request, Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create services for this community.'
            ], 403);
        }

        $validated = $request->validate([
            'service_date' => 'required|date',
            'service_type' => 'required|string|in:sunday_service,prayer_meeting,bible_study,youth_service,children_service,women_fellowship,men_fellowship,evangelism,special_event,conference,retreat,other',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'coordinator_id' => 'nullable|exists:members,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'guests_count' => 'nullable|integer|min:0',
            'offerings_amount' => 'nullable|numeric|min:0',
            'scripture_readings' => 'nullable|string',
            'choir' => 'nullable|string|max:255',
            'announcements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verify coordinator belongs to community if provided
        if ($request->coordinator_id) {
            $coordinator = Member::find($request->coordinator_id);
            if (!$coordinator || $coordinator->community_id !== $community->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coordinator does not belong to this community.'
                ], 422);
            }
        }

        // Set church elder ID (current user's member ID)
        $validated['church_elder_id'] = $user->member_id;
        
        // Set default values for numeric fields that cannot be null
        $validated['attendance_count'] = $validated['attendance_count'] ?? 0;
        $validated['guests_count'] = $validated['guests_count'] ?? 0;
        $validated['offerings_amount'] = $validated['offerings_amount'] ?? 0;
        
        // Set status based on whether attendance/offerings are provided
        $validated['status'] = ($validated['attendance_count'] > 0 || $validated['offerings_amount'] > 0) 
            ? 'completed' 
            : 'scheduled';

        try {
            $service = SundayService::create($validated);

            Log::info('Service created by church elder', [
                'service_id' => $service->id,
                'community_id' => $community->id,
                'elder_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully.',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating service', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit service form
     */
    public function editService(Community $community, SundayService $service)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to edit services for this community.');
        }

        // Verify service belongs to this elder
        if ($service->church_elder_id !== $user->member_id) {
            abort(403, 'You are not authorized to edit this service.');
        }

        $members = $community->members()->orderBy('full_name')->get();

        return view('church-elder.edit-service', compact('community', 'service', 'members'));
    }

    /**
     * Update a service
     */
    public function updateService(Request $request, Community $community, SundayService $service)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update services for this community.'
            ], 403);
        }

        // Verify service belongs to this elder
        if ($service->church_elder_id !== $user->member_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this service.'
            ], 403);
        }

        $validated = $request->validate([
            'service_date' => 'required|date',
            'service_type' => 'required|string|in:sunday_service,prayer_meeting,bible_study,youth_service,children_service,women_fellowship,men_fellowship,evangelism,special_event,conference,retreat,other',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'coordinator_id' => 'nullable|exists:members,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'guests_count' => 'nullable|integer|min:0',
            'offerings_amount' => 'nullable|numeric|min:0',
            'scripture_readings' => 'nullable|string',
            'choir' => 'nullable|string|max:255',
            'announcements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verify coordinator belongs to community if provided
        if ($request->coordinator_id) {
            $coordinator = Member::find($request->coordinator_id);
            if (!$coordinator || $coordinator->community_id !== $community->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coordinator does not belong to this community.'
                ], 422);
            }
        }

        // Set default values for numeric fields
        $validated['attendance_count'] = $validated['attendance_count'] ?? 0;
        $validated['guests_count'] = $validated['guests_count'] ?? 0;
        $validated['offerings_amount'] = $validated['offerings_amount'] ?? 0;
        
        // Set status based on whether attendance/offerings are provided
        $validated['status'] = ($validated['attendance_count'] > 0 || $validated['offerings_amount'] > 0) 
            ? 'completed' 
            : 'scheduled';

        try {
            $service->update($validated);

            Log::info('Service updated by church elder', [
                'service_id' => $service->id,
                'community_id' => $community->id,
                'elder_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully.',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating service', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a service
     */
    public function deleteService(Community $community, SundayService $service)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete services for this community.'
            ], 403);
        }

        // Verify service belongs to this elder
        if ($service->church_elder_id !== $user->member_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this service.'
            ], 403);
        }

        try {
            $serviceId = $service->id;
            $service->delete();

            Log::info('Service deleted by church elder', [
                'service_id' => $serviceId,
                'community_id' => $community->id,
                'elder_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting service', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show create task form
     */
    public function createTask(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to create tasks for this community.');
        }

        $members = $community->members()->orderBy('full_name')->get();

        return view('church-elder.create-task', compact('community', 'members'));
    }

    /**
     * Store a new task
     */
    public function storeTask(Request $request, Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create tasks for this community.'
            ], 403);
        }

        $rules = [
            'task_type' => 'required|string|in:member_visit,prayer_request,follow_up,outreach,other',
            'task_title' => 'required|string|max:255',
            'description' => 'required|string',
            'task_date' => 'required|date',
            'task_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        if ($request->task_type === 'member_visit') {
            $rules['member_id'] = 'required|exists:members,id';
        } else {
            $rules['member_id'] = 'nullable|exists:members,id';
        }

        $validated = $request->validate($rules);

        // Verify member belongs to community if provided
        if ($request->member_id) {
            $member = Member::find($request->member_id);
            if (!$member || $member->community_id !== $community->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member does not belong to this community.'
                ], 422);
            }
        }

        $validated['church_elder_id'] = $user->id;
        $validated['community_id'] = $community->id;
        $validated['status'] = 'pending';

        try {
            $task = ChurchElderTask::create($validated);

            Log::info('Task created by church elder', [
                'task_id' => $task->id,
                'community_id' => $community->id,
                'elder_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully.',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating task', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all tasks
     */
    public function tasksIndex(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view tasks for this community.');
        }

        $tasks = ChurchElderTask::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->with(['member', 'community'])
            ->orderBy('task_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('church-elder.tasks.index', compact('community', 'tasks'));
    }

    /**
     * Show a single task
     */
    public function showTask(Community $community, ChurchElderTask $task)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community) || $task->church_elder_id !== $user->id) {
            abort(403, 'You are not authorized to view this task.');
        }

        $task->load(['member', 'community', 'churchElder', 'pastorCommenter']);

        return view('church-elder.tasks.show', compact('community', 'task'));
    }

    /**
     * Update task status
     */
    public function updateTaskStatus(Request $request, Community $community, ChurchElderTask $task)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community) || $task->church_elder_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this task.'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'outcome' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'task' => $task
        ]);
    }

    /**
     * Show create issue form
     */
    public function createIssue(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to report issues for this community.');
        }

        return view('church-elder.create-issue', compact('community'));
    }

    /**
     * Store a new issue
     */
    public function storeIssue(Request $request, Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to report issues for this community.'
            ], 403);
        }

        $validated = $request->validate([
            'issue_type' => 'required|string|in:financial,member_concern,facility,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $validated['church_elder_id'] = $user->id;
        $validated['community_id'] = $community->id;
        $validated['status'] = 'open';

        try {
            $issue = ChurchElderIssue::create($validated);

            Log::info('Issue reported by church elder', [
                'issue_id' => $issue->id,
                'community_id' => $community->id,
                'elder_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Issue reported successfully.',
                'issue' => $issue
            ]);
        } catch (\Exception $e) {
            Log::error('Error reporting issue', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to report issue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all issues
     */
    public function issuesIndex(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view issues for this community.');
        }

        $issues = ChurchElderIssue::where('community_id', $community->id)
            ->where('church_elder_id', $user->id)
            ->with(['community', 'resolver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('church-elder.issues.index', compact('community', 'issues'));
    }

    /**
     * Show a single issue
     */
    public function showIssue(Community $community, ChurchElderIssue $issue)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community) || $issue->church_elder_id !== $user->id) {
            abort(403, 'You are not authorized to view this issue.');
        }

        $issue->load(['community', 'resolver', 'churchElder', 'pastorCommenter']);

        return view('church-elder.issues.show', compact('community', 'issue'));
    }

    /**
     * Show all offerings with filters
     */
    public function allOfferings(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view offerings for this community.');
        }

        $query = Offering::whereHas('member', function($q) use ($community) {
            $q->where('community_id', $community->id);
        })->with('member');

        // Apply filters
        if (request()->has('start_date') && request()->start_date) {
            $query->where('offering_date', '>=', request()->start_date);
        }
        if (request()->has('end_date') && request()->end_date) {
            $query->where('offering_date', '<=', request()->end_date);
        }
        if (request()->has('offering_type') && request()->offering_type) {
            $query->where('offering_type', request()->offering_type);
        }
        if (request()->has('approval_status') && request()->approval_status) {
            $query->where('approval_status', request()->approval_status);
        }
        if (request()->has('member_id') && request()->member_id) {
            $query->where('member_id', request()->member_id);
        }

        $offerings = $query->orderBy('offering_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $members = $community->members()->orderBy('full_name')->get();
        $offeringStats = $this->getOfferingStats($community);

        return view('church-elder.all-offerings', compact('community', 'offerings', 'members', 'offeringStats'));
    }

    /**
     * Show attendance recording page
     */
    public function attendance(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to record attendance for this community.');
        }

        // Get community members
        $members = $community->members()->orderBy('full_name')->get();

        // Get services created by this church elder
        $recentServices = SundayService::where('church_elder_id', $user->member_id)
            ->orderBy('service_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Get selected service if service_id is provided
        $selectedService = null;
        if (request()->has('service_id')) {
            $selectedService = SundayService::where('id', request('service_id'))
                ->where('church_elder_id', $user->member_id)
                ->first();
        }

        // Get recent attendance records
        $recentAttendances = ServiceAttendance::whereHas('member', function($query) use ($community) {
            $query->where('community_id', $community->id);
        })
        ->with(['member', 'sundayService'])
        ->orderBy('attended_at', 'desc')
        ->limit(20)
        ->get();

        // Check time restriction for selected service
        $canRecordAttendance = true;
        $timeRestrictionMessage = '';
        
        if ($selectedService) {
            $serviceDate = $selectedService->service_date ?? null;
            $startTime = $selectedService->start_time ?? null;
            
            if ($serviceDate && $startTime) {
                try {
                    $timeString = $startTime;
                    if ($startTime instanceof \Carbon\Carbon) {
                        $timeString = $startTime->format('H:i:s');
                    } elseif (is_object($startTime) && method_exists($startTime, 'format')) {
                        $timeString = $startTime->format('H:i:s');
                    } elseif (is_string($startTime)) {
                        if (strlen($startTime) === 5) {
                            $timeString = $startTime . ':00';
                        }
                    }
                    
                    $serviceStartDateTime = \Carbon\Carbon::parse($serviceDate->format('Y-m-d') . ' ' . $timeString);
                    $now = now();
                    
                    if ($now->lt($serviceStartDateTime)) {
                        $canRecordAttendance = false;
                        $timeRestrictionMessage = 'Attendance and offering cannot be recorded before the service start time. Service starts at ' . 
                            $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' . 
                            $now->format('d/m/Y h:i A') . '.';
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to parse service start time for attendance restriction', [
                        'service_id' => $selectedService->id,
                        'start_time' => $startTime,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return view('church-elder.attendance', compact('community', 'members', 'recentServices', 'recentAttendances', 'selectedService', 'canRecordAttendance', 'timeRestrictionMessage'));
    }

    /**
     * Record attendance for a service
     */
    public function recordAttendance(Request $request, Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to record attendance for this community.'
            ], 403);
        }

        $request->validate([
            'service_id' => 'required|exists:sunday_services,id',
            'service_type' => 'required|string|in:sunday_service,prayer_meeting,bible_study,youth_service,children_service,women_fellowship,men_fellowship,evangelism,special_event,conference,retreat,other',
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'exists:members,id',
            'attendance_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify service belongs to this community and elder
        $service = SundayService::findOrFail($request->service_id);
        
        // Check if service belongs to this elder
        if ($service->church_elder_id !== $user->member_id) {
            return response()->json([
                'success' => false,
                'message' => 'This service does not belong to you. You can only record attendance for services you created.'
            ], 403);
        }

        // Check time restriction
        $serviceDate = $service->service_date ?? null;
        $startTime = $service->start_time ?? null;
        
        if ($serviceDate && $startTime) {
            try {
                $timeString = $startTime;
                if ($startTime instanceof \Carbon\Carbon) {
                    $timeString = $startTime->format('H:i:s');
                } elseif (is_object($startTime) && method_exists($startTime, 'format')) {
                    $timeString = $startTime->format('H:i:s');
                } elseif (is_string($startTime)) {
                    if (strlen($startTime) === 5) {
                        $timeString = $startTime . ':00';
                    }
                }
                
                $serviceStartDateTime = \Carbon\Carbon::parse($serviceDate->format('Y-m-d') . ' ' . $timeString);
                $now = now();
                
                if ($now->lt($serviceStartDateTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Attendance cannot be recorded before the service start time. Service starts at ' . 
                            $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' . 
                            $now->format('d/m/Y h:i A') . '.'
                    ], 422);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to parse service start time for attendance restriction', [
                    'service_id' => $service->id,
                    'start_time' => $startTime,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Verify all members belong to this community
        // Clean and validate member IDs
        $rawMemberIds = is_array($request->member_ids) ? $request->member_ids : [];
        $memberIds = [];
        
        foreach ($rawMemberIds as $id) {
            // Skip empty values
            if (empty($id) && $id !== '0') {
                continue;
            }
            
            // Convert to integer and skip invalid values
            if (is_numeric($id)) {
                $intId = (int) $id;
                if ($intId > 0) {
                    $memberIds[] = $intId;
                }
            }
        }
        
        // Remove duplicates and re-index array
        $memberIds = array_values(array_unique($memberIds));
        
        if (empty($memberIds)) {
            Log::warning('No valid member IDs provided for attendance', [
                'raw_member_ids' => $rawMemberIds,
                'community_id' => $community->id,
                'service_id' => $request->service_id,
                'user_id' => $user->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one valid member to record attendance. No valid member IDs were provided. Raw data: ' . json_encode($rawMemberIds)
            ], 422);
        }
        
        // Get all members that match the IDs
        $allMembers = Member::whereIn('id', $memberIds)->get();
        
        // Check if all requested members exist
        $foundMemberIds = $allMembers->pluck('id')->toArray();
        $invalidMemberIds = array_diff($memberIds, $foundMemberIds);
        
        if (!empty($invalidMemberIds)) {
            Log::warning('Invalid member IDs provided for attendance', [
                'invalid_ids' => $invalidMemberIds,
                'valid_ids' => $foundMemberIds,
                'requested_ids' => $memberIds,
                'raw_member_ids' => $rawMemberIds,
                'community_id' => $community->id
            ]);
            
            $invalidIdsStr = !empty($invalidMemberIds) ? implode(', ', array_map('strval', $invalidMemberIds)) : 'Unknown';
            return response()->json([
                'success' => false,
                'message' => 'Some selected members do not exist in the system. Invalid member IDs: ' . $invalidIdsStr . '. Please refresh the page and try again.'
            ], 422);
        }
        
        // Filter members that belong to this community
        $members = $allMembers->filter(function($member) use ($community) {
            return $member->community_id == $community->id;
        });

        if ($members->count() !== count($memberIds)) {
            // Find which members don't belong to this community
            $validMemberIds = $members->pluck('id')->toArray();
            $invalidMemberIds = array_diff($memberIds, $validMemberIds);
            $invalidMembers = $allMembers->whereIn('id', $invalidMemberIds)->pluck('full_name')->toArray();
            
            $errorMessage = 'Some selected members do not belong to this community (' . $community->name . ').';
            if (!empty($invalidMembers)) {
                $errorMessage .= ' Invalid members: ' . implode(', ', $invalidMembers);
            }
            $errorMessage .= ' Please select only members from ' . $community->name . '.';
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 422);
        }
        
        // Use the validated member IDs
        $memberIds = $members->pluck('id')->toArray();

        try {
            $attendanceDate = $request->attendance_date ? Carbon::parse($request->attendance_date) : now();
            $recordedBy = $user->name;

            // Remove existing attendance for this service and members
            ServiceAttendance::where('service_type', $request->service_type)
                ->where('service_id', $request->service_id)
                ->whereIn('member_id', $memberIds)
                ->delete();

            // Create new attendance records
            $attendanceData = [];
            foreach ($memberIds as $memberId) {
                $attendanceData[] = [
                    'service_type' => $request->service_type,
                    'service_id' => $request->service_id,
                    'member_id' => $memberId,
                    'child_id' => null,
                    'attended_at' => $attendanceDate,
                    'recorded_by' => $recordedBy,
                    'notes' => $request->notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($attendanceData)) {
                ServiceAttendance::insert($attendanceData);
            }

            // Update service attendance count
            $totalAttendance = ServiceAttendance::where('service_type', $request->service_type)
                ->where('service_id', $request->service_id)
                ->count();

            $service->update(['attendance_count' => $totalAttendance]);

            Log::info('Attendance recorded by church elder', [
                'service_id' => $request->service_id,
                'community_id' => $community->id,
                'elder_id' => $user->id,
                'member_count' => count($memberIds)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully.',
                'attendance_count' => $totalAttendance
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording attendance', [
                'error' => $e->getMessage(),
                'community_id' => $community->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View attendance records
     */
    public function viewAttendance(Community $community)
    {
        $user = auth()->user();
        
        if (!$this->isElderOfCommunity($user, $community)) {
            abort(403, 'You are not authorized to view attendance for this community.');
        }

        $query = ServiceAttendance::whereHas('member', function($q) use ($community) {
            $q->where('community_id', $community->id);
        })->with(['member', 'sundayService']);

        // Apply filters
        if (request()->has('start_date') && request()->start_date) {
            $query->whereDate('attended_at', '>=', request()->start_date);
        }
        if (request()->has('end_date') && request()->end_date) {
            $query->whereDate('attended_at', '<=', request()->end_date);
        }
        if (request()->has('service_type') && request()->service_type) {
            $query->where('service_type', request()->service_type);
        }
        if (request()->has('member_id') && request()->member_id) {
            $query->where('member_id', request()->member_id);
        }

        $attendances = $query->orderBy('attended_at', 'desc')
            ->paginate(20);

        $members = $community->members()->orderBy('full_name')->get();
        $services = SundayService::orderBy('service_date', 'desc')
            ->limit(50)
            ->get();

        return view('church-elder.view-attendance', compact('community', 'attendances', 'members', 'services'));
    }
}
