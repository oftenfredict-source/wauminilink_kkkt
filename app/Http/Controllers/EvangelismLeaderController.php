<?php

namespace App\Http\Controllers;

use App\Models\EvangelismReport;
use App\Models\EvangelismTask;
use App\Models\EvangelismIssue;
use App\Models\ChurchElderTask;
use App\Models\ChurchElderIssue;
use App\Models\Member;
use App\Models\Community;
use App\Models\Campus;
use App\Models\Leader;
use App\Models\CommunityOffering;
use App\Models\BranchOffering;
use App\Models\SundayService;
use App\Models\ServiceAttendance;
use App\Models\BereavementEvent;
use App\Models\BereavementContribution;
use App\Models\Offering;
use App\Models\Tithe;
use App\Services\SmsService;
use App\Services\SettingsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EvangelismLeaderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user is an evangelism leader
     */
    private function checkEvangelismLeaderPermission()
    {
        if (!auth()->check()) {
            abort(401, 'Please log in to access this page.');
        }
        
        $user = auth()->user();
        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Only Evangelism Leaders can access this page.');
        }
    }

    /**
     * Display the evangelism leader dashboard
     */
    public function index()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get statistics (including children who are church members)
        $adultMembers = Member::where('campus_id', $campus->id)->count();
        $childMembers = \App\Models\Child::where('is_church_member', true)
            ->whereHas('member', function($query) use ($campus) {
                $query->where('campus_id', $campus->id);
            })
            ->count();
        $totalMembers = $adultMembers + $childMembers;
        $totalCommunities = Community::where('campus_id', $campus->id)->count();
        $pendingTasks = EvangelismTask::where('evangelism_leader_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $openIssues = EvangelismIssue::where('evangelism_leader_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
        
        // Get pending offerings count (only for communities in this campus)
        $communityIds = Community::where('campus_id', $campus->id)->pluck('id');
        $pendingOfferings = CommunityOffering::whereIn('community_id', $communityIds)
            ->where('status', 'pending_evangelism')
            ->count();
        $pendingOfferingsAmount = CommunityOffering::whereIn('community_id', $communityIds)
            ->where('status', 'pending_evangelism')
            ->sum('amount');

        // Get recent reports
        $recentReports = EvangelismReport::where('evangelism_leader_id', $user->id)
            ->with(['community', 'campus'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent tasks
        $recentTasks = EvangelismTask::where('evangelism_leader_id', $user->id)
            ->with(['member', 'community', 'campus'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent issues
        $recentIssues = EvangelismIssue::where('evangelism_leader_id', $user->id)
            ->with(['community', 'campus'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get branch Sunday services (where all communities join together)
        $branchServices = SundayService::where('campus_id', $campus->id)
            ->where('is_branch_service', true)
            ->where('service_type', 'sunday_service')
            ->with(['branchOfferings'])
            ->orderBy('service_date', 'desc')
            ->limit(10)
            ->get();

        // Get branch offerings statistics
        $branchOfferingsPending = BranchOffering::where('campus_id', $campus->id)
            ->where('status', 'pending_secretary')
            ->sum('amount');
        $branchOfferingsPendingCount = BranchOffering::where('campus_id', $campus->id)
            ->where('status', 'pending_secretary')
            ->count();

        // Get bereavement statistics for this campus
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        $activeBereavements = 0;
        $totalBereavementContributions = 0;
        
        if ($campusMemberIds->isNotEmpty()) {
            $activeBereavements = BereavementEvent::where('status', 'open')
                ->whereHas('contributions', function($q) use ($campusMemberIds) {
                    $q->whereIn('member_id', $campusMemberIds);
                })
                ->count();
            $totalBereavementContributions = BereavementContribution::whereIn('member_id', $campusMemberIds)
                ->where('has_contributed', true)
                ->sum('contribution_amount');
        }

        return view('evangelism-leader.dashboard', compact(
            'totalMembers',
            'totalCommunities',
            'pendingTasks',
            'openIssues',
            'pendingOfferings',
            'pendingOfferingsAmount',
            'recentReports',
            'recentTasks',
            'recentIssues',
            'campus',
            'branchServices',
            'branchOfferingsPending',
            'branchOfferingsPendingCount',
            'activeBereavements',
            'totalBereavementContributions'
        ));
    }

    /**
     * Show register member page (redirects to members.add with campus_id)
     */
    public function showRegisterMember()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        return redirect()->route('members.add', ['campus_id' => $campus->id]);
    }

    /**
     * Show create report form
     */
    public function createReport()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('evangelism-leader.create-report', compact('communities', 'campus'));
    }

    /**
     * Store a new report
     */
    public function storeReport(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $validated = $request->validate([
            'community_id' => 'nullable|exists:communities,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'report_date' => 'required|date',
        ]);

        $validated['evangelism_leader_id'] = $user->id;
        $validated['campus_id'] = $campus->id;
        $validated['status'] = 'submitted';
        $validated['submitted_at'] = now();

        EvangelismReport::create($validated);

        return redirect()->route('evangelism-leader.reports.index')
            ->with('success', 'Report submitted successfully.');
    }

    /**
     * List all reports
     */
    public function reportsIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        $reports = EvangelismReport::where('evangelism_leader_id', $user->id)
            ->with(['community', 'campus', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('evangelism-leader.reports.index', compact('reports'));
    }

    /**
     * Show a single report
     */
    public function showReport(EvangelismReport $report)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        if ($report->evangelism_leader_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to this report.');
        }

        $report->load(['community', 'campus', 'reviewer']);

        return view('evangelism-leader.reports.show', compact('report'));
    }

    /**
     * Show create task form
     */
    public function createTask()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $members = Member::where('campus_id', $campus->id)
            ->orderBy('full_name')
            ->get();

        return view('evangelism-leader.create-task', compact('communities', 'members', 'campus'));
    }

    /**
     * Store a new task
     */
    public function storeTask(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Validate based on task type
        $rules = [
            'community_id' => 'nullable|exists:communities,id',
            'task_type' => 'required|string|in:member_visit,community_outreach,follow_up,other',
            'task_title' => 'required|string|max:255',
            'description' => 'required|string',
            'task_date' => 'required|date',
            'task_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'outcome' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
        
        // Make member_id required for member_visit tasks
        if ($request->task_type === 'member_visit') {
            $rules['member_id'] = 'required|exists:members,id';
        } else {
            $rules['member_id'] = 'nullable|exists:members,id';
        }

        $validated = $request->validate($rules);
        
        // Validate that member belongs to the campus if provided
        if (isset($validated['member_id'])) {
            $member = \App\Models\Member::find($validated['member_id']);
            if ($member && $member->campus_id !== $campus->id) {
                return redirect()->back()
                    ->withErrors(['member_id' => 'The selected member does not belong to your campus.'])
                    ->withInput();
            }
        }

        $validated['evangelism_leader_id'] = $user->id;
        $validated['campus_id'] = $campus->id;
        $validated['status'] = 'pending';
        $validated['sent_to_pastor'] = false;

        $task = EvangelismTask::create($validated);
        
        // Send SMS notifications for member_visit tasks
        if ($request->task_type === 'member_visit' && isset($validated['member_id'])) {
            // Reload task with member relationship
            $task->load('member');
            $this->sendMemberVisitNotifications($task, $campus);
        }

        return redirect()->route('evangelism-leader.tasks.index')
            ->with('success', 'Task report created successfully.');
    }

    /**
     * List all tasks
     */
    public function tasksIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        $tasks = EvangelismTask::where('evangelism_leader_id', $user->id)
            ->with(['member', 'community', 'campus'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('evangelism-leader.tasks.index', compact('tasks'));
    }

    /**
     * Show a single task
     */
    public function showTask(EvangelismTask $task)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        if ($task->evangelism_leader_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load(['member', 'community', 'campus', 'pastorCommenter']);

        return view('evangelism-leader.tasks.show', compact('task'));
    }

    /**
     * Update task status
     */
    public function updateTaskStatus(Request $request, EvangelismTask $task)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        if ($task->evangelism_leader_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to this task.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
            'outcome' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('evangelism-leader.tasks.show', $task)
            ->with('success', 'Task status updated successfully.');
    }

    /**
     * Show create issue form
     */
    public function createIssue()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('evangelism-leader.create-issue', compact('communities', 'campus'));
    }

    /**
     * Store a new issue
     */
    public function storeIssue(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $validated = $request->validate([
            'community_id' => 'nullable|exists:communities,id',
            'issue_type' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $validated['evangelism_leader_id'] = $user->id;
        $validated['campus_id'] = $campus->id;
        $validated['status'] = 'open';

        EvangelismIssue::create($validated);

        return redirect()->route('evangelism-leader.issues.index')
            ->with('success', 'Issue reported successfully.');
    }

    /**
     * List all issues
     */
    public function issuesIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        $issues = EvangelismIssue::where('evangelism_leader_id', $user->id)
            ->with(['community', 'campus', 'resolver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('evangelism-leader.issues.index', compact('issues'));
    }

    /**
     * Show a single issue
     */
    public function showIssue(EvangelismIssue $issue)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        
        if ($issue->evangelism_leader_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to this issue.');
        }

        $issue->load(['community', 'campus', 'resolver', 'pastorCommenter']);

        return view('evangelism-leader.issues.show', compact('issue'));
    }
    
    /**
     * Send SMS notifications for member visit tasks
     */
    private function sendMemberVisitNotifications(EvangelismTask $task, Campus $campus)
    {
        try {
            // Check if SMS notifications are enabled
            if (!SettingsService::get('enable_sms_notifications', false)) {
                Log::info('SMS notifications disabled, skipping member visit notification');
                return false;
            }
            
            $smsService = app(SmsService::class);
            $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
            
            // Get evangelism leader name
            $evangelismLeader = Leader::where('member_id', Auth::user()->member_id)
                ->where('position', 'evangelism_leader')
                ->where('is_active', true)
                ->with('member')
                ->first();
            
            $evangelismLeaderName = $evangelismLeader && $evangelismLeader->member 
                ? $evangelismLeader->member->full_name 
                : 'Evangelism Leader';
            
            // Send SMS to visited member
            if ($task->member && $task->member->phone_number) {
                $memberMessage = "Shalom {$task->member->full_name}, {$evangelismLeaderName} amefanya ziara kwako leo ({$task->task_date->format('d/m/Y')}). ";
                $memberMessage .= "Tunaamini ziara hii imekuwa na manufaa. Mungu akubariki na akupe nguvu zaidi.";
                
                $result = $smsService->sendDebug($task->member->phone_number, $memberMessage);
                
                Log::info('Member visit SMS sent to member', [
                    'member_id' => $task->member->id,
                    'member_name' => $task->member->full_name,
                    'phone' => $task->member->phone_number,
                    'ok' => $result['ok'] ?? false,
                ]);
            }
            
            // Send SMS to senior pastor (pastor position)
            $pastors = Leader::where('campus_id', $campus->id)
                ->where('position', 'pastor')
                ->where('is_active', true)
                ->with('member')
                ->get();
            
            foreach ($pastors as $pastor) {
                if ($pastor->member && $pastor->member->phone_number) {
                    $pastorMessage = "Habari Mchungaji {$pastor->member->full_name}, ";
                    $pastorMessage .= "{$evangelismLeaderName} amefanya ziara kwa {$task->member->full_name} ({$task->member->member_id}) ";
                    $pastorMessage .= "tarehe {$task->task_date->format('d/m/Y')}. ";
                    if ($task->task_time) {
                        $timeStr = is_string($task->task_time) ? $task->task_time : $task->task_time->format('H:i');
                        $pastorMessage .= "Muda: {$timeStr}. ";
                    }
                    if ($task->location) {
                        $pastorMessage .= "Mahali: {$task->location}. ";
                    }
                    $pastorMessage .= "Tafadhali angalia ripoti kwenye mfumo.";
                    
                    $result = $smsService->sendDebug($pastor->member->phone_number, $pastorMessage);
                    
                    Log::info('Member visit SMS sent to pastor', [
                        'pastor_id' => $pastor->id,
                        'pastor_name' => $pastor->member->full_name,
                        'phone' => $pastor->member->phone_number,
                        'ok' => $result['ok'] ?? false,
                    ]);
                    
                    // Mark task as sent to pastor
                    $task->update([
                        'sent_to_pastor' => true,
                        'sent_to_pastor_at' => now(),
                    ]);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending member visit SMS notifications', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * List branch Sunday services
     */
    public function branchServicesIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $services = SundayService::where('campus_id', $campus->id)
            ->where('is_branch_service', true)
            ->where('service_type', 'sunday_service')
            ->with(['branchOfferings', 'evangelismLeader'])
            ->orderBy('service_date', 'desc')
            ->paginate(15);

        return view('evangelism-leader.branch-services.index', compact('services', 'campus'));
    }

    /**
     * Show create branch service form
     */
    public function branchServicesCreate()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        return view('evangelism-leader.branch-services.create', compact('campus'));
    }

    /**
     * Store branch Sunday service
     */
    public function branchServicesStore(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $validated = $request->validate([
            'service_date' => 'required|date',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'guests_count' => 'nullable|integer|min:0',
            'scripture_readings' => 'nullable|string',
            'choir' => 'nullable|string|max:255',
            'announcements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['campus_id'] = $campus->id;
        $validated['evangelism_leader_id'] = $user->id;
        $validated['is_branch_service'] = true;
        $validated['service_type'] = 'sunday_service';
        $validated['status'] = 'scheduled';
        $validated['attendance_count'] = $validated['attendance_count'] ?? 0;
        $validated['guests_count'] = $validated['guests_count'] ?? 0;
        $validated['offerings_amount'] = 0;

        // Check for duplicate branch service for this campus
        $existingService = SundayService::where('service_date', $validated['service_date'])
            ->where('service_type', $validated['service_type'])
            ->where('campus_id', $campus->id)
            ->where('is_branch_service', true)
            ->first();
        
        if ($existingService) {
            return redirect()->back()
                ->withErrors(['service_date' => 'A branch Sunday service already exists for this date in your campus.'])
                ->withInput();
        }

        try {
            $service = SundayService::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withErrors(['service_date' => 'A service already exists for this date and type.'])
                    ->withInput();
            }
            throw $e;
        }

        Log::info('Branch Sunday service created', [
            'service_id' => $service->id,
            'campus_id' => $campus->id,
            'evangelism_leader_id' => $user->id
        ]);

        return redirect()->route('evangelism-leader.branch-services.index')
            ->with('success', 'Branch Sunday service created successfully.');
    }

    /**
     * Show branch service details
     */
    public function branchServicesShow(SundayService $service)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus || $service->campus_id !== $campus->id || !$service->is_branch_service) {
            abort(404, 'Service not found.');
        }

        $service->load(['branchOfferings', 'evangelismLeader', 'campus']);

        return view('evangelism-leader.branch-services.show', compact('service', 'campus'));
    }

    /**
     * List branch offerings
     */
    public function branchOfferingsIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $offerings = BranchOffering::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->with(['service', 'secretary'])
            ->orderBy('offering_date', 'desc')
            ->paginate(15);

        $totalPending = BranchOffering::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('status', 'pending_secretary')
            ->sum('amount');

        $totalCompleted = BranchOffering::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        return view('evangelism-leader.branch-offerings.index', compact('offerings', 'campus', 'totalPending', 'totalCompleted'));
    }

    /**
     * Show create branch offering form
     */
    public function branchOfferingsCreate(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $service = null;
        $canRecordOffering = true;
        $timeRestrictionMessage = '';
        
        if ($request->has('service_id')) {
            $service = SundayService::where('id', $request->service_id)
                ->where('campus_id', $campus->id)
                ->where('is_branch_service', true)
                ->firstOrFail();
            
            // Check date and time restriction for offering
            $serviceDate = $service->service_date ?? null;
            $startTime = $service->start_time ?? null;
            $now = now();
            
            if ($serviceDate) {
                // First check if service date has been reached
                $serviceDateOnly = \Carbon\Carbon::parse($serviceDate->format('Y-m-d'))->startOfDay();
                $today = $now->copy()->startOfDay();
                
                if ($today->lt($serviceDateOnly)) {
                    $canRecordOffering = false;
                    $timeRestrictionMessage = 'Offering cannot be recorded before the service date. Service date is ' . 
                        $serviceDateOnly->format('d/m/Y') . '. Today is ' . 
                        $today->format('d/m/Y') . '.';
                } elseif ($startTime) {
                    // If date is reached, check if start time has been reached
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
                        
                        if ($now->lt($serviceStartDateTime)) {
                            $canRecordOffering = false;
                            $timeRestrictionMessage = 'Offering cannot be recorded before the service start time. Service starts at ' . 
                                $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' . 
                                $now->format('d/m/Y h:i A') . '.';
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to parse service start time for offering restriction', [
                            'service_id' => $service->id,
                            'start_time' => $startTime,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        // Get recent branch services for selection
        $recentServices = SundayService::where('campus_id', $campus->id)
            ->where('is_branch_service', true)
            ->where('service_type', 'sunday_service')
            ->orderBy('service_date', 'desc')
            ->limit(20)
            ->get();

        return view('evangelism-leader.branch-offerings.create', compact('campus', 'service', 'recentServices', 'canRecordOffering', 'timeRestrictionMessage'));
    }

    /**
     * Store branch offering
     */
    public function branchOfferingsStore(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        $validated = $request->validate([
            'service_id' => 'nullable|exists:sunday_services,id',
            'amount' => 'required|numeric|min:0.01',
            'offering_date' => 'required|date',
            'collection_method' => 'required|string|in:cash,mobile_money,bank_transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'leader_notes' => 'nullable|string',
        ]);

        // Verify service belongs to this campus if provided and check date/time restriction
        if ($validated['service_id']) {
            $service = SundayService::findOrFail($validated['service_id']);
            if ($service->campus_id !== $campus->id || !$service->is_branch_service) {
                return back()->with('error', 'Invalid service selected.')->withInput();
            }
            
            // Check date and time restriction
            $serviceDate = $service->service_date ?? null;
            $startTime = $service->start_time ?? null;
            $now = now();
            
            if ($serviceDate) {
                // First check if service date has been reached
                $serviceDateOnly = \Carbon\Carbon::parse($serviceDate->format('Y-m-d'))->startOfDay();
                $today = $now->copy()->startOfDay();
                
                if ($today->lt($serviceDateOnly)) {
                    return back()->with('error', 'Offering cannot be recorded before the service date. Service date is ' . 
                        $serviceDateOnly->format('d/m/Y') . '. Today is ' . 
                        $today->format('d/m/Y') . '.')->withInput();
                } elseif ($startTime) {
                    // If date is reached, check if start time has been reached
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
                        
                        if ($now->lt($serviceStartDateTime)) {
                            return back()->with('error', 'Offering cannot be recorded before the service start time. Service starts at ' . 
                                $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' . 
                                $now->format('d/m/Y h:i A') . '.')->withInput();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to parse service start time for offering restriction', [
                            'service_id' => $service->id,
                            'start_time' => $startTime,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        $validated['campus_id'] = $campus->id;
        $validated['evangelism_leader_id'] = $user->id;
        $validated['status'] = 'pending_secretary';
        $validated['handover_to_secretary_at'] = now();

        $offering = BranchOffering::create($validated);

        Log::info('Branch offering created', [
            'offering_id' => $offering->id,
            'campus_id' => $campus->id,
            'evangelism_leader_id' => $user->id,
            'amount' => $offering->amount
        ]);

        return redirect()->route('evangelism-leader.branch-offerings.index')
            ->with('success', 'Branch offering recorded successfully. It has been sent to the General Secretary for approval.');
    }

    /**
     * Show branch offering details
     */
    public function branchOfferingsShow(BranchOffering $offering)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus || $offering->campus_id !== $campus->id || $offering->evangelism_leader_id !== $user->id) {
            abort(404, 'Offering not found.');
        }

        $offering->load(['service', 'secretary', 'rejectedBy', 'campus']);

        return view('evangelism-leader.branch-offerings.show', compact('offering', 'campus'));
    }

    /**
     * Show attendance recording form for branch service
     */
    public function branchServiceAttendance(SundayService $service)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus || $service->campus_id !== $campus->id || !$service->is_branch_service) {
            abort(404, 'Service not found.');
        }

        // Get all members from all communities in this campus
        $members = Member::where('campus_id', $campus->id)
            ->orderBy('full_name')
            ->get();

        // Get existing attendance for this service
        $existingAttendance = ServiceAttendance::where('service_type', 'sunday_service')
            ->where('service_id', $service->id)
            ->with('member')
            ->get();

        // Check time restriction for attendance and offering
        $canRecordAttendance = true;
        $timeRestrictionMessage = '';
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
                    $canRecordAttendance = false;
                    $timeRestrictionMessage = 'Attendance and offering cannot be recorded before the service start time. Service starts at ' . 
                        $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' . 
                        $now->format('d/m/Y h:i A') . '.';
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to parse service start time for attendance restriction', [
                    'service_id' => $service->id,
                    'start_time' => $startTime,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('evangelism-leader.branch-services.attendance', compact('service', 'campus', 'members', 'existingAttendance', 'canRecordAttendance', 'timeRestrictionMessage'));
    }

    /**
     * Record attendance for branch service
     */
    public function branchServiceRecordAttendance(Request $request, SundayService $service)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus || $service->campus_id !== $campus->id || !$service->is_branch_service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found or unauthorized.'
            ], 403);
        }

        $request->validate([
            'service_type' => 'required|string|in:sunday_service',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'nullable|numeric',
            'attendance_date' => 'nullable|date',
            'guests_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

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

        // Clean and validate member IDs
        $rawMemberIds = is_array($request->member_ids) ? $request->member_ids : [];
        $memberIds = [];

        foreach ($rawMemberIds as $id) {
            if (is_numeric($id)) {
                $intId = (int) $id;
                if ($intId > 0) {
                    $memberIds[] = $intId;
                }
            }
        }

        $memberIds = array_values(array_unique($memberIds));

        if (empty($memberIds) && (!$request->guests_count || $request->guests_count == 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one member or enter guests count.'
            ], 422);
        }

        // Verify all members belong to this campus
        if (!empty($memberIds)) {
            $allMembers = Member::whereIn('id', $memberIds)
                ->where('campus_id', $campus->id)
                ->get();

            if ($allMembers->count() !== count($memberIds)) {
                $foundMemberIds = $allMembers->pluck('id')->toArray();
                $invalidMemberIds = array_diff($memberIds, $foundMemberIds);
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected members do not belong to this campus. Invalid member IDs: ' . implode(', ', $invalidMemberIds)
                ], 422);
            }
        }

        try {
            $attendanceDate = $request->attendance_date ? Carbon::parse($request->attendance_date) : now();
            $recordedBy = $user->name;

            // Remove existing attendance for this service
            ServiceAttendance::where('service_type', 'sunday_service')
                ->where('service_id', $service->id)
                ->delete();

            // Create new attendance records
            $attendanceData = [];
            foreach ($memberIds as $memberId) {
                $attendanceData[] = [
                    'service_type' => 'sunday_service',
                    'service_id' => $service->id,
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

            // Update service attendance count and guests count
            $totalAttendance = ServiceAttendance::where('service_type', 'sunday_service')
                ->where('service_id', $service->id)
                ->count();

            $guestsCount = $request->guests_count ?? 0;

            $service->update([
                'attendance_count' => $totalAttendance,
                'guests_count' => $guestsCount,
                'status' => 'completed'
            ]);

            Log::info('Branch service attendance recorded by evangelism leader', [
                'service_id' => $service->id,
                'campus_id' => $campus->id,
                'evangelism_leader_id' => $user->id,
                'member_count' => count($memberIds),
                'guests_count' => $guestsCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully for ' . count($memberIds) . ' members' . ($guestsCount > 0 ? ' and ' . $guestsCount . ' guests' : '') . '.',
                'attendance_count' => $totalAttendance,
                'guests_count' => $guestsCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording branch service attendance', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
                'campus_id' => $campus->id,
                'user_id' => $user->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display all Church Elder tasks from communities in this evangelism leader's campus
     */
    public function churchElderTasksIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get all community IDs in this campus
        $communityIds = Community::where('campus_id', $campus->id)->pluck('id');

        // Get tasks from church elders in these communities
        $tasks = ChurchElderTask::whereIn('community_id', $communityIds)
            ->with(['churchElder', 'community', 'member', 'pastorCommenter'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('evangelism-leader.church-elder-tasks.index', compact('tasks', 'campus'));
    }

    /**
     * Show a single Church Elder task
     */
    public function showChurchElderTask(ChurchElderTask $task)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the task belongs to a community in this evangelism leader's campus
        if ($task->community->campus_id !== $campus->id) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load(['churchElder', 'community', 'member', 'pastorCommenter']);

        return view('evangelism-leader.church-elder-tasks.show', compact('task', 'campus'));
    }

    /**
     * Display all Church Elder issues from communities in this evangelism leader's campus
     */
    public function churchElderIssuesIndex()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get all community IDs in this campus
        $communityIds = Community::where('campus_id', $campus->id)->pluck('id');

        // Get issues from church elders in these communities
        $issues = ChurchElderIssue::whereIn('community_id', $communityIds)
            ->with(['churchElder', 'community', 'pastorCommenter'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('evangelism-leader.church-elder-issues.index', compact('issues', 'campus'));
    }

    /**
     * Show a single Church Elder issue
     */
    public function showChurchElderIssue(ChurchElderIssue $issue)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the issue belongs to a community in this evangelism leader's campus
        if ($issue->community->campus_id !== $campus->id) {
            abort(403, 'Unauthorized access to this issue.');
        }

        $issue->load(['churchElder', 'community', 'pastorCommenter']);

        return view('evangelism-leader.church-elder-issues.show', compact('issue', 'campus'));
    }

    /**
     * Display bereavement events for evangelism leader's campus
     */
    public function bereavementIndex(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get members from this campus
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        
        // Get community IDs from this campus
        $campusCommunityIds = Community::where('campus_id', $campus->id)->pluck('id');
        
        // Get bereavement events that:
        // 1. Have contributions from members in this campus, OR
        // 2. Are associated with a community in this campus, OR
        // 3. Were created by this evangelism leader
        $query = BereavementEvent::with(['contributions.member', 'creator', 'community'])
            ->where(function($q) use ($campusMemberIds, $campusCommunityIds, $user) {
                // Events with contributions from this campus
                $q->whereHas('contributions', function($subQ) use ($campusMemberIds) {
                    $subQ->whereIn('member_id', $campusMemberIds);
                })
                // OR events associated with communities in this campus
                ->orWhereIn('community_id', $campusCommunityIds)
                // OR events created by this evangelism leader
                ->orWhere('created_by', $user->id);
            });
        
        // Filter by community if provided
        if ($request->filled('community_id')) {
            $query->where('community_id', $request->input('community_id'));
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function($q) use ($s) {
                $q->where('deceased_name', 'like', "%{$s}%")
                  ->orWhere('family_details', 'like', "%{$s}%")
                  ->orWhere('related_departments', 'like', "%{$s}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('incident_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('incident_date', '<=', $request->date('to'));
        }

        $events = $query->orderBy('incident_date', 'desc')->paginate(15);
        $events->appends($request->query());

        $totalMembers = Member::where('campus_id', $campus->id)->count();
        
        // Get communities from this campus for filter
        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('evangelism-leader.bereavement.index', compact('events', 'totalMembers', 'campus', 'communities'));
    }

    /**
     * Show the form for creating a new bereavement event
     */
    public function bereavementCreate()
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get communities from this campus
        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get members from this campus only
        $members = Member::where('campus_id', $campus->id)
            ->with('community')
            ->orderBy('full_name')
            ->get();
        
        return view('evangelism-leader.bereavement.create', compact('members', 'campus', 'communities'));
    }

    /**
     * Store a newly created bereavement event
     */
    public function bereavementStore(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        try {
            $validated = $request->validate([
                'deceased_name' => 'required|string|max:255',
                'family_details' => 'nullable|string',
                'related_departments' => 'nullable|string',
                'incident_date' => 'required|date',
                'contribution_start_date' => 'required|date',
                'contribution_end_date' => 'required|date|after:contribution_start_date',
                'notes' => 'nullable|string',
                'community_id' => 'nullable|exists:communities,id',
                'member_ids' => 'nullable|array',
                'member_ids.*' => 'exists:members,id',
                'send_notifications' => 'boolean',
            ]);

            // Handle community_id - convert empty string to null
            $communityId = $request->input('community_id');
            if ($communityId === '' || $communityId === null) {
                $communityId = null;
                $validated['community_id'] = null;
            } else {
                // Verify community belongs to this campus if provided
                $community = Community::find($communityId);
                if (!$community) {
                    return redirect()->back()
                        ->withErrors(['community_id' => 'Selected community not found.'])
                        ->withInput();
                }
                if ($community->campus_id !== $campus->id) {
                    return redirect()->back()
                        ->withErrors(['community_id' => 'Selected community does not belong to your campus.'])
                        ->withInput();
                }
                $validated['community_id'] = $communityId;
            }

            // Verify all selected members belong to this campus
            if ($request->filled('member_ids')) {
                $invalidMembers = Member::whereIn('id', $request->input('member_ids'))
                    ->where('campus_id', '!=', $campus->id)
                    ->exists();
                
                if ($invalidMembers) {
                    return redirect()->back()
                        ->withErrors(['member_ids' => 'All selected members must belong to your campus.'])
                        ->withInput();
                }
            }

            $validated['created_by'] = auth()->id();
            $validated['status'] = 'open';

            \Log::info('Creating bereavement event', [
                'validated' => $validated,
                'user_id' => auth()->id(),
                'campus_id' => $campus->id
            ]);

            $event = BereavementEvent::create($validated);
            
            \Log::info('Bereavement event created successfully', [
                'event_id' => $event->id
            ]);

            // Create contribution records for members from this campus/community
            $memberIds = $request->input('member_ids', []);
            $selectedCommunityId = $validated['community_id'] ?? null;
            
            if (empty($memberIds)) {
                // If no members selected, create records based on community or campus
                if ($selectedCommunityId) {
                    // If community selected, include all members from that community
                    $members = Member::where('campus_id', $campus->id)
                        ->where('community_id', $selectedCommunityId)
                        ->get();
                } else {
                    // If no community selected, include all members from this campus
                    $members = Member::where('campus_id', $campus->id)->get();
                }
            } else {
                $members = Member::whereIn('id', $memberIds)
                    ->where('campus_id', $campus->id)
                    ->get();
            }

            \Log::info('Members found for bereavement contributions', [
                'count' => $members->count(),
                'community_id' => $selectedCommunityId,
                'member_ids_provided' => !empty($memberIds)
            ]);

            if ($members->isEmpty()) {
                \Log::warning('No members found for bereavement event', [
                    'campus_id' => $campus->id,
                    'community_id' => $selectedCommunityId
                ]);
                // Still allow event creation even if no members found
            }

            foreach ($members as $member) {
                try {
                    BereavementContribution::create([
                        'bereavement_event_id' => $event->id,
                        'member_id' => $member->id,
                        'has_contributed' => false,
                        'contribution_type' => 'individual',
                        'contribution_amount' => 0,
                        'contribution_date' => now()->toDateString(),
                        'payment_method' => 'cash',
                        'recorded_by' => auth()->id(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create contribution record for member: ' . $member->id, [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Send notifications if requested
            if ($request->boolean('send_notifications')) {
                try {
                    $notificationService = app(NotificationService::class);
                    $notificationService->sendBereavementNotifications(
                        $event,
                        $members->pluck('id')->toArray(),
                        'created'
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send bereavement notifications: ' . $e->getMessage());
                }
            }

            // Send notification to pastors about the new bereavement event
            $this->sendBereavementNotificationToPastors($event, $campus);

            return redirect()->route('evangelism-leader.bereavement.index')
                ->with('success', 'Bereavement event created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for bereavement event', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to create bereavement event', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create bereavement event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show a specific bereavement event
     */
    public function bereavementShow(BereavementEvent $bereavement)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the bereavement event belongs to this campus
        // Allow access if:
        // 1. Event has contributions from members in this campus, OR
        // 2. Event is associated with a community in this campus, OR
        // 3. Event was created by this evangelism leader
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        $campusCommunityIds = Community::where('campus_id', $campus->id)->pluck('id');
        
        $hasCampusContributions = $bereavement->contributions()
            ->whereIn('member_id', $campusMemberIds)
            ->exists();
        
        $belongsToCampusCommunity = $bereavement->community_id && 
            $campusCommunityIds->contains($bereavement->community_id);
        
        $createdByThisLeader = $bereavement->created_by === $user->id;
        
        if (!$hasCampusContributions && !$belongsToCampusCommunity && !$createdByThisLeader) {
            abort(403, 'Unauthorized access to this bereavement event.');
        }

        $bereavement->load(['contributions.member', 'creator', 'community']);
        
        // Filter contributions to show only members from this campus
        $contributions = $bereavement->contributions()
            ->whereIn('member_id', $campusMemberIds)
            ->with('member')
            ->get();
        
        // Get all members from this campus who haven't contributed yet
        // This includes members with contribution records (has_contributed = false) 
        // and members who don't have a contribution record yet
        $contributedMemberIds = $bereavement->contributions()
            ->where('has_contributed', true)
            ->whereIn('member_id', $campusMemberIds)
            ->pluck('member_id')
            ->filter()
            ->toArray();
        
        $availableMembers = Member::whereIn('id', $campusMemberIds)
            ->whereNotIn('id', $contributedMemberIds)
            ->orderBy('full_name')
            ->get();
        
        // Calculate totals
        $totalContributions = $contributions->where('has_contributed', true)->sum(function($contribution) {
            return $contribution->contribution_amount ?? 0;
        });
        $totalContributors = $contributions->where('has_contributed', true)->count();
        
        // Total members should be all members from the campus
        $totalMembers = $campusMemberIds->count();
        
        // Get all members who have contribution records (contributed or not)
        $membersWithRecords = $contributions->pluck('member_id')->unique();
        // Get all members from campus who don't have contribution records
        $membersWithoutRecords = Member::whereIn('id', $campusMemberIds)
            ->whereNotIn('id', $membersWithRecords)
            ->pluck('id');
        
        // Create a collection of all non-contributors for the view
        // This includes: members with records (has_contributed = false) + members without records
        $nonContributors = collect();
        
        // Add members with contribution records who haven't contributed
        foreach ($contributions->where('has_contributed', false) as $contribution) {
            $nonContributors->push($contribution);
        }
        
        // Add members without contribution records (they haven't contributed by default)
        $membersWithoutRecordsList = Member::whereIn('id', $membersWithoutRecords)
            ->with('community')
            ->get();
        
        foreach ($membersWithoutRecordsList as $member) {
            // Create a pseudo-contribution object for display
            $nonContributors->push((object)[
                'member' => $member,
                'member_id' => $member->id,
                'has_contributed' => false,
            ]);
        }
        
        \Log::info('Bereavement show statistics', [
            'event_id' => $bereavement->id,
            'total_contributions' => $totalContributions,
            'total_contributors' => $totalContributors,
            'total_members' => $totalMembers,
            'contributions_count' => $contributions->count(),
            'campus_member_ids_count' => $campusMemberIds->count(),
            'available_members_count' => $availableMembers->count()
        ]);
        
        return view('evangelism-leader.bereavement.show', compact('bereavement', 'contributions', 'totalContributions', 'totalContributors', 'totalMembers', 'campus', 'availableMembers', 'nonContributors'));
    }

    /**
     * Show the form for editing a bereavement event
     */
    public function bereavementEdit(BereavementEvent $bereavement)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the bereavement event belongs to this campus
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        $campusCommunityIds = Community::where('campus_id', $campus->id)->pluck('id');
        
        $hasCampusContributions = $bereavement->contributions()
            ->whereIn('member_id', $campusMemberIds)
            ->exists();
        
        $belongsToCampusCommunity = $bereavement->community_id && 
            $campusCommunityIds->contains($bereavement->community_id);
        
        $createdByThisLeader = $bereavement->created_by === $user->id;
        
        if (!$hasCampusContributions && !$belongsToCampusCommunity && !$createdByThisLeader) {
            abort(403, 'Unauthorized access to this bereavement event.');
        }

        if ($bereavement->isClosed()) {
            return redirect()->route('evangelism-leader.bereavement.show', $bereavement->id)
                ->with('error', 'Cannot edit a closed bereavement event.');
        }

        // Get communities from this campus
        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('evangelism-leader.bereavement.edit', compact('bereavement', 'campus', 'communities'));
    }

    /**
     * Update a bereavement event
     */
    public function bereavementUpdate(Request $request, BereavementEvent $bereavement)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the bereavement event belongs to this campus
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        $campusCommunityIds = Community::where('campus_id', $campus->id)->pluck('id');
        
        $hasCampusContributions = $bereavement->contributions()
            ->whereIn('member_id', $campusMemberIds)
            ->exists();
        
        $belongsToCampusCommunity = $bereavement->community_id && 
            $campusCommunityIds->contains($bereavement->community_id);
        
        $createdByThisLeader = $bereavement->created_by === $user->id;
        
        if (!$hasCampusContributions && !$belongsToCampusCommunity && !$createdByThisLeader) {
            abort(403, 'Unauthorized access to this bereavement event.');
        }

        if ($bereavement->isClosed()) {
            return redirect()->back()
                ->with('error', 'Cannot update a closed bereavement event.')
                ->withInput();
        }

        try {
            $validated = $request->validate([
                'deceased_name' => 'required|string|max:255',
                'family_details' => 'nullable|string',
                'related_departments' => 'nullable|string',
                'incident_date' => 'required|date',
                'contribution_start_date' => 'required|date',
                'contribution_end_date' => 'required|date|after:contribution_start_date',
                'notes' => 'nullable|string',
                'community_id' => 'nullable|exists:communities,id',
            ]);

            // Handle community_id - convert empty string to null
            $communityId = $request->input('community_id');
            if ($communityId === '' || $communityId === null) {
                $validated['community_id'] = null;
            } else {
                // Verify community belongs to this campus
                $community = Community::find($communityId);
                if (!$community) {
                    return redirect()->back()
                        ->withErrors(['community_id' => 'Selected community not found.'])
                        ->withInput();
                }
                if ($community->campus_id !== $campus->id) {
                    return redirect()->back()
                        ->withErrors(['community_id' => 'Selected community does not belong to your campus.'])
                        ->withInput();
                }
                $validated['community_id'] = $communityId;
            }

            $bereavement->update($validated);

            return redirect()->route('evangelism-leader.bereavement.show', $bereavement->id)
                ->with('success', 'Bereavement event updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to update bereavement event', [
                'error' => $e->getMessage(),
                'event_id' => $bereavement->id
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update bereavement event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Close a bereavement event
     */
    public function bereavementClose(Request $request, BereavementEvent $bereavement)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the bereavement event belongs to this campus
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        $campusCommunityIds = Community::where('campus_id', $campus->id)->pluck('id');
        
        $hasCampusContributions = $bereavement->contributions()
            ->whereIn('member_id', $campusMemberIds)
            ->exists();
        
        $belongsToCampusCommunity = $bereavement->community_id && 
            $campusCommunityIds->contains($bereavement->community_id);
        
        $createdByThisLeader = $bereavement->created_by === $user->id;
        
        if (!$hasCampusContributions && !$belongsToCampusCommunity && !$createdByThisLeader) {
            abort(403, 'Unauthorized access to this bereavement event.');
        }

        if ($bereavement->isClosed()) {
            return redirect()->back()
                ->with('error', 'Event is already closed.');
        }

        try {
            $bereavement->close();

            return redirect()->route('evangelism-leader.bereavement.show', $bereavement->id)
                ->with('success', 'Bereavement event closed successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to close bereavement event', [
                'error' => $e->getMessage(),
                'event_id' => $bereavement->id
            ]);
            return redirect()->back()
                ->with('error', 'Failed to close bereavement event: ' . $e->getMessage());
        }
    }

    /**
     * Record a contribution for a member
     */
    public function bereavementRecordContribution(Request $request, BereavementEvent $bereavement)
    {
        $this->checkEvangelismLeaderPermission();
        
        $user = auth()->user();
        $campus = $user->getCampus();
        
        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Verify the bereavement event belongs to this campus
        $campusMemberIds = Member::where('campus_id', $campus->id)->pluck('id');
        $campusCommunityIds = Community::where('campus_id', $campus->id)->pluck('id');
        
        $hasCampusContributions = $bereavement->contributions()
            ->whereIn('member_id', $campusMemberIds)
            ->exists();
        
        $belongsToCampusCommunity = $bereavement->community_id && 
            $campusCommunityIds->contains($bereavement->community_id);
        
        $createdByThisLeader = $bereavement->created_by === $user->id;
        
        if (!$hasCampusContributions && !$belongsToCampusCommunity && !$createdByThisLeader) {
            abort(403, 'Unauthorized access to this bereavement event.');
        }

        if ($bereavement->isClosed()) {
            return redirect()->back()
                ->with('error', 'Cannot record contributions for a closed bereavement event.');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'contribution_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,mobile_money,bank_transfer,other',
            'notes' => 'nullable|string',
        ]);

        // Verify member belongs to this campus
        $member = Member::findOrFail($validated['member_id']);
        if ($member->campus_id !== $campus->id) {
            return redirect()->back()
                ->with('error', 'Member does not belong to your campus.');
        }

        // Use updateOrCreate to handle both existing and new contribution records
        $contribution = BereavementContribution::updateOrCreate(
            [
                'bereavement_event_id' => $bereavement->id,
                'member_id' => $validated['member_id'],
            ],
            [
                'has_contributed' => true,
                'contribution_amount' => $validated['amount'],
                'contribution_date' => $validated['contribution_date'],
                'contribution_type' => 'individual',
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => $user->id,
            ]
        );

        return redirect()->back()
            ->with('success', 'Contribution recorded successfully.');
    }

    /**
     * Send notification to pastors about a new bereavement event
     */
    private function sendBereavementNotificationToPastors(BereavementEvent $event, $campus)
    {
        try {
            // Get all pastors (users with pastor role or can_approve_finances)
            $pastors = \App\Models\User::where(function($query) {
                $query->where('role', 'pastor')
                    ->orWhere('can_approve_finances', true)
                    ->orWhere('role', 'admin');
            })->get();

            if ($pastors->isEmpty()) {
                \Log::warning('No pastors found to send bereavement notification', [
                    'event_id' => $event->id,
                    'campus_id' => $campus->id
                ]);
                return;
            }

            // Send notification to each pastor
            // Create database notification directly to ensure it's saved immediately (bypass queue)
            foreach ($pastors as $pastor) {
                try {
                    // Create database notification directly
                    $pastor->notifications()->create([
                        'id' => \Illuminate\Support\Str::uuid()->toString(),
                        'type' => 'App\Notifications\BereavementNotification',
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $pastor->id,
                        'data' => json_encode([
                            'type' => 'bereavement_created',
                            'bereavement_event_id' => $event->id,
                            'deceased_name' => $event->deceased_name,
                            'incident_date' => $event->incident_date->format('Y-m-d'),
                            'contribution_start_date' => $event->contribution_start_date->format('Y-m-d'),
                            'contribution_end_date' => $event->contribution_end_date->format('Y-m-d'),
                            'days_remaining' => $event->days_remaining,
                            'campus_name' => $campus->name,
                            'campus_id' => $campus->id,
                            'created_by' => auth()->user()->name,
                            'message' => "A new bereavement event has been created for {$event->deceased_name} in {$campus->name} branch.",
                        ]),
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Also send via notification system (for email, etc.) - this will be queued
                    try {
                        $pastor->notify(new \App\Notifications\BereavementNotification($event, 'created'));
                    } catch (\Exception $e) {
                        \Log::warning("Failed to queue bereavement notification email for pastor {$pastor->id}: " . $e->getMessage());
                    }
                    
                    \Log::info("Bereavement notification sent to pastor", [
                        'pastor_id' => $pastor->id,
                        'pastor_name' => $pastor->name,
                        'event_id' => $event->id,
                        'campus_id' => $campus->id
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Failed to send bereavement notification to pastor {$pastor->id}: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send bereavement notification to pastors: ' . $e->getMessage());
        }
    }

    /**
     * Show finance management page
     */
    public function financeIndex()
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get pending offerings (not yet submitted to secretary)
        $pendingOfferings = Offering::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('submitted_to_secretary', false)
            ->with('member')
            ->orderBy('offering_date', 'desc')
            ->get();

        $pendingOfferingsTotal = $pendingOfferings->sum('amount');

        // Get pending tithes (not yet submitted to secretary)
        $pendingTithes = Tithe::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('submitted_to_secretary', false)
            ->with('member')
            ->orderBy('tithe_date', 'desc')
            ->get();

        $pendingTithesTotal = $pendingTithes->sum('amount');

        // Get submitted records (sent to secretary, awaiting approval)
        $submittedOfferings = Offering::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('submitted_to_secretary', true)
            ->where('approval_status', 'pending')
            ->with('member')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $submittedTithes = Tithe::where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('submitted_to_secretary', true)
            ->where('approval_status', 'pending')
            ->with('member')
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Get members for dropdown
        $members = Member::where('campus_id', $campus->id)
            ->orderBy('full_name')
            ->get();

        return view('evangelism-leader.finance.index', compact(
            'campus',
            'pendingOfferings',
            'pendingOfferingsTotal',
            'pendingTithes',
            'pendingTithesTotal',
            'submittedOfferings',
            'submittedTithes',
            'members'
        ));
    }

    /**
     * Store individual offering
     */
    public function storeOffering(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus) {
            return redirect()->back()->with('error', 'Campus not found.');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'offering_date' => 'required|date',
            'offering_type' => 'required|string|in:general,special,thanksgiving,building_fund,other',
            'payment_method' => 'required|string|in:cash,check,bank_transfer,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify member belongs to this campus
        $member = Member::findOrFail($validated['member_id']);
        if ($member->campus_id !== $campus->id) {
            return redirect()->back()->with('error', 'Member does not belong to your campus.');
        }

        $offering = Offering::create([
            'member_id' => $validated['member_id'],
            'campus_id' => $campus->id,
            'evangelism_leader_id' => $user->id,
            'amount' => $validated['amount'],
            'offering_date' => $validated['offering_date'],
            'offering_type' => $validated['offering_type'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => $user->name,
            'approval_status' => 'pending',
            'submitted_to_secretary' => false,
        ]);

            return redirect()->route('evangelism-leader.finance.index')
                ->with('success', 'Offering recorded successfully. Submit to secretary when ready.');
    }

    /**
     * Show offering details
     */
    public function showOffering(Offering $offering)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus || $offering->campus_id !== $campus->id || $offering->evangelism_leader_id !== $user->id) {
            abort(403, 'Unauthorized access to this offering.');
        }

        $offering->load('member', 'campus');
        return view('evangelism-leader.finance.offerings.show', compact('offering', 'campus'));
    }

    /**
     * Show edit offering form
     */
    public function editOffering(Offering $offering)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus || $offering->campus_id !== $campus->id || $offering->evangelism_leader_id !== $user->id) {
            abort(403, 'Unauthorized access to this offering.');
        }

        if ($offering->submitted_to_secretary) {
            return redirect()->route('evangelism-leader.finance.index')
                ->with('error', 'Cannot edit an offering that has already been submitted to secretary.');
        }

        $members = Member::where('campus_id', $campus->id)->orderBy('full_name')->get();
        return view('evangelism-leader.finance.offerings.edit', compact('offering', 'members', 'campus'));
    }

    /**
     * Update offering
     */
    public function updateOffering(Request $request, Offering $offering)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus || $offering->campus_id !== $campus->id || $offering->evangelism_leader_id !== $user->id) {
            abort(403, 'Unauthorized access to this offering.');
        }

        if ($offering->submitted_to_secretary) {
            return redirect()->back()->with('error', 'Cannot update an offering that has already been submitted to secretary.');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'offering_date' => 'required|date',
            'offering_type' => 'required|string|in:general,special,thanksgiving,building_fund,other',
            'payment_method' => 'required|string|in:cash,check,bank_transfer,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify member belongs to this campus
        $member = Member::findOrFail($validated['member_id']);
        if ($member->campus_id !== $campus->id) {
            return redirect()->back()->with('error', 'Member does not belong to your campus.');
        }

        $offering->update($validated);

        return redirect()->route('evangelism-leader.finance.index')
            ->with('success', 'Offering updated successfully.');
    }

    /**
     * Store aggregate tithe (from all members - single record, no individual tracking)
     */
    public function storeTithe(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus) {
            return redirect()->back()->with('error', 'Campus not found.');
        }

        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'tithe_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,check,bank_transfer,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Create a single aggregate tithe record (no member_id - represents collection from all members)
            $tithe = Tithe::create([
                'member_id' => null, // No specific member - aggregate collection
                'campus_id' => $campus->id,
                'evangelism_leader_id' => $user->id,
                'amount' => $validated['total_amount'],
                'tithe_date' => $validated['tithe_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => $user->name,
                'approval_status' => 'pending',
                'is_aggregate' => true,
                'submitted_to_secretary' => false,
            ]);

            return redirect()->route('evangelism-leader.finance.index')
                ->with('success', 'Aggregate tithe recorded successfully (TZS ' . number_format($validated['total_amount'], 2) . '). Submit to secretary when ready.');
        } catch (\Exception $e) {
            Log::error('Failed to store aggregate tithe: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to record tithe: ' . $e->getMessage());
        }
    }

    /**
     * Show tithe details
     */
    public function showTithe(Tithe $tithe)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus || $tithe->campus_id !== $campus->id || $tithe->evangelism_leader_id !== $user->id) {
            abort(403, 'Unauthorized access to this tithe.');
        }

        $tithe->load('member', 'campus');
        return view('evangelism-leader.finance.tithes.show', compact('tithe', 'campus'));
    }

    /**
     * Show edit tithe form
     */
    public function editTithe(Tithe $tithe)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus || $tithe->campus_id !== $campus->id || $tithe->evangelism_leader_id !== $user->id) {
            abort(403, 'Unauthorized access to this tithe.');
        }

        if ($tithe->submitted_to_secretary) {
            return redirect()->route('evangelism-leader.finance.index')
                ->with('error', 'Cannot edit a tithe that has already been submitted to secretary.');
        }

        return view('evangelism-leader.finance.tithes.edit', compact('tithe', 'campus'));
    }

    /**
     * Update tithe
     */
    public function updateTithe(Request $request, Tithe $tithe)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus || $tithe->campus_id !== $campus->id || $tithe->evangelism_leader_id !== $user->id) {
            abort(403, 'Unauthorized access to this tithe.');
        }

        if ($tithe->submitted_to_secretary) {
            return redirect()->back()->with('error', 'Cannot update a tithe that has already been submitted to secretary.');
        }

        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'tithe_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,check,bank_transfer,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $tithe->update([
            'amount' => $validated['total_amount'],
            'tithe_date' => $validated['tithe_date'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('evangelism-leader.finance.index')
            ->with('success', 'Tithe updated successfully.');
    }

    /**
     * Submit offerings to secretary
     */
    public function submitOfferings(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus) {
            return redirect()->back()->with('error', 'Campus not found.');
        }

        $validated = $request->validate([
            'offering_ids' => 'required|array|min:1',
            'offering_ids.*' => 'exists:offerings,id',
        ]);

        // Verify all offerings belong to this leader and campus
        $offerings = Offering::whereIn('id', $validated['offering_ids'])
            ->where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('submitted_to_secretary', false)
            ->get();

        if ($offerings->count() !== count($validated['offering_ids'])) {
            return redirect()->back()->with('error', 'Some offerings are invalid or already submitted.');
        }

        $offerings->each(function($offering) {
            $offering->update([
                'submitted_to_secretary' => true,
                'submitted_at' => now(),
                'approval_status' => 'pending',
            ]);
        });

        // Send notification to secretary
        $this->sendFinancialSubmissionNotification('offerings', $offerings->count(), $offerings->sum('amount'));

        return redirect()->route('evangelism-leader.finance.index')
            ->with('success', $offerings->count() . ' offering(s) submitted to secretary successfully.');
    }

    /**
     * Submit tithes to secretary
     */
    public function submitTithes(Request $request)
    {
        $this->checkEvangelismLeaderPermission();
        $user = auth()->user();
        $campus = $user->getCampus();

        if (!$campus) {
            return redirect()->back()->with('error', 'Campus not found.');
        }

        $validated = $request->validate([
            'tithe_ids' => 'required|array|min:1',
            'tithe_ids.*' => 'exists:tithes,id',
        ]);

        // Verify all tithes belong to this leader and campus
        $tithes = Tithe::whereIn('id', $validated['tithe_ids'])
            ->where('campus_id', $campus->id)
            ->where('evangelism_leader_id', $user->id)
            ->where('submitted_to_secretary', false)
            ->get();

        if ($tithes->count() !== count($validated['tithe_ids'])) {
            return redirect()->back()->with('error', 'Some tithes are invalid or already submitted.');
        }

        $tithes->each(function($tithe) {
            $tithe->update([
                'submitted_to_secretary' => true,
                'submitted_at' => now(),
                'approval_status' => 'pending',
            ]);
        });

        // Send notification to secretary
        $this->sendFinancialSubmissionNotification('tithes', $tithes->count(), $tithes->sum('amount'));

        return redirect()->route('evangelism-leader.finance.index')
            ->with('success', $tithes->count() . ' tithe record(s) submitted to secretary successfully.');
    }

    /**
     * Send notification to secretary about financial submission
     */
    private function sendFinancialSubmissionNotification($type, $count, $totalAmount)
    {
        try {
            $secretaries = \App\Models\User::where('role', 'secretary')
                ->orWhere('role', 'admin')
                ->get();

            foreach ($secretaries as $secretary) {
                $secretary->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'App\Notifications\FinancialApprovalNotification',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $secretary->id,
                    'data' => json_encode([
                        'type' => 'financial_submission',
                        'submission_type' => $type,
                        'count' => $count,
                        'total_amount' => $totalAmount,
                        'campus_name' => auth()->user()->getCampus()->name ?? 'Unknown',
                        'submitted_by' => auth()->user()->name,
                        'message' => "New {$type} submission from " . (auth()->user()->getCampus()->name ?? 'Evangelism Leader') . ": {$count} record(s) totaling TZS " . number_format($totalAmount, 2),
                        'created_at' => now()->toDateTimeString(),
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::info("Financial submission notification sent to secretaries", [
                'type' => $type,
                'count' => $count,
                'total_amount' => $totalAmount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send financial submission notification: ' . $e->getMessage());
        }
    }
}
