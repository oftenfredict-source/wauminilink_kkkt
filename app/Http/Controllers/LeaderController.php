<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leader;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
use App\Services\SmsService;
use App\Services\SettingsService;

class LeaderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->canManageLeadership()) {
                abort(403, 'Unauthorized. Only Pastors and Secretaries can manage leadership positions.');
            }
            return $next($request);
        })->except(['index', 'show', 'showChangePassword', 'updatePassword']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Leader::with(['member', 'campus', 'communities.campus'])
            ->active()
            ->orderBy('position')
            ->orderBy('appointment_date', 'desc');

        // Apply branch filtering
        $userCampus = auth()->user()->getCampus();
        if ($userCampus && !$userCampus->is_main_campus) {
            // Branch user - only their branch leaders
            $query->where('campus_id', $userCampus->id);
        } elseif ($userCampus && $userCampus->is_main_campus && request()->filled('campus_id')) {
            // Usharika admin filtering by specific branch
            $query->where('campus_id', request()->campus_id);
        }
        // Otherwise show all (for Usharika admin)

        $leaders = $query->get();

        // Filter out leaders without members (data integrity issue)
        $leaders = $leaders->filter(function ($leader) {
            return $leader->member !== null;
        });

        // For evangelism leaders, also load campuses where they are assigned as evangelism leader
        $evangelismLeaders = $leaders->where('position', 'evangelism_leader');
        if ($evangelismLeaders->isNotEmpty()) {
            $assignedCampuses = \App\Models\Campus::whereIn('evangelism_leader_id', $evangelismLeaders->pluck('id'))
                ->get()
                ->keyBy('evangelism_leader_id');

            // Add assigned campus info to each evangelism leader
            foreach ($evangelismLeaders as $leader) {
                if ($assignedCampuses->has($leader->id)) {
                    $leader->assignedCampus = $assignedCampuses->get($leader->id);
                }
            }
        }

        // Group leaders by position
        $leadersByPosition = $leaders->groupBy('position');

        return view('leaders.index', compact('leaders', 'leadersByPosition'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get members - filtered by branch
        $userCampus = auth()->user()->getCampus();
        $query = Member::orderBy('full_name');

        // Check if campus_id is specified in request (for branch leader assignment)
        $targetCampusId = request()->get('campus_id');
        $targetCampus = null;

        if ($targetCampusId && (auth()->user()->isUsharikaAdmin() || (auth()->user()->getCampus() && auth()->user()->getCampus()->is_main_campus))) {
            // Usharika admin assigning leader to specific branch
            $query->where('campus_id', $targetCampusId);
            $targetCampus = \App\Models\Campus::find($targetCampusId);
        } elseif ($userCampus && !$userCampus->is_main_campus) {
            // Branch user - only their branch members
            $query->where('campus_id', $userCampus->id);
            $targetCampus = $userCampus;
        } else {
            $targetCampus = $userCampus;
        }

        $members = $query->get();
        $positions = $this->getPositionOptions();

        return view('leaders.create', compact('members', 'positions', 'userCampus', 'targetCampus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'position' => 'required|string|in:pastor,assistant_pastor,secretary,elder,deacon,deaconess,youth_leader,children_leader,worship_leader,choir_leader,usher_leader,evangelism_leader,parish_worker,prayer_leader,other',
            'position_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'appointed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Please correct the errors in the form.')
                ->withErrors($validator)
                ->withInput();
        }

        // Check if member already has an active position of the same type
        $existingLeader = Leader::where('member_id', $request->member_id)
            ->where('position', $request->position)
            ->where('is_active', true)
            ->first();

        if ($existingLeader) {
            return redirect()->back()
                ->with('error', 'This member already holds an active ' . $existingLeader->position_display . ' position.')
                ->withErrors(['position' => 'This member already holds an active ' . $existingLeader->position_display . ' position.'])
                ->withInput();
        }

        // Determine campus_id for leader
        $userCampus = auth()->user()->getCampus();
        $campusId = null;

        if ($userCampus && !$userCampus->is_main_campus) {
            // Branch user - assign to their branch
            $campusId = $userCampus->id;
        } elseif ($request->filled('campus_id')) {
            // Usharika admin can specify branch
            $campusId = $request->campus_id;
        } else {
            // Get member's campus
            $member = Member::find($request->member_id);
            if ($member && $member->campus_id) {
                $campusId = $member->campus_id;
            } elseif ($userCampus) {
                $campusId = $userCampus->id;
            }
        }

        $leaderData = $request->all();
        $leaderData['campus_id'] = $campusId;

        $leader = Leader::create($leaderData);

        // Load member relationship for notification
        $leader->load('member');

        // Automatically create or update user account for the assigned leader
        $userAccount = $this->createOrUpdateUserAccount($leader);

        // Send database notification to the appointed leader
        if ($leader->member) {
            $leader->member->notify(new \App\Notifications\LeaderAppointmentNotification($leader));
        }

        // Send SMS notification to the appointed leader with login credentials
        $smsResult = $this->sendLeaderAppointmentSms($leader, $userAccount);

        $successMessage = 'Leader position assigned successfully!';
        if ($userAccount) {
            $successMessage .= ' User account created/updated.';
        }

        // Add SMS status to message
        if ($smsResult && $smsResult['sent']) {
            $successMessage .= ' Login credentials sent via SMS.';
        } elseif ($smsResult && !$smsResult['sent']) {
            $successMessage .= ' Note: SMS could not be sent. ' . ($smsResult['reason'] ?? 'Please check member phone number and SMS settings.');
        }

        return redirect()->route('leaders.index')
            ->with('success', $successMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Leader $leader)
    {
        $leader->load('member');
        return view('leaders.show', compact('leader'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leader $leader)
    {
        $members = Member::orderBy('full_name')->get();
        $positions = $this->getPositionOptions();

        return view('leaders.edit', compact('leader', 'members', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leader $leader)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'position' => 'required|string|in:pastor,assistant_pastor,secretary,elder,deacon,deaconess,youth_leader,children_leader,worship_leader,choir_leader,usher_leader,evangelism_leader,parish_worker,prayer_leader,other',
            'position_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'is_active' => 'boolean',
            'appointed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Please correct the errors in the form.')
                ->withErrors($validator)
                ->withInput();
        }

        // Check if another member already has an active position of the same type
        if ($request->is_active) {
            $existingLeader = Leader::where('member_id', $request->member_id)
                ->where('position', $request->position)
                ->where('is_active', true)
                ->where('id', '!=', $leader->id)
                ->first();

            if ($existingLeader) {
                return redirect()->back()
                    ->with('error', 'This member already holds an active ' . $existingLeader->position_display . ' position.')
                    ->withErrors(['position' => 'This member already holds an active ' . $existingLeader->position_display . ' position.'])
                    ->withInput();
            }
        }

        // Check if position or member changed
        $positionChanged = $leader->position !== $request->position;
        $memberChanged = $leader->member_id !== $request->member_id;
        $wasActive = $leader->is_active;
        $isNowActive = $request->is_active ?? $leader->is_active;

        $leader->update($request->all());

        // Reload leader with member relationship
        $leader->load('member');

        // If leader was deactivated, check if user should be updated to member role
        if ($wasActive && !$isNowActive) {
            $this->updateUserRoleIfNoActivePositions($leader->member_id);
        }

        // Update user account if position or member changed and leader is active
        if (($positionChanged || $memberChanged) && $isNowActive) {
            $this->createOrUpdateUserAccount($leader);
        }

        return redirect()->route('leaders.index')
            ->with('success', 'Leader position updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leader $leader)
    {
        $memberId = $leader->member_id;
        $leader->delete();

        // Update user role if member has no active leadership positions
        $this->updateUserRoleIfNoActivePositions($memberId);

        return redirect()->route('leaders.index')
            ->with('success', 'Leader position removed successfully!');
    }

    /**
     * Deactivate a leader position
     */
    public function deactivate(Leader $leader)
    {
        $memberId = $leader->member_id;
        $leader->update(['is_active' => false]);

        // Update user role if member has no active leadership positions
        $this->updateUserRoleIfNoActivePositions($memberId);

        return redirect()->route('leaders.index')
            ->with('success', 'Leader position deactivated successfully!');
    }

    /**
     * Reactivate a leader position
     */
    public function reactivate(Leader $leader)
    {
        $leader->update(['is_active' => true]);

        // Update user account when reactivated
        $leader->load('member');
        $this->createOrUpdateUserAccount($leader);

        return redirect()->route('leaders.index')
            ->with('success', 'Leader position reactivated successfully!');
    }

    /**
     * Display leadership reports
     */
    public function reports()
    {
        $leaders = Leader::with('member')->get();

        // Group by position
        $leadersByPosition = $leaders->groupBy('position');

        // Active vs Inactive
        $activeLeaders = $leaders->where('is_active', true);
        $inactiveLeaders = $leaders->where('is_active', false);

        // By appointment year
        $leadersByYear = $leaders->groupBy(function ($leader) {
            return $leader->appointment_date->year;
        });

        // Recent appointments (last 6 months)
        $recentAppointments = $leaders->where('appointment_date', '>=', now()->subMonths(6));

        // Expiring terms (next 3 months)
        $expiringTerms = $leaders->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addMonths(3));

        return view('leaders.reports', compact(
            'leaders',
            'leadersByPosition',
            'activeLeaders',
            'inactiveLeaders',
            'leadersByYear',
            'recentAppointments',
            'expiringTerms'
        ));
    }

    /**
     * Export leadership report as CSV
     */
    public function exportCsv()
    {
        $leaders = Leader::with('member')->get();

        $filename = 'leadership_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($leaders) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Member ID',
                'Member Name',
                'Position',
                'Position Title',
                'Appointment Date',
                'End Date',
                'Status',
                'Appointed By',
                'Description',
                'Notes'
            ]);

            // CSV Data
            foreach ($leaders as $leader) {
                fputcsv($file, [
                    $leader->member->member_id,
                    $leader->member->full_name,
                    $leader->position_display,
                    $leader->position_title ?? '',
                    $leader->appointment_date->format('Y-m-d'),
                    $leader->end_date ? $leader->end_date->format('Y-m-d') : '',
                    $leader->is_active ? 'Active' : 'Inactive',
                    $leader->appointed_by ?? '',
                    $leader->description ?? '',
                    $leader->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export leadership report as PDF
     */
    public function exportPdf()
    {
        $leaders = Leader::with('member')->get();
        $leadersByPosition = $leaders->groupBy('position');
        $activeLeaders = $leaders->where('is_active', true);

        // For now, return a simple HTML view that can be printed as PDF
        // In a real application, you'd use a PDF library like DomPDF or TCPDF
        return view('leaders.reports-pdf', compact('leaders', 'leadersByPosition', 'activeLeaders'));
    }

    /**
     * Generate identity card for a specific leader
     */
    public function identityCard(Leader $leader)
    {
        $leader->load('member');

        // Get church information from settings or use defaults
        $churchName = \App\Services\SettingsService::get('church_name', 'Waumini Church');
        $churchAddress = \App\Services\SettingsService::get('church_address', 'Dar es Salaam, Tanzania');
        $churchPhone = \App\Services\SettingsService::get('church_phone', '+255 XXX XXX XXX');
        $churchEmail = \App\Services\SettingsService::get('church_email', 'info@waumini.org');

        return view('leaders.identity-card', compact('leader', 'churchName', 'churchAddress', 'churchPhone', 'churchEmail'));
    }

    /**
     * Generate identity cards for all active leaders
     */
    public function bulkIdentityCards()
    {
        $leaders = Leader::with('member')->active()->get();

        // Get church information from settings or use defaults
        $churchName = \App\Services\SettingsService::get('church_name', 'Waumini Church');
        $churchAddress = \App\Services\SettingsService::get('church_address', 'Dar es Salaam, Tanzania');
        $churchPhone = \App\Services\SettingsService::get('church_phone', '+255 XXX XXX XXX');
        $churchEmail = \App\Services\SettingsService::get('church_email', 'info@waumini.org');

        return view('leaders.bulk-identity-cards', compact('leaders', 'churchName', 'churchAddress', 'churchPhone', 'churchEmail'));
    }

    /**
     * Generate identity card for a specific position
     */
    public function positionIdentityCards($position)
    {
        $leaders = Leader::with('member')->where('position', $position)->active()->get();

        if ($leaders->isEmpty()) {
            return redirect()->back()->with('error', 'No active leaders found for this position.');
        }

        // Get church information from settings or use defaults
        $churchName = \App\Services\SettingsService::get('church_name', 'Waumini Church');
        $churchAddress = \App\Services\SettingsService::get('church_address', 'Dar es Salaam, Tanzania');
        $churchPhone = \App\Services\SettingsService::get('church_phone', '+255 XXX XXX XXX');
        $churchEmail = \App\Services\SettingsService::get('church_email', 'info@waumini.org');

        return view('leaders.bulk-identity-cards', compact('leaders', 'churchName', 'churchAddress', 'churchPhone', 'churchEmail'));
    }

    /**
     * Create or update user account for assigned leader
     */
    private function createOrUpdateUserAccount(Leader $leader)
    {
        try {
            $member = $leader->member;
            if (!$member) {
                \Log::warning('Cannot create user account: Leader has no associated member', [
                    'leader_id' => $leader->id
                ]);
                return null;
            }

            // Map leader position to user role
            $role = $this->mapPositionToRole($leader->position);

            if (!$role) {
                \Log::info('Leadership position does not require user account', [
                    'position' => $leader->position,
                    'member_id' => $member->id
                ]);
                return null;
            }

            // Extract lastname from full_name (assuming last word is lastname)
            $nameParts = explode(' ', trim($member->full_name));
            $lastname = !empty($nameParts) ? strtoupper(end($nameParts)) : 'MEMBER';

            // Check if user account already exists
            $user = \App\Models\User::where('member_id', $member->id)->first();

            if ($user) {
                // Update existing user account with new role
                $user->update([
                    'role' => $role,
                    'campus_id' => $leader->campus_id ?? $member->campus_id,
                    'can_approve_finances' => in_array($role, ['pastor', 'admin']),
                ]);

                \Log::info('User account updated for leader', [
                    'member_id' => $member->id,
                    'user_id' => $user->id,
                    'role' => $role,
                    'position' => $leader->position,
                ]);

                return [
                    'user' => $user,
                    'username' => $user->email,
                    'password' => $lastname, // Return lastname for SMS
                    'is_new' => false
                ];
            } else {
                // Create new user account with default credentials
                $user = \App\Models\User::create([
                    'name' => $member->full_name,
                    'email' => $member->member_id, // Use member_id as username/email
                    'password' => \Hash::make($lastname), // Password is lastname in uppercase
                    'role' => $role,
                    'member_id' => $member->id,
                    'phone_number' => $member->phone_number,
                    'campus_id' => $leader->campus_id ?? $member->campus_id,
                    'can_approve_finances' => in_array($role, ['pastor', 'admin']),
                ]);

                \Log::info('User account created for leader', [
                    'member_id' => $member->id,
                    'user_id' => $user->id,
                    'username' => $member->member_id,
                    'role' => $role,
                    'position' => $leader->position,
                ]);

                return [
                    'user' => $user,
                    'username' => $member->member_id,
                    'password' => $lastname,
                    'is_new' => true
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create/update user account for leader', [
                'leader_id' => $leader->id,
                'member_id' => $leader->member_id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Map leadership position to user role
     */
    private function mapPositionToRole($position)
    {
        return match ($position) {
            'pastor', 'assistant_pastor' => 'pastor',
            'secretary' => 'secretary',
            'evangelism_leader' => 'evangelism_leader',
            'parish_worker' => 'parish_worker',
            'elder' => 'elder',
            'treasurer', 'assistant_treasurer', 'assistant_secretary' => match ($position) {
                    'treasurer', 'assistant_treasurer' => 'treasurer',
                    'assistant_secretary' => 'secretary',
                    default => null
                },
            default => null // Positions like deacon, deaconess, youth_leader, etc. don't require user accounts
        };
    }

    /**
     * Update user role to 'member' if they have no active leadership positions
     */
    private function updateUserRoleIfNoActivePositions($memberId)
    {
        try {
            // Check if member has any active leadership positions
            $activePositions = Leader::where('member_id', $memberId)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->get();

            // If no active positions, update user role to 'member'
            if ($activePositions->isEmpty()) {
                $user = \App\Models\User::where('member_id', $memberId)->first();

                if ($user) {
                    $user->update([
                        'role' => 'member',
                        'can_approve_finances' => false,
                    ]);

                    \Log::info('User role updated to member (no active leadership positions)', [
                        'member_id' => $memberId,
                        'user_id' => $user->id,
                        'previous_role' => $user->getOriginal('role'),
                    ]);
                }
            } else {
                // Member still has active positions, update to the highest priority role
                $highestPriorityPosition = $activePositions->sortBy(function ($leader) {
                    $priority = [
                        'pastor' => 1,
                        'assistant_pastor' => 2,
                        'secretary' => 3,
                        'parish_worker' => 4,
                        'evangelism_leader' => 5,
                        'elder' => 6,
                        'assistant_secretary' => 7,
                        'treasurer' => 8,
                        'assistant_treasurer' => 9,
                    ];
                    return $priority[$leader->position] ?? 99;
                })->first();

                $role = $this->mapPositionToRole($highestPriorityPosition->position);

                if ($role) {
                    $user = \App\Models\User::where('member_id', $memberId)->first();

                    if ($user) {
                        $user->update([
                            'role' => $role,
                            'can_approve_finances' => in_array($role, ['pastor', 'admin']),
                        ]);

                        \Log::info('User role updated based on active leadership positions', [
                            'member_id' => $memberId,
                            'user_id' => $user->id,
                            'role' => $role,
                            'position' => $highestPriorityPosition->position,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update user role after leader removal', [
                'member_id' => $memberId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Send SMS notification for leader appointment with login credentials
     * Returns array with 'sent' (bool) and 'reason' (string) keys
     */
    private function sendLeaderAppointmentSms(Leader $leader, $userAccount = null)
    {
        try {
            // Ensure member relationship is loaded
            if (!$leader->relationLoaded('member')) {
                $leader->load('member');
            }

            // Check if member exists
            if (!$leader->member) {
                \Log::warning('Cannot send SMS: Leader has no associated member', [
                    'leader_id' => $leader->id
                ]);
                return [
                    'sent' => false,
                    'reason' => 'Leader has no associated member'
                ];
            }

            // Check if SMS notifications are enabled
            $smsEnabled = SettingsService::get('enable_sms_notifications', false);
            if (!$smsEnabled) {
                \Log::info('SMS notifications disabled, skipping leader appointment notification', [
                    'leader_id' => $leader->id,
                    'member_id' => $leader->member->id,
                    'member_name' => $leader->member->full_name
                ]);
                return [
                    'sent' => false,
                    'reason' => 'SMS notifications are disabled in system settings'
                ];
            }

            // Check if member has a phone number
            $phoneNumber = $leader->member->phone_number;
            if (empty($phoneNumber)) {
                \Log::info('Member has no phone number, skipping SMS notification for leader', [
                    'leader_id' => $leader->id,
                    'member_id' => $leader->member->id,
                    'member_name' => $leader->member->full_name
                ]);
                return [
                    'sent' => false,
                    'reason' => 'Member has no phone number'
                ];
            }

            // Get church name from settings (use same default as member registration)
            $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');

            // Build the message
            $message = "Hongera {$leader->member->full_name}! Umechaguliwa rasmi kuwa {$leader->position_display} wa kanisa la {$churchName}. ";

            // Add login credentials if user account was created/updated
            if ($userAccount) {
                $message .= "Akaunti yako imeundwa. Ingia kwa: Username: {$userAccount['username']}, Password: {$userAccount['password']}. ";
                $message .= "Tafadhali badilisha password baada ya kuingia. ";
            }

            $message .= "Mungu akupe hekima, ujasiri na neema katika kutimiza wajibu huu wa kiroho. Tunaamini uongozi wako utaleta umoja, upendo, na maendeleo katika huduma ya Bwana.";

            // Send SMS using the same method as member registration (sendDebug)
            $smsService = app(SmsService::class);
            $resp = $smsService->sendDebug($phoneNumber, $message);

            $smsSent = $resp['ok'] ?? false;
            $smsError = $resp['reason'] ?? ($resp['error'] ?? null);

            \Log::info('Leader appointment SMS attempt', [
                'leader_id' => $leader->id,
                'leader_name' => $leader->member->full_name,
                'position' => $leader->position_display,
                'phone' => $phoneNumber,
                'user_account_created' => $userAccount ? true : false,
                'sms_sent' => $smsSent,
                'ok' => $resp['ok'] ?? null,
                'status' => $resp['status'] ?? null,
                'body' => $resp['body'] ?? null,
                'reason' => $smsError,
                'error' => $resp['error'] ?? null,
            ]);

            if ($smsSent) {
                \Log::info('Leader appointment SMS sent successfully', [
                    'leader_id' => $leader->id,
                    'member_id' => $leader->member->id,
                    'phone' => $phoneNumber
                ]);
            } else {
                \Log::warning('Leader appointment SMS failed', [
                    'leader_id' => $leader->id,
                    'member_id' => $leader->member->id,
                    'phone' => $phoneNumber,
                    'error' => $smsError,
                    'response' => $resp
                ]);
            }

            return [
                'sent' => $smsSent,
                'reason' => $smsError ?? ($smsSent ? 'SMS sent successfully' : 'SMS sending failed'),
                'response' => $resp
            ];
        } catch (\Exception $e) {
            \Log::error('Error sending leader appointment SMS', [
                'leader_id' => $leader->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'sent' => false,
                'reason' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('leaders.change-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();

        // Verify current password
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->password = \Hash::make($request->new_password);
        $user->save();

        // Log the password change
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'password_changed',
                'description' => 'User changed their password',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => 'leader.password.update',
                'method' => 'POST',
            ]);
        } catch (\Exception $e) {
            // Silently continue if table doesn't exist
        }

        return redirect()->route('leader.change-password')
            ->with('success', 'Your password has been changed successfully!');
    }

    /**
     * Get available position options
     */
    private function getPositionOptions()
    {
        return [
            'pastor' => 'Pastor',
            'assistant_pastor' => 'Assistant Pastor',
            'secretary' => 'Secretary',
            'elder' => 'Church Elder',
            'parish_worker' => 'Parish Worker',
            'deacon' => 'Deacon',
            'deaconess' => 'Deaconess',
            'youth_leader' => 'Youth Leader',
            'children_leader' => 'Children Leader',
            'worship_leader' => 'Worship Leader',
            'choir_leader' => 'Choir Leader',
            'usher_leader' => 'Usher Leader',
            'evangelism_leader' => 'Evangelism Leader',
            'prayer_leader' => 'Prayer Leader',
            'other' => 'Other (Custom Position)'
        ];
    }
}
