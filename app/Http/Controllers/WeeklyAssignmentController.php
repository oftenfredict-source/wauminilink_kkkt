<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyAssignment;
use App\Models\Leader;
use Illuminate\Support\Facades\Validator;
use App\Services\SmsService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Log;

class WeeklyAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WeeklyAssignment::with(['leader.member', 'assignedBy']);

        // Filter by position if provided
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filter by status
        $status = $request->get('status', 'active');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'current') {
            $query->currentWeek()->where('is_active', true);
        } elseif ($status === 'past') {
            $query->where('week_end_date', '<', now()->toDateString())
                  ->where('is_active', true);
        } elseif ($status === 'upcoming') {
            $query->where('week_start_date', '>', now()->toDateString())
                  ->where('is_active', true);
        } elseif ($status === 'all') {
            // Show all assignments (active and inactive)
            // No additional filter
        }

        // Filter by date range (overrides status filters)
        if ($request->filled('from')) {
            $query->where('week_end_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->where('week_start_date', '<=', $request->date('to'));
        }

        $assignments = $query->orderBy('week_start_date', 'desc')
                            ->orderBy('position')
                            ->paginate(15);

        // Get active leaders grouped by position for filters
        $leadersByPosition = Leader::with('member')
            ->active()
            ->get()
            ->groupBy('position');

        $positions = [
            'pastor' => 'Mchungaji',
            'assistant_pastor' => 'Msaidizi wa Mchungaji',
            'secretary' => 'Katibu',
            'assistant_secretary' => 'Msaidizi wa Katibu',
            'treasurer' => 'Mweka Hazina',
            'assistant_treasurer' => 'Msaidizi wa Mweka Hazina',
            'elder' => 'Mzee wa Kanisa',
            'deacon' => 'Shamashi',
            'deaconess' => 'Shamasha',
            'youth_leader' => 'Kiongozi wa Vijana',
            'children_leader' => 'Kiongozi wa Watoto',
            'worship_leader' => 'Kiongozi wa Ibada',
            'choir_leader' => 'Kiongozi wa Kwaya',
            'usher_leader' => 'Kiongozi wa Wakaribishaji',
            'evangelism_leader' => 'Kiongozi wa Uinjilisti',
            'prayer_leader' => 'Kiongozi wa Maombi',
            'other' => 'Kiongozi'
        ];

        return view('leaders.weekly-assignments.index', compact('assignments', 'leadersByPosition', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active leaders grouped by position
        $leadersByPosition = Leader::with('member')
            ->active()
            ->get()
            ->groupBy('position');

        $positions = [
            'pastor' => 'Mchungaji',
            'assistant_pastor' => 'Msaidizi wa Mchungaji',
            'secretary' => 'Katibu',
            'assistant_secretary' => 'Msaidizi wa Katibu',
            'treasurer' => 'Mweka Hazina',
            'assistant_treasurer' => 'Msaidizi wa Mweka Hazina',
            'elder' => 'Mzee wa Kanisa',
            'deacon' => 'Shamashi',
            'deaconess' => 'Shamasha',
            'youth_leader' => 'Kiongozi wa Vijana',
            'children_leader' => 'Kiongozi wa Watoto',
            'worship_leader' => 'Kiongozi wa Ibada',
            'choir_leader' => 'Kiongozi wa Kwaya',
            'usher_leader' => 'Kiongozi wa Wakaribishaji',
            'evangelism_leader' => 'Kiongozi wa Uinjilisti',
            'prayer_leader' => 'Kiongozi wa Maombi',
            'other' => 'Kiongozi'
        ];

        return view('leaders.weekly-assignments.create', compact('leadersByPosition', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leader_id' => 'required|exists:leaders,id',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'position' => 'required|string|in:pastor,assistant_pastor,secretary,assistant_secretary,treasurer,assistant_treasurer,elder,deacon,deaconess,youth_leader,children_leader,worship_leader,choir_leader,usher_leader,evangelism_leader,prayer_leader,other',
            'duties' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for overlapping assignments
        $overlapping = WeeklyAssignment::where('leader_id', $request->leader_id)
            ->where('is_active', true)
            ->where(function($q) use ($request) {
                $q->whereBetween('week_start_date', [$request->week_start_date, $request->week_end_date])
                  ->orWhereBetween('week_end_date', [$request->week_start_date, $request->week_end_date])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('week_start_date', '<=', $request->week_start_date)
                         ->where('week_end_date', '>=', $request->week_end_date);
                  });
            })
            ->first();

        if ($overlapping) {
            return redirect()->back()
                ->withErrors(['week_start_date' => 'This leader already has an assignment for this week period.'])
                ->withInput();
        }

        $assignment = WeeklyAssignment::create([
            'leader_id' => $request->leader_id,
            'week_start_date' => $request->week_start_date,
            'week_end_date' => $request->week_end_date,
            'position' => $request->position,
            'duties' => $request->duties,
            'notes' => $request->notes,
            'assigned_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Load relationships before sending SMS
        $assignment->load(['leader.member']);

        // Send SMS notification to the assigned leader (only on create)
        $this->sendAssignmentSms($assignment);

        return redirect()->route('weekly-assignments.index')
            ->with('success', 'Weekly assignment created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WeeklyAssignment $weeklyAssignment)
    {
        $weeklyAssignment->load(['leader.member', 'assignedBy']);
        return view('leaders.weekly-assignments.show', compact('weeklyAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WeeklyAssignment $weeklyAssignment)
    {
        $leadersByPosition = Leader::with('member')
            ->active()
            ->get()
            ->groupBy('position');

        $positions = [
            'pastor' => 'Mchungaji',
            'assistant_pastor' => 'Msaidizi wa Mchungaji',
            'secretary' => 'Katibu',
            'assistant_secretary' => 'Msaidizi wa Katibu',
            'treasurer' => 'Mweka Hazina',
            'assistant_treasurer' => 'Msaidizi wa Mweka Hazina',
            'elder' => 'Mzee wa Kanisa',
            'deacon' => 'Shamashi',
            'deaconess' => 'Shamasha',
            'youth_leader' => 'Kiongozi wa Vijana',
            'children_leader' => 'Kiongozi wa Watoto',
            'worship_leader' => 'Kiongozi wa Ibada',
            'choir_leader' => 'Kiongozi wa Kwaya',
            'usher_leader' => 'Kiongozi wa Wakaribishaji',
            'evangelism_leader' => 'Kiongozi wa Uinjilisti',
            'prayer_leader' => 'Kiongozi wa Maombi',
            'other' => 'Kiongozi'
        ];

        return view('leaders.weekly-assignments.edit', compact('weeklyAssignment', 'leadersByPosition', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WeeklyAssignment $weeklyAssignment)
    {
        $validator = Validator::make($request->all(), [
            'leader_id' => 'required|exists:leaders,id',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'position' => 'required|string|in:pastor,assistant_pastor,secretary,assistant_secretary,treasurer,assistant_treasurer,elder,deacon,deaconess,youth_leader,children_leader,worship_leader,choir_leader,usher_leader,evangelism_leader,prayer_leader,other',
            'duties' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for overlapping assignments (excluding current one)
        $overlapping = WeeklyAssignment::where('leader_id', $request->leader_id)
            ->where('id', '!=', $weeklyAssignment->id)
            ->where('is_active', true)
            ->where(function($q) use ($request) {
                $q->whereBetween('week_start_date', [$request->week_start_date, $request->week_end_date])
                  ->orWhereBetween('week_end_date', [$request->week_start_date, $request->week_end_date])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('week_start_date', '<=', $request->week_start_date)
                         ->where('week_end_date', '>=', $request->week_end_date);
                  });
            })
            ->first();

        if ($overlapping) {
            return redirect()->back()
                ->withErrors(['week_start_date' => 'This leader already has an assignment for this week period.'])
                ->withInput();
        }

        // Store original values to check if important fields changed
        $originalLeaderId = $weeklyAssignment->leader_id;
        $originalStartDate = $weeklyAssignment->week_start_date->format('Y-m-d');
        $originalEndDate = $weeklyAssignment->week_end_date->format('Y-m-d');
        $wasActive = $weeklyAssignment->is_active;

        $weeklyAssignment->update($request->all());

        // Reload to get updated relationships
        $weeklyAssignment->refresh();
        $weeklyAssignment->load(['leader.member']);

        // Only send SMS if:
        // 1. Assignment is active
        // 2. AND (leader changed OR dates changed OR was just activated)
        $leaderChanged = $originalLeaderId != $weeklyAssignment->leader_id;
        $datesChanged = $originalStartDate != $weeklyAssignment->week_start_date->format('Y-m-d') 
                    || $originalEndDate != $weeklyAssignment->week_end_date->format('Y-m-d');
        $justActivated = !$wasActive && $weeklyAssignment->is_active;

        if ($weeklyAssignment->is_active && ($leaderChanged || $datesChanged || $justActivated)) {
            $this->sendAssignmentSms($weeklyAssignment);
        }

        return redirect()->route('weekly-assignments.index')
            ->with('success', 'Weekly assignment updated successfully!');
    }

    /**
     * Track SMS sends to prevent duplicates in the same request
     */
    private static $smsSentForAssignments = [];

    /**
     * Send SMS notification for weekly assignment
     */
    private function sendAssignmentSms(WeeklyAssignment $assignment)
    {
        try {
            // Prevent duplicate SMS in the same request
            $assignmentId = $assignment->id ?? 'new';
            if (isset(self::$smsSentForAssignments[$assignmentId])) {
                Log::info('SMS already sent for assignment in this request, skipping duplicate', [
                    'assignment_id' => $assignmentId
                ]);
                return false;
            }

            // Check if SMS notifications are enabled
            if (!SettingsService::get('enable_sms_notifications', false)) {
                Log::info('SMS notifications disabled, skipping weekly assignment notification');
                return false;
            }

            // Load leader with member relationship
            if (!$assignment->relationLoaded('leader')) {
                $assignment->load(['leader.member']);
            }
            
            // Check if leader and member exist
            if (!$assignment->leader || !$assignment->leader->member) {
                Log::warning('Leader or member not found for assignment ID: ' . $assignment->id);
                return false;
            }

            $member = $assignment->leader->member;

            // Check if member has a phone number
            if (empty($member->phone_number)) {
                Log::info('Member has no phone number, skipping SMS notification for assignment: ' . $assignment->id);
                return false;
            }

            // Get church name from settings
            $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');

            // Format dates
            $startDate = $assignment->week_start_date->format('Y-m-d');
            $endDate = $assignment->week_end_date->format('Y-m-d');

            // Send SMS
            $smsService = app(SmsService::class);
            $resp = $smsService->sendWeeklyAssignmentNotificationDebug(
                $member->phone_number,
                $member->full_name,
                $startDate,
                $endDate,
                $churchName
            );

            // Mark as sent to prevent duplicates in the same request
            self::$smsSentForAssignments[$assignment->id] = true;

            Log::info('Weekly assignment SMS sent', [
                'assignment_id' => $assignment->id,
                'leader_name' => $member->full_name,
                'phone' => $member->phone_number,
                'week' => $startDate . ' to ' . $endDate,
                'ok' => $resp['ok'] ?? null,
                'status' => $resp['status'] ?? null,
                'body' => $resp['body'] ?? null,
                'reason' => $resp['reason'] ?? null,
                'error' => $resp['error'] ?? null,
            ]);

            return $resp['ok'] ?? false;
        } catch (\Exception $e) {
            Log::error('Failed to send weekly assignment SMS: ' . $e->getMessage(), [
                'assignment_id' => $assignment->id,
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WeeklyAssignment $weeklyAssignment)
    {
        $weeklyAssignment->delete();
        return redirect()->route('weekly-assignments.index')
            ->with('success', 'Weekly assignment deleted successfully!');
    }
}
