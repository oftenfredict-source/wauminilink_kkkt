<?php

namespace App\Http\Controllers;

use App\Models\ServiceAttendance;
use App\Models\SundayService;
use App\Models\SpecialEvent;
use App\Models\Member;
use App\Models\Child;
use App\Models\Offering;
use App\Services\SmsService;
use App\Services\ZKTecoService;
use App\Notifications\MissedAttendanceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * This syncs directly from the ZKTeco device and automatically checks checkboxes
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
            // Connect directly to ZKTeco device
            $ip = config('zkteco.ip', '192.168.100.108');
            $port = config('zkteco.port', 4370);
            $password = config('zkteco.password', 0);
            
            Log::info("Starting biometric sync for date: {$date}", [
                'ip' => $ip,
                'port' => $port,
                'service_id' => $service->id
            ]);
            
            $zkteco = new ZKTecoService($ip, $port, $password);
            
            if (!$zkteco->connect()) {
                Log::error("Failed to connect to biometric device", [
                    'ip' => $ip,
                    'port' => $port,
                    'date' => $date
                ]);
            return response()->json([
                'success' => false,
                    'message' => 'Failed to connect to biometric device. Please check device connection and settings.',
            ], 500);
        }

            Log::info("Successfully connected to biometric device");

            // Get users from device (those who have enrolled fingerprints)
            $deviceUsers = [];
            try {
                $deviceUsers = $zkteco->getUsers();
                Log::info("Retrieved " . count($deviceUsers) . " users from device (enrolled members)");
                
                if (count($deviceUsers) > 0) {
                    Log::info("Sample device user: " . json_encode($deviceUsers[0]));
                }
            } catch (\Exception $e) {
                Log::warning("Error getting users from device: " . $e->getMessage());
                // Continue even if we can't get users - we'll still process attendance
            }

            // Get attendance records from device
            try {
                $attendanceRecords = $zkteco->getAttendances();
                Log::info("Retrieved " . count($attendanceRecords) . " total attendance records from device");
                
                if (count($attendanceRecords) > 0) {
                    // Log first 3 records with full structure for debugging
                    Log::info("=== ATTENDANCE RECORDS FROM DEVICE ===");
                    for ($i = 0; $i < min(3, count($attendanceRecords)); $i++) {
                        Log::info("Record #{$i}: " . json_encode($attendanceRecords[$i], JSON_PRETTY_PRINT));
                        Log::info("Record #{$i} keys: " . implode(', ', array_keys($attendanceRecords[$i])));
                        
                        // Show what we extract
                        $eid = $attendanceRecords[$i]['user_id'] ?? $attendanceRecords[$i]['pin'] ?? $attendanceRecords[$i]['id'] ?? $attendanceRecords[$i]['uid'] ?? null;
                        $ts = $attendanceRecords[$i]['record_time'] ?? $attendanceRecords[$i]['timestamp'] ?? $attendanceRecords[$i]['time'] ?? null;
                        Log::info("Record #{$i} extracted - enroll_id: {$eid}, timestamp: " . var_export($ts, true));
                    }
                    
                    // Log all enroll IDs found in attendance records for debugging
                    // CRITICAL: Use user_id first (not uid) - uid is just sequential record number
                    $foundEnrollIds = [];
                    foreach ($attendanceRecords as $rec) {
                        $eid = $rec['user_id'] ?? $rec['pin'] ?? $rec['id'] ?? $rec['uid'] ?? null;
                        if ($eid) {
                            $foundEnrollIds[] = (string)$eid;
                        }
                    }
                    Log::info("Enroll IDs found in attendance records (using user_id field): " . implode(', ', array_unique($foundEnrollIds)));
                } else {
                    Log::warning("No attendance records found on device at all. Device might be empty or connection issue.");
                }
            } catch (\Exception $e) {
                Log::error("Error getting attendances from device: " . $e->getMessage());
                $zkteco->disconnect();
                throw $e;
            }
            
            $zkteco->disconnect();
            
            // Create a set of enroll IDs that have fingerprints enrolled on device
            $enrolledEnrollIds = [];
            foreach ($deviceUsers as $deviceUser) {
                $enrollId = $deviceUser['uid'] ?? $deviceUser['id'] ?? $deviceUser['userid'] ?? null;
                if ($enrollId) {
                    $enrolledEnrollIds[(string)$enrollId] = true;
                }
            }
            Log::info("Members with fingerprints enrolled on device: " . count($enrolledEnrollIds));

            // If no attendance records, provide helpful message
            // IMPORTANT: Registering a member doesn't create attendance - they must USE their fingerprint
            if (empty($attendanceRecords)) {
                $enrolledCount = count($enrolledEnrollIds);
                $message = "No attendance records found on device at all.";
                
                if ($enrolledCount > 0) {
                    $enrolledIds = array_keys($enrolledEnrollIds);
                    $message = "⚠️ NO ATTENDANCE RECORDS FOUND";
                    $message .= "\n\n📊 Status:";
                    $message .= "\n• {$enrolledCount} member(s) registered on device (Enroll IDs: " . implode(', ', $enrolledIds) . ")";
                    $message .= "\n• 0 attendance records found";
                    $message .= "\n• Date being synced: {$date}";
                    $message .= "\n\n🔍 CRITICAL DIFFERENCE:";
                    $message .= "\n❌ Enrolling fingerprint (in MENU) = Only saves fingerprint template";
                    $message .= "\n   → This does NOT create attendance records";
                    $message .= "\n   → Checkbox will NOT be checked";
                    $message .= "\n\n✅ Marking attendance (on MAIN SCREEN) = Creates attendance record";
                    $message .= "\n   → This creates an attendance record";
                    $message .= "\n   → Checkbox WILL be checked after sync";
                    $message .= "\n\n📋 HOW TO MARK ATTENDANCE:";
                    $message .= "\n1. Go to physical ZKTeco device";
                    $message .= "\n2. Make sure you're on the MAIN/HOME SCREEN (not in any menu)";
                    $message .= "\n3. Member places their enrolled finger on the scanner";
                    $message .= "\n4. Device should show: 'Verified', 'Success', or member's name";
                    $message .= "\n5. This creates an ATTENDANCE RECORD";
                    $message .= "\n6. Return to system and sync again";
                    $message .= "\n7. Checkbox will be checked automatically";
                    $message .= "\n\n💡 Remember: Enrollment (MENU) ≠ Attendance (MAIN SCREEN)";
                } else {
                    $message .= " No members are registered on the device yet.";
                }

            return response()->json([
                    'success' => true,
                    'message' => $message,
                    'inserted' => 0,
                    'synced_member_ids' => [], // Don't check any checkboxes if no attendance
                    'enrolled_count' => $enrolledCount,
                    'enrolled_ids' => $enrolledCount > 0 ? array_keys($enrolledEnrollIds) : [],
                    'debug_info' => [
                        'total_records_on_device' => 0,
                        'requested_date' => $date,
                        'enrolled_members' => $enrolledCount > 0 ? array_keys($enrolledEnrollIds) : []
                    ]
                ]);
            }
            
            // Log how many records match the date and show all dates found
            $recordsForDate = 0;
            $allDatesFound = [];
            $allEnrollIdsInRecords = [];
            
            foreach ($attendanceRecords as $rec) {
                // CRITICAL: Use user_id first (not uid) - uid is just sequential record number
                $eid = $rec['user_id'] ?? $rec['pin'] ?? $rec['id'] ?? $rec['uid'] ?? null;
                if ($eid) {
                    $allEnrollIdsInRecords[] = (string)$eid;
                }
                
                // CRITICAL: Use record_time first - this is the primary timestamp field from ZKTeco
                $timestamp = $rec['record_time'] ?? $rec['timestamp'] ?? $rec['time'] ?? $rec['punch_time'] ?? $rec['datetime'] ?? null;
                if ($timestamp) {
                    try {
                        if (is_numeric($timestamp)) {
                            $recDate = date('Y-m-d', (int)$timestamp);
                        } elseif (is_string($timestamp)) {
                            $recDate = date('Y-m-d', strtotime($timestamp));
                        } else {
                            continue;
                        }
                        $allDatesFound[] = $recDate;
                        if ($recDate === $date) {
                            $recordsForDate++;
                        }
                    } catch (\Exception $e) {
                        // Skip if can't parse
                    }
                }
            }
            
            $uniqueDates = array_unique($allDatesFound);
            $uniqueEnrollIds = array_unique($allEnrollIdsInRecords);
            
            Log::info("Attendance records analysis", [
                'total_records_on_device' => count($attendanceRecords),
                'records_matching_service_date' => $recordsForDate,
                'service_date' => $date,
                'service_id' => $service->id,
                'all_dates_found_on_device' => $uniqueDates,
                'note' => 'Only records matching the service date will be synced. Previous service attendance will be ignored.',
                'all_enroll_ids_in_records' => $uniqueEnrollIds,
                'enrolled_members_on_device' => array_keys($enrolledEnrollIds)
            ]);

        $insertedCount = 0;
        $syncedMemberIds = []; // Track which member IDs were synced
        $syncedChildIds = []; // Track which child IDs were synced (teenagers)

            foreach ($attendanceRecords as $index => $record) {
                try {
                    // CRITICAL: Based on actual ZKTeco device data structure:
                    //   - uid = sequential record number (1, 2, 3, 4...) - DO NOT USE THIS!
                    //   - user_id = actual user's enroll ID (1, 1, 1, 4, 4, 2...) - USE THIS!
                    //   - pin = alternative field for enroll ID
                    // We MUST use 'user_id' as the enroll_id, NOT 'uid'!
                    
                    // IMPORTANT: Check user_id FIRST, not uid!
                    $enrollId = $record['user_id'] ?? $record['pin'] ?? $record['id'] ?? $record['uid'] ?? null;
                    
                    if (empty($enrollId)) {
                        Log::warning("Attendance record missing enroll ID", [
                            'record_index' => $index,
                            'record' => $record,
                            'record_keys' => array_keys($record)
                        ]);
                continue;
            }

                    // Log full record structure for first few records
                    if ($index < 3) {
                        Log::info("=== PROCESSING ATTENDANCE RECORD #{$index} ===", [
                            'enroll_id' => $enrollId,
                            'enroll_id_type' => gettype($enrollId),
                            'record_keys' => array_keys($record),
                            'full_record' => $record,
                            'user_id_field' => $record['user_id'] ?? 'NOT SET',
                            'uid_field' => $record['uid'] ?? 'NOT SET',
                            'pin_field' => $record['pin'] ?? 'NOT SET',
                            'record_time_field' => $record['record_time'] ?? 'NOT SET',
                            'timestamp_field' => $record['timestamp'] ?? 'NOT SET',
                        ]);
                    }

                    // Get timestamp from record - CRITICAL: record_time is the primary field!
                    // ZKTeco library returns 'record_time' as the main timestamp field
                    $timestamp = $record['record_time'] ?? $record['timestamp'] ?? $record['time'] ?? $record['punch_time'] ?? $record['datetime'] ?? null;
                    
                    // Parse timestamp to get date
                    $recordDate = null;
                    if ($timestamp) {
                        try {
                            // ZKTeco timestamps can be:
                            // - Unix timestamp (numeric)
                            // - Formatted string (e.g., "2025-12-01 12:00:00")
                            // - DateTime object
                            if (is_numeric($timestamp)) {
                                $recordDate = date('Y-m-d', (int)$timestamp);
                            } elseif (is_string($timestamp)) {
                                $recordDate = date('Y-m-d', strtotime($timestamp));
                            } elseif (is_object($timestamp) && method_exists($timestamp, 'format')) {
                                $recordDate = $timestamp->format('Y-m-d');
                            }
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse attendance timestamp', [
                                'timestamp' => $timestamp,
                                'timestamp_type' => gettype($timestamp),
                                'error' => $e->getMessage(),
                                'record' => $record
                            ]);
                        }
                    } else {
                        // If no timestamp, use current date as fallback
                        $recordDate = $date;
                        Log::warning("Attendance record missing timestamp, using requested date", [
                            'enroll_id' => $enrollId,
                            'record' => $record
                        ]);
                    }

                    // CRITICAL: STRICT DATE FILTERING - Only sync records that EXACTLY match the service date
                    // This ensures each service is independent - members must mark attendance fresh for each new service
                    // Records without dates are SKIPPED to prevent syncing old/unknown records
                    
                    if (!$recordDate) {
                        // If record has no date, skip it - it might be from a previous service
                        // We cannot safely assign it to the current service
                        Log::warning("Skipping attendance record - no valid date found (prevents syncing old records)", [
                            'enroll_id' => $enrollId,
                            'requested_service_date' => $date,
                            'service_id' => $service->id,
                            'timestamp' => $timestamp,
                            'reason' => 'Record missing date information - cannot verify it belongs to this service'
                        ]);
                        continue; // Skip records without dates
                    }
                    
                    // Only process records that EXACTLY match the service date
                    if ($recordDate !== $date) {
                        Log::info("Skipping attendance record - date does not match service date (strict filtering)", [
                            'record_date' => $recordDate,
                            'service_date' => $date,
                            'service_id' => $service->id,
                            'enroll_id' => $enrollId,
                            'timestamp' => $timestamp,
                            'reason' => 'Record is from a different date - each service requires fresh attendance'
                        ]);
                        continue; // Skip records from different dates
                    }
                    
                    Log::info("Processing attendance record - date matches", [
                    'enroll_id' => $enrollId,
                        'record_date' => $recordDate,
                        'requested_date' => $date,
                        'timestamp' => $timestamp
                    ]);

            // Find linked member or child (teenager) by biometric_enroll_id
                    // Try multiple ways to match enroll ID - check both members and children
                    $member = Member::where('biometric_enroll_id', (string) $enrollId)
                        ->orWhere('biometric_enroll_id', (int) $enrollId)
                        ->first();
                    
                    $child = null;
                    if (!$member) {
                        // If no member found, check if it's a teenager (child)
                        $child = Child::where('biometric_enroll_id', (string) $enrollId)
                            ->orWhere('biometric_enroll_id', (int) $enrollId)
                            ->first();
                    }
                    
            if (!$member && !$child) {
                        // Log all members and children with enroll IDs for debugging
                        $allMemberEnrollIds = Member::whereNotNull('biometric_enroll_id')
                            ->pluck('biometric_enroll_id', 'id')
                            ->toArray();
                        $allChildEnrollIds = Child::whereNotNull('biometric_enroll_id')
                            ->pluck('biometric_enroll_id', 'id')
                            ->toArray();
                        
                        Log::warning('Biometric attendance record has no matching member or child', [
                    'date' => $date,
                            'enroll_id_from_device' => $enrollId,
                            'enroll_id_type' => gettype($enrollId),
                            'record' => $record,
                            'available_member_enroll_ids' => $allMemberEnrollIds,
                            'available_child_enroll_ids' => $allChildEnrollIds,
                            'hint' => 'Check if enroll ID from device matches any member\'s or child\'s biometric_enroll_id'
                ]);
                continue;
            }

                    // Process attendance for member or child
                    if ($member) {
                        Log::info("Found matching member for attendance record", [
                            'member_id' => $member->id,
                            'member_name' => $member->full_name,
                            'member_enroll_id' => $member->biometric_enroll_id,
                            'device_enroll_id' => $enrollId,
                            'record_date' => $recordDate,
                            'requested_date' => $date
                        ]);
                        
                        // If member marked attendance, they must have enrolled fingerprint
                        // So we don't need to check enrolledEnrollIds - the attendance record is proof
                        Log::info("Processing attendance for member", [
                            'member_id' => $member->id,
                            'member_name' => $member->full_name,
                            'enroll_id' => $enrollId,
                            'record_date' => $recordDate,
                            'requested_date' => $date
                        ]);
                        
                        // Check if attendance already exists for this member and service
            $exists = ServiceAttendance::where([
                'service_type' => $serviceType,
                'service_id' => $service->id,
                'member_id' => $member->id,
            ])->exists();

            if ($exists) {
                // Member already has attendance, but still include in synced list for checkbox checking
                            // This ensures the checkbox is checked even if attendance was already in database
                $syncedMemberIds[] = $member->id;
                            Log::info("Member already has attendance - adding to synced list for checkbox", [
                                'member_id' => $member->id,
                                'member_name' => $member->full_name,
                                'enroll_id' => $enrollId,
                                'service_id' => $service->id,
                                'service_date' => $service->service_date
                            ]);
                continue;
            }

                        // Create attendance record for member
                        $attendedAt = null;
                        if ($timestamp) {
                            try {
                                if (is_numeric($timestamp)) {
                                    $attendedAt = date('Y-m-d H:i:s', (int)$timestamp);
                                } elseif (is_string($timestamp)) {
                                    $attendedAt = date('Y-m-d H:i:s', strtotime($timestamp));
                                } elseif (is_object($timestamp) && method_exists($timestamp, 'format')) {
                                    $attendedAt = $timestamp->format('Y-m-d H:i:s');
                                } else {
                                    $attendedAt = now();
                                }
                            } catch (\Exception $e) {
                                Log::warning("Failed to format timestamp, using current time", [
                                    'timestamp' => $timestamp,
                                    'error' => $e->getMessage()
                                ]);
                                $attendedAt = now();
                            }
                        } else {
                            $attendedAt = $date . ' ' . date('H:i:s');
            }

            ServiceAttendance::create([
                'service_type' => $serviceType,
                'service_id' => $service->id,
                'member_id' => $member->id,
                'child_id' => null,
                            'attended_at' => $attendedAt,
                            'recorded_by' => auth()->id(),
            ]);

            $insertedCount++;
                        $syncedMemberIds[] = $member->id;
                        
                        Log::info("Created attendance record for member", [
                            'member_id' => $member->id,
                            'member_name' => $member->full_name,
                            'enroll_id' => $enrollId,
                            'service_id' => $service->id,
                            'attended_at' => $attendedAt
                        ]);
                    } elseif ($child) {
                        // Process attendance for teenager (child)
                        Log::info("Found matching child (teenager) for attendance record", [
                            'child_id' => $child->id,
                            'child_name' => $child->full_name,
                            'child_enroll_id' => $child->biometric_enroll_id,
                            'device_enroll_id' => $enrollId,
                            'record_date' => $recordDate,
                            'requested_date' => $date
                        ]);
                        
                        // Check if attendance already exists for this child and THIS SPECIFIC SERVICE
                        // Each service is independent - children must mark attendance for each new service
                        $exists = ServiceAttendance::where([
                            'service_type' => $serviceType,
                            'service_id' => $service->id, // This ensures we check for THIS specific service only
                            'child_id' => $child->id,
                        ])->exists();

                        if ($exists) {
                            // Child already has attendance, but still include in synced list for checkbox checking
                            $syncedChildIds[] = $child->id;
                            Log::info("Child already has attendance - adding to synced list for checkbox", [
                                'child_id' => $child->id,
                                'child_name' => $child->full_name,
                                'enroll_id' => $enrollId,
                                'service_id' => $service->id,
                                'service_date' => $service->service_date
                            ]);
                            continue;
                        }

                        // Create attendance record for child
                        $attendedAt = null;
                        if ($timestamp) {
                            try {
                                if (is_numeric($timestamp)) {
                                    $attendedAt = date('Y-m-d H:i:s', (int)$timestamp);
                                } elseif (is_string($timestamp)) {
                                    $attendedAt = date('Y-m-d H:i:s', strtotime($timestamp));
                                } elseif (is_object($timestamp) && method_exists($timestamp, 'format')) {
                                    $attendedAt = $timestamp->format('Y-m-d H:i:s');
                                } else {
                                    $attendedAt = now();
                                }
                            } catch (\Exception $e) {
                                Log::warning("Failed to format timestamp, using current time", [
                                    'timestamp' => $timestamp,
                                    'error' => $e->getMessage()
                                ]);
                                $attendedAt = now();
                            }
                        } else {
                            $attendedAt = $date . ' ' . date('H:i:s');
                        }

                        ServiceAttendance::create([
                            'service_type' => $serviceType,
                            'service_id' => $service->id,
                            'member_id' => null,
                            'child_id' => $child->id,
                            'attended_at' => $attendedAt,
                            'recorded_by' => auth()->id(),
                        ]);

                        $insertedCount++;
                        $syncedChildIds[] = $child->id; // Add child ID to synced list for checkbox checking
                        
                        Log::info("Created attendance record for child (teenager)", [
                            'child_id' => $child->id,
                            'child_name' => $child->full_name,
                            'enroll_id' => $enrollId,
                            'service_id' => $service->id,
                            'attended_at' => $attendedAt
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing attendance record", [
                        'record_index' => $index,
                        'record' => $record,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with next record instead of failing entire sync
                    continue;
                }
            }

            // IMPORTANT: Only return member IDs who actually marked attendance on the device
            // Do NOT check checkboxes for members who just enrolled fingerprint but didn't mark attendance
            // The synced_member_ids array already contains only members who marked attendance
            // No need to add additional members - only those who used their fingerprint to mark attendance
            
            $uniqueSyncedIds = array_values(array_unique($syncedMemberIds)); // Re-index array and ensure unique
            
            // Convert to integers to match checkbox IDs (they're stored as integers in HTML)
            $uniqueSyncedIds = array_map('intval', $uniqueSyncedIds);
            
            // Also process child IDs for teenagers
            $uniqueSyncedChildIds = array_values(array_unique($syncedChildIds)); // Re-index array and ensure unique
            $uniqueSyncedChildIds = array_map('intval', $uniqueSyncedChildIds);
            
            // Log detailed information for debugging
            $debugInfo = [
                'total_attendance_records' => count($attendanceRecords),
                'inserted_count' => $insertedCount,
                'synced_member_ids_count' => count($uniqueSyncedIds),
                'synced_member_ids' => $uniqueSyncedIds,
                'synced_child_ids_count' => count($uniqueSyncedChildIds),
                'synced_child_ids' => $uniqueSyncedChildIds,
                'synced_member_ids_types' => array_map('gettype', $uniqueSyncedIds),
                'enrolled_members_count' => count($enrolledEnrollIds),
                'requested_date' => $date,
                'service_id' => $service->id,
                'service_date' => $service->service_date
            ];
            
            // Add member details for each synced member
            if (count($uniqueSyncedIds) > 0) {
                $memberDetails = [];
                foreach ($uniqueSyncedIds as $mid) {
                    $m = Member::find($mid);
                    if ($m) {
                        $memberDetails[] = [
                            'id' => $m->id,
                            'name' => $m->full_name,
                            'enroll_id' => $m->biometric_enroll_id,
                            'checkbox_id' => "member_{$m->id}"
                        ];
                    }
                }
                $debugInfo['synced_member_details'] = $memberDetails;
            }
            
            // Add child details for each synced child (teenager)
            if (count($uniqueSyncedChildIds) > 0) {
                $childDetails = [];
                foreach ($uniqueSyncedChildIds as $cid) {
                    $c = Child::find($cid);
                    if ($c) {
                        $childDetails[] = [
                            'id' => $c->id,
                            'name' => $c->full_name,
                            'enroll_id' => $c->biometric_enroll_id,
                            'checkbox_id' => "child_{$c->id}"
                        ];
                    }
                }
                $debugInfo['synced_child_details'] = $childDetails;
            }
            
            Log::info("Sync complete - returning synced member and child IDs", $debugInfo);
            
            $totalSynced = count($uniqueSyncedIds) + count($uniqueSyncedChildIds);
            $message = "Biometric attendance synced successfully for service on {$date}. {$insertedCount} new record(s) added.";
            
            // Add note about service-specific attendance
            $message .= "\n\n📅 Note: Each service requires fresh attendance. Only attendance marked for this service date has been synced.";
            $message .= " Previous service attendance records are ignored.";
            
            if ($totalSynced > 0) {
                $memberText = count($uniqueSyncedIds) > 0 ? count($uniqueSyncedIds) . " member(s)" : "";
                $childText = count($uniqueSyncedChildIds) > 0 ? count($uniqueSyncedChildIds) . " teenager(s)" : "";
                $parts = array_filter([$memberText, $childText]);
                $message .= "\n\n✅ " . implode(" and ", $parts) . " found with attendance for this service.";
            } else {
                $message .= "\n\n⚠️ No members or teenagers found with attendance for this service date.";
                if (count($enrolledEnrollIds) > 0) {
                    $message .= "\n\n💡 " . count($enrolledEnrollIds) . " member(s) are registered on device but haven't marked attendance yet.";
                    $message .= "\n   Members must USE their fingerprint on the device (on the main screen) to create attendance records for this service.";
                }
        }

        return response()->json([
            'success' => true,
                'message' => $message,
            'inserted' => $insertedCount,
                'synced_member_ids' => $uniqueSyncedIds, // Return unique member IDs that were synced (as integers)
                'synced_child_ids' => $uniqueSyncedChildIds, // Return unique child IDs (teenagers) that were synced (as integers)
                'enrolled_count' => count($enrolledEnrollIds),
            ]);

        } catch (\Exception $e) {
            Log::error('Biometric attendance sync failed', [
                'date' => $date,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Provide more helpful error message
            $errorMessage = 'Failed to sync from biometric device: ' . $e->getMessage();
            
            // Add troubleshooting tips based on error type
            if (strpos($e->getMessage(), 'connect') !== false || strpos($e->getMessage(), 'timeout') !== false) {
                $errorMessage .= ' Please check: 1) Device is powered on, 2) IP address is correct, 3) Device is on the same network.';
            } elseif (strpos($e->getMessage(), 'getAttendances') !== false) {
                $errorMessage .= ' The device may not support this method. Check device compatibility.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        }
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
