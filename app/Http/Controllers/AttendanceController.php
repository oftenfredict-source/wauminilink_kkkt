<?php

namespace App\Http\Controllers;

use App\Models\ServiceAttendance;
use App\Models\SundayService;
use App\Models\SpecialEvent;
use App\Models\Member;
use App\Models\Child;
use App\Models\Offering;
use App\Services\SmsService;
use App\Notifications\MissedAttendanceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    /**
     * Display attendance recording page
     */
    public function index(Request $request)
    {
        $serviceType = $request->get('service_type', 'sunday_service');
        $serviceId = $request->get('service_id');
        
        // Get available services based on type
        // Show all Sunday Services for both Main Service and Children Service attendance
        // The service_type filter in attendance determines which children appear, not which services
        if ($serviceType === 'sunday_service' || $serviceType === 'children_service') {
            // Get all Sunday Services (they can be used for both main and children attendance)
            $services = SundayService::orderBy('service_date', 'desc')->get();
        } else {
            $services = SpecialEvent::orderBy('event_date', 'desc')->get();
        }
        
        // Get selected service
        $selectedService = null;
        if ($serviceId) {
            if ($serviceType === 'sunday_service' || $serviceType === 'children_service') {
                $selectedService = SundayService::find($serviceId);
            } else {
                $selectedService = SpecialEvent::find($serviceId);
            }
        }
        
        // Get all members
        $members = Member::orderBy('full_name')->get();
        
        // Get children based on attendance service type (not the service's own type)
        // For main service attendance: show only teenagers (13-17)
        // For children service attendance: show only Sunday School children (3-12)
        $children = Child::with('member')
            ->get()
            ->filter(function ($child) use ($serviceType) {
                if (!$child->shouldRecordAttendance()) {
                    return false; // Skip infants and adults
                }
                
                // Filter based on the attendance service type selected, not the service's own type
                if ($serviceType === 'sunday_service') {
                    // Main service attendance: show only teenagers (13-17)
                    return $child->shouldAttendMainService();
                } elseif ($serviceType === 'children_service') {
                    // Children service attendance: show only Sunday School children (3-12)
                    return $child->shouldAttendSundaySchool();
                }
                
                // Default: show all children who should record attendance (3-17)
                return $child->shouldRecordAttendance();
            })
            ->sortBy('full_name')
            ->values();
        
        // Get attendance records for selected service
        $attendanceRecords = collect();
        $childAttendanceRecords = collect();
        $existingOfferingAmount = null;
        
        if ($selectedService) {
            $attendanceRecords = ServiceAttendance::forService($serviceType, $serviceId)
                ->membersOnly()
                ->with('member')
                ->get()
                ->keyBy('member_id');
            
            $childAttendanceRecords = ServiceAttendance::forService($serviceType, $serviceId)
                ->childrenOnly()
                ->with('child')
                ->get()
                ->keyBy('child_id');
            
            // Get existing offering amount for children service
            if ($serviceType === 'children_service') {
                $existingOffering = Offering::where('service_id', $serviceId)
                    ->where('service_type', 'children_service')
                    ->first();
                
                if ($existingOffering) {
                    $existingOfferingAmount = $existingOffering->amount;
                } elseif (isset($selectedService->offerings_amount) && $selectedService->offerings_amount > 0) {
                    $existingOfferingAmount = $selectedService->offerings_amount;
                }
            }
        }
        
        return view('attendance.index', compact(
            'serviceType', 
            'serviceId', 
            'services', 
            'selectedService', 
            'members', 
            'children',
            'attendanceRecords',
            'childAttendanceRecords',
            'existingOfferingAmount'
        ));
    }
    
    /**
     * Store attendance records
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:sunday_service,children_service,special_event',
            'service_id' => 'required|integer',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:members,id',
            'child_ids' => 'nullable|array',
            'child_ids.*' => 'integer|exists:children,id',
            'guests_count' => 'nullable|integer|min:0',
            'children_offering_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ], [
            'member_ids.array' => 'Member IDs must be an array.',
            'child_ids.array' => 'Child IDs must be an array.',
            'guests_count.integer' => 'Guests count must be a whole number.',
            'guests_count.min' => 'Guests count cannot be negative.',
            'children_offering_amount.numeric' => 'Offering amount must be a valid number.',
            'children_offering_amount.min' => 'Offering amount cannot be negative.',
        ]);
        
        // Ensure at least one member or child is provided
        $memberIds = $request->member_ids ?? [];
        $childIds = $request->child_ids ?? [];
        
        if (empty($memberIds) && empty($childIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one member or child to record attendance.'
            ], 422);
        }
        
        // Get the service to check start time
        $serviceType = $request->service_type;
        $serviceId = $request->service_id;
        
        if ($serviceType === 'sunday_service' || $serviceType === 'children_service') {
            $service = SundayService::findOrFail($serviceId);
            $serviceDate = $service->service_date;
            $startTime = $service->start_time;
        } else {
            $service = SpecialEvent::findOrFail($serviceId);
            $serviceDate = $service->event_date;
            $startTime = $service->start_time;
        }
        
        // Check if service has a start time
        if ($startTime) {
            try {
                // Combine service date and start time
                // start_time is stored as TIME in database, so it's a string like "09:00:00" or "09:00"
                $timeString = $startTime;
                if ($startTime instanceof \Carbon\Carbon) {
                    $timeString = $startTime->format('H:i:s');
                } elseif (is_object($startTime) && method_exists($startTime, 'format')) {
                    $timeString = $startTime->format('H:i:s');
                } elseif (is_string($startTime)) {
                    // Ensure it's in H:i:s format
                    if (strlen($startTime) === 5) {
                        $timeString = $startTime . ':00';
                    }
                }
                
                $serviceStartDateTime = \Carbon\Carbon::parse($serviceDate->format('Y-m-d') . ' ' . $timeString);
                
                // Check if current time is before service start time
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
                // If time parsing fails, log error but allow attendance (fallback)
                \Log::warning('Failed to parse service start time for attendance restriction', [
                    'service_type' => $serviceType,
                    'service_id' => $serviceId,
                    'start_time' => $startTime,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        try {
            DB::beginTransaction();
            
            $notes = $request->notes;
            $recordedBy = auth()->user()->name ?? 'System';
            
            // Remove existing attendance records for this service
            ServiceAttendance::forService($serviceType, $serviceId)->delete();
            
            // Create new attendance records for members
            $attendanceData = [];
            foreach ($memberIds as $memberId) {
                $attendanceData[] = [
                    'service_type' => $serviceType,
                    'service_id' => $serviceId,
                    'member_id' => $memberId,
                    'child_id' => null,
                    'attended_at' => now(),
                    'recorded_by' => $recordedBy,
                    'notes' => $notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Create new attendance records for children
            foreach ($childIds as $childId) {
                $child = Child::find($childId);
                if (!$child) {
                    continue;
                }
                
                // Validate that child should record attendance (ages 3-17)
                if (!$child->shouldRecordAttendance()) {
                    continue; // Skip infants (< 3) and adults (18+)
                }
                
                // Validate age-based service routing based on attendance service type
                if ($serviceType === 'sunday_service') {
                    // Main service attendance: only teenagers (13-17) should be recorded
                    if (!$child->shouldAttendMainService()) {
                        continue; // Skip children who should attend Sunday School
                    }
                } elseif ($serviceType === 'children_service') {
                    // Children service attendance: only Sunday School children (3-12) should be recorded
                    if (!$child->shouldAttendSundaySchool()) {
                        continue; // Skip teenagers who should attend main service
                    }
                }
                
                $attendanceData[] = [
                    'service_type' => $serviceType,
                    'service_id' => $serviceId,
                    'member_id' => null,
                    'child_id' => $childId,
                    'attended_at' => now(),
                    'recorded_by' => $recordedBy,
                    'notes' => $notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($attendanceData)) {
                ServiceAttendance::insert($attendanceData);
            }
            
            // Update service attendance count (members + children + guests)
            $attendanceCount = count($attendanceData);
            $guestsCount = 0;
            
            if ($serviceType === 'sunday_service' || $serviceType === 'children_service') {
                // Get guests count for main service
                if ($serviceType === 'sunday_service') {
                    $guestsCount = $request->filled('guests_count') ? (int) $request->guests_count : 0;
                }
                
                // Total attendance includes members, children, and guests
                $totalAttendance = $attendanceCount + $guestsCount;
                
                $updateData = [
                    'attendance_count' => $attendanceCount, // Members + children only
                    'guests_count' => $guestsCount
                ];
                
                // If children service offering is provided, update the service offerings_amount
                if ($serviceType === 'children_service' && $request->filled('children_offering_amount') && $request->children_offering_amount > 0) {
                    $updateData['offerings_amount'] = $request->children_offering_amount;
                }
                
                SundayService::where('id', $serviceId)->update($updateData);
            } else {
                SpecialEvent::where('id', $serviceId)->update(['attendance_count' => $attendanceCount]);
            }
            
            // Create offering record for children service if offering amount is provided
            if ($serviceType === 'children_service' && $request->filled('children_offering_amount') && $request->children_offering_amount > 0) {
                // Check if there's already an offering record for this service
                $existingOffering = Offering::where('service_id', $serviceId)
                    ->where('service_type', 'children_service')
                    ->first();
                
                if ($existingOffering) {
                    // Update existing offering
                    $existingOffering->update([
                        'amount' => $request->children_offering_amount,
                        'notes' => 'Children Service (Sunday School) Offering - ' . ($service->theme ?? 'General Service'),
                        'approval_status' => 'pending', // Reset to pending for re-approval
                        'is_verified' => false
                    ]);
                } else {
                    // Create new offering record
                    $offering = Offering::create([
                        'member_id' => null, // General offering from Children Service
                        'amount' => $request->children_offering_amount,
                        'offering_date' => $serviceDate,
                        'offering_type' => 'general',
                        'service_type' => 'children_service',
                        'service_id' => $serviceId,
                        'payment_method' => 'cash',
                        'reference_number' => 'CS-' . $serviceId . '-' . time(),
                        'notes' => 'Children Service (Sunday School) Offering - ' . ($service->theme ?? 'General Service'),
                        'recorded_by' => $recordedBy,
                        'approval_status' => 'pending',
                        'is_verified' => false
                    ]);
                    
                    // Send notification to pastors about pending offering
                    $this->sendFinancialApprovalNotification('offering', $offering);
                }
            }
            
            DB::commit();
            
            $memberCount = count($memberIds);
            $childCount = count($childIds);
            $guestsCount = ($serviceType === 'sunday_service' && $request->filled('guests_count')) ? (int) $request->guests_count : 0;
            $totalAttendance = $attendanceCount + $guestsCount;
            
            $message = "Attendance recorded successfully for ";
            $parts = [];
            if ($memberCount > 0) {
                $parts[] = "{$memberCount} member(s)";
            }
            if ($childCount > 0) {
                $parts[] = "{$childCount} child(ren)";
            }
            if ($guestsCount > 0) {
                $parts[] = "{$guestsCount} guest(s)";
            }
            $message .= implode(', ', $parts);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'attendance_count' => $attendanceCount, // Members + children
                'guests_count' => $guestsCount,
                'total_attendance' => $totalAttendance, // Members + children + guests
                'member_count' => $memberCount,
                'child_count' => $childCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get attendance history for a member
     */
    public function memberHistory(Request $request, $memberId)
    {
        $member = Member::findOrFail($memberId);
        
        $query = ServiceAttendance::byMember($memberId)
            ->with(['sundayService', 'specialEvent'])
            ->orderBy('attended_at', 'desc');
            
        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('attended_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('attended_at', '<=', $request->date('to'));
        }
        
        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        
        $attendances = $query->paginate(20);
        $attendances->appends($request->query());
        
        // Calculate statistics
        $totalAttendances = ServiceAttendance::byMember($memberId)->count();
        $sundayAttendances = ServiceAttendance::byMember($memberId)->sundayServices()->count();
        $specialEventAttendances = ServiceAttendance::byMember($memberId)->specialEvents()->count();
        
        return view('attendance.member-history', compact(
            'member', 
            'attendances', 
            'totalAttendances', 
            'sundayAttendances', 
            'specialEventAttendances'
        ));
    }
    
    /**
     * Get service attendance report
     */
    public function serviceReport(Request $request, $serviceType, $serviceId)
    {
        if ($serviceType === 'sunday_service' || $serviceType === 'children_service') {
            $service = SundayService::findOrFail($serviceId);
        } else {
            $service = SpecialEvent::findOrFail($serviceId);
        }
        
        $attendances = ServiceAttendance::forService($serviceType, $serviceId)
            ->with(['member', 'child'])
            ->orderBy('attended_at', 'desc')
            ->get();
            
        // Get member and child statistics
        $totalMembers = Member::count();
        $totalChildren = Child::whereHas('member', function($query) {
            // Only count children who should record attendance (ages 3-17)
        })->get()->filter(function($child) {
            return $child->shouldRecordAttendance();
        })->count();
        
        $attendedMembers = $attendances->filter(function($att) {
            return $att->isMemberAttendance();
        })->count();
        $attendedChildren = $attendances->filter(function($att) {
            return $att->isChildAttendance();
        })->count();
        
        // Get guests count for main service
        $guestsCount = 0;
        if (($serviceType === 'sunday_service' || $serviceType === 'children_service') && isset($service->guests_count)) {
            $guestsCount = $service->guests_count ?? 0;
        }
        
        $totalAttendees = $attendedMembers + $attendedChildren + $guestsCount;
        $totalPotentialAttendees = $totalMembers + $totalChildren;
        $attendancePercentage = $totalPotentialAttendees > 0 ? round(($totalAttendees / $totalPotentialAttendees) * 100, 2) : 0;
        
        // Get attendance by gender (members and children)
        $attendanceByGender = collect();
        foreach ($attendances as $attendance) {
            $gender = null;
            if ($attendance->isMemberAttendance() && $attendance->member) {
                $gender = $attendance->member->gender;
            } elseif ($attendance->isChildAttendance() && $attendance->child) {
                $gender = $attendance->child->gender;
            }
            if ($gender) {
                $attendanceByGender->push($gender);
            }
        }
        $attendanceByGender = $attendanceByGender->groupBy(function($item) {
            return $item;
        })->map(function ($group) {
            return $group->count();
        });
            
        return view('attendance.service-report', compact(
            'service', 
            'serviceType', 
            'attendances', 
            'totalMembers',
            'totalChildren',
            'attendedMembers',
            'attendedChildren',
            'guestsCount',
            'totalAttendees',
            'totalPotentialAttendees',
            'attendancePercentage', 
            'attendanceByGender'
        ));
    }
    
    /**
     * Get overall attendance statistics
     */
    public function statistics(Request $request)
    {
        $query = ServiceAttendance::query();
        
        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('attended_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('attended_at', '<=', $request->date('to'));
        }
        
        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        
        // Filter by specific service/event
        if ($request->filled('service_id') && $request->filled('service_type')) {
            $query->where('service_id', $request->service_id)
                  ->where('service_type', $request->service_type);
        }
        
        // Get basic statistics
        $totalAttendances = $query->count();
        $sundayAttendances = (clone $query)->sundayServices()->count();
        $specialEventAttendances = (clone $query)->specialEvents()->count();
        
        // Get attendance by category (adult members vs children)
        $adultMemberAttendances = (clone $query)->membersOnly()->count();
        $childrenAttendances = (clone $query)->childrenOnly()->count();
        
        // Get total guests count from sunday services (respecting all filters)
        $guestsQuery = SundayService::query();
        if ($request->filled('from')) {
            $guestsQuery->whereDate('service_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $guestsQuery->whereDate('service_date', '<=', $request->date('to'));
        }
        // Filter by specific service if selected
        if ($request->filled('service_id') && $request->filled('service_type') && $request->service_type === 'sunday_service') {
            $guestsQuery->where('id', $request->service_id);
        }
        $totalGuests = $guestsQuery->sum('guests_count');
        
        // Get attendance by gender for members (optimized query)
        $maleMemberAttendances = (clone $query)
            ->whereNotNull('service_attendances.member_id')
            ->join('members', 'service_attendances.member_id', '=', 'members.id')
            ->where('members.gender', 'male')
            ->count();
        
        $femaleMemberAttendances = (clone $query)
            ->whereNotNull('service_attendances.member_id')
            ->join('members', 'service_attendances.member_id', '=', 'members.id')
            ->where('members.gender', 'female')
            ->count();
        
        // Get attendance by gender for children (optimized query)
        $maleChildAttendances = (clone $query)
            ->whereNotNull('service_attendances.child_id')
            ->join('children', 'service_attendances.child_id', '=', 'children.id')
            ->where('children.gender', 'male')
            ->count();
        
        $femaleChildAttendances = (clone $query)
            ->whereNotNull('service_attendances.child_id')
            ->join('children', 'service_attendances.child_id', '=', 'children.id')
            ->where('children.gender', 'female')
            ->count();
        
        // Get most regular attendees (members and children) - apply all filters
        $mostRegularMemberQuery = ServiceAttendance::select('member_id', DB::raw('COUNT(*) as attendance_count'))
            ->whereNotNull('member_id');
        
        if ($request->filled('from')) {
            $mostRegularMemberQuery->whereDate('attended_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $mostRegularMemberQuery->whereDate('attended_at', '<=', $request->date('to'));
        }
        if ($request->filled('service_type')) {
            $mostRegularMemberQuery->where('service_type', $request->service_type);
        }
        if ($request->filled('service_id') && $request->filled('service_type')) {
            $mostRegularMemberQuery->where('service_id', $request->service_id)
                                   ->where('service_type', $request->service_type);
        }
        
        $mostRegularMemberAttendees = $mostRegularMemberQuery
            ->groupBy('member_id')
            ->orderBy('attendance_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($att) {
                $member = Member::find($att->member_id);
                return [
                    'type' => 'member',
                    'id' => $att->member_id,
                    'name' => $member ? $member->full_name : 'Unknown Member',
                    'member_id' => $member ? $member->member_id : null,
                    'attendance_count' => $att->attendance_count
                ];
            });
        
        $mostRegularChildQuery = ServiceAttendance::select('child_id', DB::raw('COUNT(*) as attendance_count'))
            ->whereNotNull('child_id');
        
        if ($request->filled('from')) {
            $mostRegularChildQuery->whereDate('attended_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $mostRegularChildQuery->whereDate('attended_at', '<=', $request->date('to'));
        }
        if ($request->filled('service_type')) {
            $mostRegularChildQuery->where('service_type', $request->service_type);
        }
        if ($request->filled('service_id') && $request->filled('service_type')) {
            $mostRegularChildQuery->where('service_id', $request->service_id)
                                  ->where('service_type', $request->service_type);
        }
        
        $mostRegularChildAttendees = $mostRegularChildQuery
            ->groupBy('child_id')
            ->orderBy('attendance_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($att) {
                $child = Child::find($att->child_id);
                return [
                    'type' => 'child',
                    'id' => $att->child_id,
                    'name' => $child ? $child->full_name : 'Unknown Child',
                    'member_id' => null,
                    'attendance_count' => $att->attendance_count
                ];
            });
        
        // Combine and sort by attendance count
        $mostRegularAttendees = $mostRegularMemberAttendees->concat($mostRegularChildAttendees)
            ->sortByDesc('attendance_count')
            ->take(10)
            ->values();
            
        // Get attendance trends by month - apply all filters
        $monthlyTrendsQuery = ServiceAttendance::select(
                DB::raw('YEAR(attended_at) as year'),
                DB::raw('MONTH(attended_at) as month'),
                DB::raw('COUNT(*) as attendance_count')
            );
        
        if ($request->filled('from')) {
            $monthlyTrendsQuery->whereDate('attended_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $monthlyTrendsQuery->whereDate('attended_at', '<=', $request->date('to'));
        }
        if ($request->filled('service_type')) {
            $monthlyTrendsQuery->where('service_type', $request->service_type);
        }
        if ($request->filled('service_id') && $request->filled('service_type')) {
            $monthlyTrendsQuery->where('service_id', $request->service_id)
                               ->where('service_type', $request->service_type);
        }
        
        $monthlyTrends = $monthlyTrendsQuery
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Get services and events for filter dropdowns
        $sundayServices = SundayService::orderBy('service_date', 'desc')->get();
        $specialEvents = SpecialEvent::orderBy('event_date', 'desc')->get();
            
        return view('attendance.statistics', compact(
            'totalAttendances',
            'sundayAttendances', 
            'specialEventAttendances',
            'adultMemberAttendances',
            'childrenAttendances',
            'totalGuests',
            'maleMemberAttendances',
            'femaleMemberAttendances',
            'maleChildAttendances',
            'femaleChildAttendances',
            'mostRegularAttendees',
            'monthlyTrends',
            'sundayServices',
            'specialEvents'
        ));
    }
    
    /**
     * Manually trigger attendance notifications
     */
    public function triggerNotifications(Request $request)
    {
        try {
            $dryRun = $request->get('dry_run', false);
            
            // Run the attendance notification command
            $exitCode = Artisan::call('attendance:check-notifications', [
                '--dry-run' => $dryRun
            ]);
            
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance notifications processed successfully',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process attendance notifications',
                    'output' => $output
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing attendance notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get members who have missed 4+ consecutive weeks
     */
    public function getMembersWithMissedAttendance(Request $request)
    {
        $members = Member::where('membership_type', 'permanent')
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->get();
            
        $membersWithMissedAttendance = [];
        
        foreach ($members as $member) {
            if ($this->hasMissedFourConsecutiveWeeks($member)) {
                $lastAttendance = ServiceAttendance::where('member_id', $member->id)
                    ->where('service_type', 'sunday_service')
                    ->orderBy('attended_at', 'desc')
                    ->first();
                    
                $membersWithMissedAttendance[] = [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'member_id' => $member->member_id,
                    'phone' => $member->phone_number,
                    'last_attendance' => $lastAttendance ? $lastAttendance->attended_at->format('M d, Y') : 'Never',
                    'weeks_missed' => $this->calculateWeeksMissed($member)
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'members' => $membersWithMissedAttendance,
            'count' => count($membersWithMissedAttendance)
        ]);
    }

    /**
     * Sync attendance records from biometric device into ServiceAttendance
     */
    public function syncBiometricAttendance(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $serviceType = 'sunday_service';

        // Find Sunday service for the given date
        $service = SundayService::whereDate('service_date', $date)->first();
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => "No Sunday service found for date {$date}",
            ], 422);
        }

        try {
            $response = Http::timeout(10)->get('http://192.168.100.100:8000/api/v1/attendances', [
                // If the biometric API supports date filtering, keep this.
                // Otherwise the device will return all records and we will filter by date manually.
                'date' => $date,
                'per_page' => 500,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Biometric attendance sync failed (HTTP error)', [
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to contact biometric device: ' . $e->getMessage(),
            ], 500);
        }

        if (!$response->successful()) {
            \Log::warning('Biometric attendance sync HTTP non-success', [
                'date' => $date,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Biometric device API returned HTTP status ' . $response->status(),
            ], 500);
        }

        $payload = $response->json();
        if (!($payload['success'] ?? false) || !is_array($payload['data'] ?? null)) {
            \Log::warning('Biometric attendance sync: unexpected payload', [
                'date' => $date,
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Biometric device API returned invalid data format',
            ], 500);
        }

        $records = $payload['data'];
        $insertedCount = 0;
        $syncedMemberIds = []; // Track which member IDs were synced

        foreach ($records as $row) {
            // Example row:
            // {
            //   "id": 40,
            //   "user": { "id": 15, "name": "Ally", "enroll_id": "2" },
            //   "attendance_date": "2025-12-01",
            //   "check_in_time": "2025-12-01 12:00:24",
            //   ...
            // }

            // Filter by date (in case API doesn't support date filter)
            if (!empty($row['attendance_date']) && $row['attendance_date'] !== $date) {
                continue;
            }

            $enrollId = $row['user']['enroll_id'] ?? null;
            if (empty($enrollId)) {
                continue;
            }

            // Find linked member by biometric_enroll_id
            $member = Member::where('biometric_enroll_id', (string) $enrollId)->first();
            if (!$member) {
                \Log::warning('Biometric attendance record has no matching member', [
                    'date' => $date,
                    'enroll_id' => $enrollId,
                    'user' => $row['user'] ?? null,
                ]);
                continue;
            }

            // Avoid duplicate attendance for same member & service
            $exists = ServiceAttendance::where([
                'service_type' => $serviceType,
                'service_id' => $service->id,
                'member_id' => $member->id,
            ])->exists();

            if ($exists) {
                // Member already has attendance, but still include in synced list for checkbox checking
                $syncedMemberIds[] = $member->id;
                continue;
            }

            $attendedAt = $row['check_in_time'] ?? null;
            if (empty($attendedAt) && !empty($row['attendance_date'])) {
                $attendedAt = $row['attendance_date'] . ' 00:00:00';
            }

            ServiceAttendance::create([
                'service_type' => $serviceType,
                'service_id' => $service->id,
                'member_id' => $member->id,
                'child_id' => null,
                'attended_at' => $attendedAt ?? now(),
                'recorded_by' => 'BiometricDevice',
                'notes' => 'Imported from biometric device ' . ($row['device_ip'] ?? 'unknown'),
            ]);

            $insertedCount++;
            $syncedMemberIds[] = $member->id; // Track synced member ID
        }

        return response()->json([
            'success' => true,
            'message' => "Biometric attendance synced successfully for {$date}",
            'inserted' => $insertedCount,
            'synced_member_ids' => array_unique($syncedMemberIds), // Return unique member IDs that were synced
        ]);
    }
    
    /**
     * Check if a member has missed 4 consecutive weeks (helper method)
     */
    private function hasMissedFourConsecutiveWeeks(Member $member): bool
    {
        $fiveWeeksAgo = now()->subWeeks(5)->startOfWeek();
        $lastSunday = now()->previous(\Carbon\Carbon::SUNDAY);
        
        $recentServices = SundayService::whereBetween('service_date', [$fiveWeeksAgo, $lastSunday])
            ->orderBy('service_date', 'desc')
            ->get();

        if ($recentServices->count() < 4) {
            return false;
        }

        $attendedServices = ServiceAttendance::where('member_id', $member->id)
            ->where('service_type', 'sunday_service')
            ->whereIn('service_id', $recentServices->pluck('id'))
            ->pluck('service_id')
            ->toArray();

        $lastFourServices = $recentServices->take(4);
        $missedCount = 0;

        foreach ($lastFourServices as $service) {
            if (!in_array($service->id, $attendedServices)) {
                $missedCount++;
            } else {
                break;
            }
        }

        return $missedCount >= 4;
    }
    
    /**
     * Calculate weeks missed for a member
     */
    private function calculateWeeksMissed(Member $member): int
    {
        $lastAttendance = ServiceAttendance::where('member_id', $member->id)
            ->where('service_type', 'sunday_service')
            ->orderBy('attended_at', 'desc')
            ->first();
            
        if (!$lastAttendance) {
            // If never attended, calculate from first service
            $firstService = SundayService::orderBy('service_date', 'asc')->first();
            if ($firstService) {
                return now()->diffInWeeks($firstService->service_date);
            }
            return 0;
        }
        
        return now()->diffInWeeks($lastAttendance->attended_at);
    }
    
    /**
     * Send financial approval notification to pastors
     */
    private function sendFinancialApprovalNotification($type, $record)
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

            // Create notification data
            $notificationData = [
                'type' => $type,
                'record_id' => $record->id,
                'amount' => $record->amount,
                'date' => $record->offering_date ?? $record->tithe_date ?? $record->donation_date ?? $record->expense_date ?? $record->created_at,
                'recorded_by' => $record->recorded_by ?? 'System',
                'member_name' => $record->member->full_name ?? 'General Member',
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
                        'record_id' => $record->id
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Failed to send financial approval notification to pastor {$pastor->id}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send financial approval notification: ' . $e->getMessage());
        }
    }
}
