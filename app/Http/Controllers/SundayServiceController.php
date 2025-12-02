<?php

namespace App\Http\Controllers;

use App\Models\SundayService;
use App\Models\Member;
use App\Models\Offering;
use App\Models\WeeklyAssignment;
use Illuminate\Http\Request;

class SundayServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SundayService::query();

        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function($q) use ($s) {
                $q->where('theme', 'like', "%{$s}%")
                  ->orWhere('preacher', 'like', "%{$s}%")
                  ->orWhere('venue', 'like', "%{$s}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('service_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('service_date', '<=', $request->date('to'));
        }

        $services = $query->with(['coordinator', 'churchElder'])->orderBy('service_date', 'desc')->paginate(10);
        $services->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json($services);
        }

        $totalMembers = Member::count();
        return view('services.sunday.page', compact('services', 'totalMembers'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Sunday Service Store Request:', $request->all());
            
            $validated = $request->validate([
                'service_date' => 'required|date',
                'service_type' => 'required|string|max:255',
                'theme' => 'nullable|string|max:255',
                'preacher' => 'nullable|string|max:255',
                'coordinator_id' => 'nullable|exists:members,id',
                'church_elder_id' => 'nullable|exists:members,id',
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
            ], [
                'service_date.required' => 'Service date is required.',
                'service_date.date' => 'Please enter a valid date.',
                'service_type.required' => 'Service type is required.',
                'service_type.in' => 'Please select a valid service type.',
                'start_time.date_format' => 'Start time must be in HH:MM format.',
                'end_time.date_format' => 'End time must be in HH:MM format.',
                'attendance_count.integer' => 'Attendance count must be a whole number.',
                'attendance_count.min' => 'Attendance count cannot be negative.',
                'offerings_amount.numeric' => 'Offerings amount must be a number.',
                'offerings_amount.min' => 'Offerings amount cannot be negative.',
            ]);

            // Set status based on whether attendance/offerings are provided
            $validated['status'] = ($request->has('attendance_count') || $request->has('offerings_amount')) 
                ? 'completed' 
                : 'scheduled';

            \Log::info('Validated data:', $validated);

            // Check for duplicate service_date + service_type combination
            $existingService = SundayService::where('service_date', $validated['service_date'])
                ->where('service_type', $validated['service_type'])
                ->first();
            
            if ($existingService) {
                return response()->json([
                    'success' => false,
                    'message' => 'A ' . $validated['service_type'] . ' service already exists for this date. Please choose a different date or service type.',
                    'errors' => ['service_date' => ['A service of this type already exists for this date.']]
                ], 422);
            }

            try {
                $service = SundayService::create($validated);
                \Log::info('Service created successfully', ['service_id' => $service->id, 'service_date' => $service->service_date]);
            } catch (\Exception $e) {
                \Log::error('Failed to create service', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'validated_data' => $validated
                ]);
                throw $e;
            }

            // Send SMS notifications to coordinator and church elder (don't fail if this fails)
            try {
                $this->sendServiceNotifications($service);
            } catch (\Exception $e) {
                \Log::error('Failed to send service notifications', ['error' => $e->getMessage()]);
                // Don't fail the entire request if notifications fail
            }

            // If offerings amount is provided, create an Offering record for pastor approval
            if ($request->has('offerings_amount') && $validated['offerings_amount'] > 0) {
                $offering = Offering::create([
                    'member_id' => null, // General member offering from Sunday service
                    'amount' => $validated['offerings_amount'],
                    'offering_date' => $validated['service_date'],
                    'offering_type' => 'general',
                    'service_type' => 'sunday_service',
                    'service_id' => $service->id,
                    'payment_method' => 'cash',
                    'reference_number' => 'SS-' . $service->id . '-' . time(),
                    'notes' => 'Sunday Service Offering - ' . ($validated['theme'] ?? 'General Service'),
                    'recorded_by' => auth()->user()->name ?? 'System',
                    'approval_status' => 'pending',
                    'is_verified' => false
                ]);

                // Send notification to pastors about pending offering
                $this->sendFinancialApprovalNotification('offering', $offering);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sunday service saved successfully' . ($request->has('offerings_amount') && $validated['offerings_amount'] > 0 ? ' and offering sent for pastor approval' : ''),
                'service' => $service,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(SundayService $sundayService)
    {
        $sundayService->load(['coordinator', 'churchElder']);
        return response()->json($sundayService);
    }

    public function update(Request $request, SundayService $sundayService)
    {
        $validated = $request->validate([
            'service_date' => 'sometimes|required|date',
            'service_type' => 'sometimes|required|string|max:255',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'coordinator_id' => 'nullable|exists:members,id',
            'church_elder_id' => 'nullable|exists:members,id',
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

        // Update status based on whether attendance/offerings are provided
        $validated['status'] = ($request->has('attendance_count') || $request->has('offerings_amount')) 
            ? 'completed' 
            : 'scheduled';

        // Check for duplicate service_date + service_type combination (excluding current service)
        if ($request->has('service_date') || $request->has('service_type')) {
            $serviceDate = $validated['service_date'] ?? $sundayService->service_date;
            $serviceType = $validated['service_type'] ?? $sundayService->service_type;
            
            $existingService = SundayService::where('service_date', $serviceDate)
                ->where('service_type', $serviceType)
                ->where('id', '!=', $sundayService->id)
                ->first();
            
            if ($existingService) {
                return response()->json([
                    'success' => false,
                    'message' => 'A ' . $serviceType . ' service already exists for this date. Please choose a different date or service type.',
                    'errors' => ['service_date' => ['A service of this type already exists for this date.']]
                ], 422);
            }
        }

        // Check if coordinator is being changed
        $coordinatorChanged = $request->has('coordinator_id') && 
                             $sundayService->coordinator_id != $request->coordinator_id;

        $sundayService->update($validated);
        
        // Refresh relationships to ensure updated data is available
        $sundayService->refresh();
        $sundayService->load(['coordinator', 'churchElder']);

        // Send SMS notification if coordinator was changed or newly assigned
        if ($coordinatorChanged && $sundayService->coordinator_id) {
            $this->sendCoordinatorSms($sundayService);
        }

        // Handle offerings changes
        if ($request->has('offerings_amount') && $validated['offerings_amount'] > 0) {
            // Check if there's already an offering record for this service
            $existingOffering = Offering::where('service_id', $sundayService->id)
                ->where('service_type', 'sunday_service')
                ->first();

            if ($existingOffering) {
                // Update existing offering
                $existingOffering->update([
                    'amount' => $validated['offerings_amount'],
                    'notes' => 'Sunday Service Offering - ' . ($validated['theme'] ?? 'General Service'),
                    'approval_status' => 'pending', // Reset to pending for re-approval
                    'is_verified' => false
                ]);
            } else {
                // Create new offering record
                $offering = Offering::create([
                    'member_id' => null, // General member offering from Sunday service
                    'amount' => $validated['offerings_amount'],
                    'offering_date' => $validated['service_date'],
                    'offering_type' => 'general',
                    'service_type' => 'sunday_service',
                    'service_id' => $sundayService->id,
                    'payment_method' => 'cash',
                    'reference_number' => 'SS-' . $sundayService->id . '-' . time(),
                    'notes' => 'Sunday Service Offering - ' . ($validated['theme'] ?? 'General Service'),
                    'recorded_by' => auth()->user()->name ?? 'System',
                    'approval_status' => 'pending',
                    'is_verified' => false
                ]);

                // Send notification to pastors about pending offering
                $this->sendFinancialApprovalNotification('offering', $offering);
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Sunday service updated successfully' . ($request->has('offerings_amount') && $validated['offerings_amount'] > 0 ? ' and offering sent for pastor approval' : ''),
            'service' => $sundayService
        ]);
    }

    public function destroy(SundayService $sundayService)
    {
        $sundayService->delete();
        return response()->json(['success' => true, 'message' => 'Sunday service deleted successfully']);
    }

    /**
     * Get all coordinators (from all registered members)
     */
    public function getCoordinators(Request $request)
    {
        try {
            $search = $request->input('search', '');
            
            $query = Member::query();
            
            // If search term provided, filter by name or member_id
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('member_id', 'like', "%{$search}%");
                });
            }
            
            $coordinators = $query->orderBy('full_name')
                ->limit(100) // Limit results for performance
                ->get(['id', 'full_name', 'member_id']);

            $totalCount = Member::count();
            
            \Log::info('Coordinators Query Result', [
                'count' => $coordinators->count(),
                'total' => $totalCount,
                'search_term' => $search
            ]);

            return response()->json([
                'success' => true,
                'coordinators' => $coordinators->map(function($member) {
                    return [
                        'id' => $member->id,
                        'full_name' => $member->full_name ?? 'Unknown',
                        'member_id' => $member->member_id ?? 'N/A',
                        'display_text' => ($member->full_name ?? 'Unknown') . ' (' . ($member->member_id ?? 'N/A') . ')'
                    ];
                }),
                'total' => $totalCount,
                'has_more' => $totalCount > 100
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading coordinators', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'coordinators' => [],
                'total' => 0,
                'has_more' => false
            ], 500);
        }
    }

    /**
     * Get church elders (from leaders with position 'elder')
     * Supports search functionality for better filtering when there are many options
     */
    public function getChurchElders(Request $request)
    {
        try {
            // Get current date for comparison
            $today = now()->toDateString();
            $search = $request->input('search', '');
            
            // Query for active church elders from Leaders model
            $query = \App\Models\Leader::with('member')
                ->where('position', 'elder')
                ->where('is_active', true)
                ->where(function($query) use ($today) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $today);
                })
                ->where('appointment_date', '<=', $today)
                ->whereHas('member'); // Only get leaders that have associated members
            
            // Apply search filter if provided
            if (!empty($search)) {
                $query->whereHas('member', function($q) use ($search) {
                    $q->where(function($subQ) use ($search) {
                        $subQ->where('full_name', 'like', "%{$search}%")
                             ->orWhere('member_id', 'like', "%{$search}%");
                    });
                });
            }
            
            $leaders = $query->get();
            
            $churchElders = $leaders
                ->filter(function($leader) {
                    // Only include leaders that have a valid member
                    return $leader->member !== null;
                })
                ->map(function($leader) {
                    return [
                        'id' => $leader->member->id,
                        'full_name' => $leader->member->full_name,
                        'member_id' => $leader->member->member_id,
                        'display_text' => $leader->member->full_name . ' (' . $leader->member->member_id . ')'
                    ];
                })
                ->unique('id')
                ->values()
                ->sortBy('full_name')
                ->values();

            \Log::info('Church Elders Query Result', [
                'count' => $churchElders->count(),
                'total_leaders' => $leaders->count(),
                'search_term' => $search,
                'elders' => $churchElders->toArray()
            ]);

            return response()->json([
                'success' => true,
                'church_elders' => $churchElders,
                'total' => $churchElders->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading church elders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'church_elders' => [],
                'total' => 0
            ], 500);
        }
    }

    /**
     * Get preachers (from users/pastors and leaders with pastor position, or allow "other")
     */
    public function getPreachers()
    {
        $today = now()->toDateString();
        $preachers = collect();
        
        // Get pastors from Users model
        $userPastors = \App\Models\User::where(function($query) {
                $query->where('role', 'pastor')
                      ->orWhere('can_approve_finances', true);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        
        // Add user pastors to collection
        foreach ($userPastors as $user) {
            $preachers->push([
                'id' => 'user_' . $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'display_text' => $user->name . ($user->email ? ' (' . $user->email . ')' : ''),
                'type' => 'user'
            ]);
        }
        
        // Get pastors from Leaders model (members with pastor position)
        $leaderPastors = \App\Models\Leader::with('member')
            ->whereIn('position', ['pastor', 'assistant_pastor'])
            ->where('is_active', true)
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $today);
            })
            ->where('appointment_date', '<=', $today)
            ->whereHas('member')
            ->get()
            ->map(function($leader) {
                return [
                    'id' => 'leader_' . $leader->member->id,
                    'name' => $leader->member->full_name,
                    'email' => $leader->member->email,
                    'member_id' => $leader->member->member_id,
                    'display_text' => $leader->member->full_name . ' (' . $leader->member->member_id . ')',
                    'type' => 'leader'
                ];
            });
        
        // Merge and remove duplicates (by name)
        $allPreachers = $preachers->merge($leaderPastors)
            ->unique('name')
            ->sortBy('name')
            ->values();

        \Log::info('Preachers Query Result', [
            'count' => $allPreachers->count(),
            'user_pastors' => $userPastors->count(),
            'leader_pastors' => $leaderPastors->count()
        ]);

        return response()->json([
            'success' => true,
            'preachers' => $allPreachers,
            'allow_other' => true // Indicate that "other" option is allowed
        ]);
    }

    /**
     * Get weekly assignment for a specific date (for church elder auto-population)
     */
    public function getWeeklyAssignmentForDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->date;
        
        \Log::info('Checking weekly assignment for date', ['date' => $date]);
        
        // Find active weekly assignment for elder position that covers this date
        $assignment = WeeklyAssignment::with(['leader.member'])
            ->where('position', 'elder')
            ->where('is_active', true)
            ->where('week_start_date', '<=', $date)
            ->where('week_end_date', '>=', $date)
            ->first();

        \Log::info('Weekly assignment query result', [
            'found' => $assignment ? true : false,
            'assignment_id' => $assignment ? $assignment->id : null,
            'leader_id' => $assignment && $assignment->leader ? $assignment->leader->id : null,
            'member_id' => $assignment && $assignment->leader && $assignment->leader->member ? $assignment->leader->member->id : null
        ]);

        if ($assignment && $assignment->leader && $assignment->leader->member) {
            $member = $assignment->leader->member;
            
            // Verify the member is actually an active church elder
            $today = now()->toDateString();
            $isActiveElder = Member::where('id', $member->id)
                ->whereHas('leadershipPositions', function($query) use ($today) {
                    $query->where('position', 'elder')
                          ->where('is_active', true)
                          ->where(function($q) use ($today) {
                              $q->whereNull('end_date')
                                 ->orWhere('end_date', '>=', $today);
                          })
                          ->where('appointment_date', '<=', $today);
                })->exists();

            \Log::info('Member elder status', [
                'member_id' => $member->id,
                'member_name' => $member->full_name,
                'member_code' => $member->member_id,
                'is_active_elder' => $isActiveElder,
                'date_checked' => $date
            ]);

            // Return the member ID (which is what the church elders dropdown uses)
            return response()->json([
                'success' => true,
                'has_assignment' => true,
                'is_active_elder' => $isActiveElder,
                'assignment' => [
                    'leader_id' => $assignment->leader->id,
                    'member_id' => $member->id, // This is the member's primary key ID
                    'member_name' => $member->full_name,
                    'member_code' => $member->member_id, // This is the member's code/identifier
                    'display_text' => $member->full_name . ' (' . $member->member_id . ')',
                    'duties' => $assignment->duties
                ]
            ]);
        }

        \Log::info('No weekly assignment found for date', ['date' => $date]);

        return response()->json([
            'success' => true,
            'has_assignment' => false
        ]);
    }

    public function exportCsv(Request $request)
    {
        $filename = 'sunday_services_' . now()->format('Ymd_His') . '.csv';
        $services = SundayService::with(['coordinator', 'churchElder'])->orderBy('service_date', 'desc')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function() use ($services) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Service Date','Service Type','Theme','Preacher','Coordinator','Church Elder','Start Time','End Time','Venue','Attendance','Offerings','Scripture Readings','Choir','Announcements','Notes']);
            foreach ($services as $s) {
                fputcsv($handle, [
                    optional($s->service_date)->format('Y-m-d'),
                    $s->service_type,
                    $s->theme,
                    $s->preacher,
                    $s->coordinator ? $s->coordinator->full_name : '',
                    $s->churchElder ? $s->churchElder->full_name : '',
                    $s->start_time,
                    $s->end_time,
                    $s->venue,
                    $s->attendance_count,
                    $s->offerings_amount,
                    $s->scripture_readings,
                    $s->choir,
                    $s->announcements,
                    $s->notes,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send notification to pastors about pending financial approval
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

    /**
     * Send SMS notification to coordinator only
     */
    private function sendCoordinatorSms($service)
    {
        try {
            $smsEnabled = \App\Services\SettingsService::get('enable_sms_notifications', false);
            if (!$smsEnabled) {
                \Log::info('SMS notifications disabled, skipping coordinator notification');
                return;
            }

            // Load the coordinator relationship
            $service->load('coordinator');

            // Notify Coordinator with custom message
            if ($service->coordinator_id && $service->coordinator) {
                // Format date and day in Swahili
                $dayNames = [
                    'Monday' => 'Jumatatu',
                    'Tuesday' => 'Jumanne',
                    'Wednesday' => 'Jumatano',
                    'Thursday' => 'Alhamisi',
                    'Friday' => 'Ijumaa',
                    'Saturday' => 'Jumamosi',
                    'Sunday' => 'Jumapili'
                ];
                $dayName = $dayNames[$service->service_date->format('l')] ?? $service->service_date->format('l');
                $serviceDate = $service->service_date->format('d/m/Y');
                $dateWithDay = $serviceDate . ' / ' . $dayName;
                
                // Custom coordinator message as requested
                $coordinatorName = $service->coordinator->full_name;
                $coordinatorMessage = "Shalom {$coordinatorName}, tunakujulisha kuwa umepangwa kuratibu ibada ya {$dateWithDay}. Tafadhali jiandae ipasavyo na thibitisha kupokea ujumbe huu. Mungu akutie nguvu katika utumishi wako.";
                
                $coordinatorResp = app(\App\Services\SmsService::class)->sendDebug($service->coordinator->phone_number, $coordinatorMessage);
                \Log::info('Service coordinator SMS sent', [
                    'coordinator_id' => $service->coordinator_id,
                    'coordinator_name' => $service->coordinator->full_name,
                    'coordinator_phone' => $service->coordinator->phone_number,
                    'sms_response' => $coordinatorResp
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send coordinator SMS: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS notifications to coordinator, church elder, and all members
     */
    private function sendServiceNotifications($service)
    {
        try {
            $smsEnabled = \App\Services\SettingsService::get('enable_sms_notifications', false);
            if (!$smsEnabled) {
                \Log::info('SMS notifications disabled, skipping service notifications');
                return;
            }

            // Load the relationships to ensure we have the data
            $service->load(['coordinator', 'churchElder']);

            $churchName = \App\Services\SettingsService::get('church_name', 'Waumini Church');
            $serviceDate = $service->service_date->format('d/m/Y');
            // Handle start_time as string (HH:MM format) or DateTime
            $serviceTime = $service->start_time ? (is_string($service->start_time) ? $service->start_time : $service->start_time->format('H:i')) : 'TBA';
            
            // Notify Coordinator using the dedicated method
            $this->sendCoordinatorSms($service);

            // Notify Church Elder
            if ($service->church_elder_id && $service->churchElder) {
                $coordinatorName = $service->coordinator ? $service->coordinator->full_name : 'TBA';
                $elderMessage = "Taarifa ya Huduma:\n{$service->service_type} imepangwa tarehe {$serviceDate}\nMratibu: {$coordinatorName}\nMada: {$service->theme}\nMuda: {$serviceTime}\n\nTafadhali pata msaada wa kusimamia huduma hii.\n- {$churchName}";
                
                $elderResp = app(\App\Services\SmsService::class)->sendDebug($service->churchElder->phone_number, $elderMessage);
                \Log::info('Service church elder SMS sent', [
                    'elder_id' => $service->church_elder_id,
                    'elder_name' => $service->churchElder->full_name,
                    'elder_phone' => $service->churchElder->phone_number,
                    'sms_response' => $elderResp
                ]);
            }

            // Send SMS to all members about the scheduled service
            $this->sendServiceNotificationToMembers($service);

        } catch (\Exception $e) {
            \Log::error('Failed to send service notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS notification to all members about scheduled service
     */
    private function sendServiceNotificationToMembers($service)
    {
        try {
            $smsService = app(\App\Services\SmsService::class);
            
            // Format date and day in Swahili
            $dayNames = [
                'Monday' => 'Jumatatu',
                'Tuesday' => 'Jumanne',
                'Wednesday' => 'Jumatano',
                'Thursday' => 'Alhamisi',
                'Friday' => 'Ijumaa',
                'Saturday' => 'Jumamosi',
                'Sunday' => 'Jumapili'
            ];
            $dayName = $dayNames[$service->service_date->format('l')] ?? $service->service_date->format('l');
            $serviceDate = $service->service_date->format('d/m/Y');
            $dateWithDay = $serviceDate . ' (' . $dayName . ')';
            
            // Format time - handle as string (HH:MM format) or DateTime
            $serviceTime = $service->start_time ? (is_string($service->start_time) ? $service->start_time : $service->start_time->format('H:i')) : 'TBA';
            
            // Get all active members with phone numbers
            $members = Member::where('membership_type', 'permanent')
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->get();
            
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($members as $member) {
                try {
                    // Format date for message (without day name in parentheses)
                    $formattedDate = $service->service_date->format('d/m/Y');
                    
                    // Format time
                    $formattedTime = $serviceTime !== 'TBA' ? $serviceTime : 'TBA';
                    
                    // Build personalized message using the specified template
                    $message = "Shalom {$member->full_name}, ibada yetu ya {$formattedDate} saa {$formattedTime} inakaribia.\n";
                    $message .= "Jitayarishe kuonana na Bwana kwa sifa, maombi na neno lenye nguvu.\n";
                    $message .= "Usikose, Mungu ana jambo maalum kwa ajili yako.";
                    
                    // Send SMS
                    $result = $smsService->send($member->phone_number, $message);
                    
                    if ($result) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                        \Log::warning('Failed to send service notification SMS', [
                            'member_id' => $member->id,
                            'member_name' => $member->full_name,
                            'phone' => $member->phone_number
                        ]);
                    }
                    
                    // Small delay to avoid overwhelming the SMS service
                    usleep(100000); // 0.1 second delay
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    \Log::error('Error sending service notification to member', [
                        'member_id' => $member->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            \Log::info('Service notification SMS sent to members', [
                'service_id' => $service->id,
                'service_date' => $service->service_date->format('Y-m-d'),
                'total_members' => $members->count(),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send service notifications to members: ' . $e->getMessage());
        }
    }
}


