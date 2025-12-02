<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\FailedLoginAttempt;
use App\Models\SystemLog;
use App\Models\BlockedIp;
use App\Services\SmsService;
use App\Services\SettingsService;
use App\Services\DeviceInfoService;
use App\Services\SystemMonitorService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access. Administrator privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_sessions' => DB::table('sessions')
                ->where('last_activity', '>', now()->subHours(24)->timestamp)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id'),
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
        ];

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Active sessions
        $activeSessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('sessions.last_activity', '>', now()->subHours(24)->timestamp)
            ->select('sessions.*', 'users.name', 'users.email', 'users.role')
            ->orderBy('sessions.last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $session->last_activity_formatted = Carbon::createFromTimestamp($session->last_activity)->diffForHumans();
                $session->is_current = $session->id === session()->getId();
                return $session;
            });

        // Activity by action type
        $activityByAction = ActivityLog::select('action', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Top active users
        $topActiveUsers = ActivityLog::select('user_id', DB::raw('count(*) as activity_count'))
            ->with('user:id,name,email,role')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'activeSessions',
            'activityByAction',
            'topActiveUsers'
        ));
    }

    /**
     * Display activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);
        
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action');

        // Check if called from unified logs view
        if ($request->has('type') || $request->get('unified', false)) {
            return view('admin.logs', [
                'logs' => $logs,
                'users' => $users,
                'actions' => $actions,
                'logType' => 'activity',
            ]);
        }

        return view('admin.activity-logs', compact('logs', 'users', 'actions'));
    }

    /**
     * Display user sessions
     * Shows all logged-in users regardless of role (admin, pastor, secretary, treasurer, member)
     */
    public function sessions(Request $request)
    {
        // First, try to sync any sessions that might not have user_id set
        // This helps catch sessions that were created before the middleware was added
        try {
            // Get all authenticated sessions from file system and sync to database
            // This is a fallback in case middleware didn't catch all sessions
            $allSessions = DB::table('sessions')->whereNull('user_id')->get();
            foreach ($allSessions as $session) {
                // Try to decode session payload to get user_id if stored in session data
                try {
                    $payload = unserialize(base64_decode($session->payload));
                    if (isset($payload['login_web_' . sha1('Illuminate\Auth\SessionGuard')])) {
                        $userId = $payload['login_web_' . sha1('Illuminate\Auth\SessionGuard')];
                        if ($userId) {
                            DB::table('sessions')
                                ->where('id', $session->id)
                                ->update(['user_id' => $userId]);
                        }
                    }
                } catch (\Exception $e) {
                    // Skip if can't decode
                }
            }
        } catch (\Exception $e) {
            // Silently continue
        }

        // Get all sessions with logged-in users - no role filtering
        // Using INNER JOIN to ensure we only get sessions with valid users
        $query = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id') // Only show sessions with logged-in users
            ->select('sessions.*', 'users.name', 'users.email', 'users.role');

        if ($request->filled('user_id')) {
            $query->where('sessions.user_id', $request->user_id);
        }

        if ($request->filled('active_only')) {
            $query->where('sessions.last_activity', '>', now()->subHours(24)->timestamp);
        }

        $sessions = $query->orderBy('sessions.last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $session->last_activity_formatted = Carbon::createFromTimestamp($session->last_activity)->format('Y-m-d H:i:s');
                $session->last_activity_human = Carbon::createFromTimestamp($session->last_activity)->diffForHumans();
                $session->is_current = $session->id === session()->getId();
                $session->is_active = $session->last_activity > now()->subHours(24)->timestamp;
                
                // Check if user is blocked from logging in
                $user = User::find($session->user_id);
                if ($user) {
                    $session->is_login_blocked = $user->isLoginBlocked();
                    $session->login_blocked_until = $user->login_blocked_until;
                    $session->remaining_block_time = $user->getRemainingBlockTime();
                    $session->remaining_block_time_formatted = $user->getRemainingBlockTimeFormatted();
                } else {
                    $session->is_login_blocked = false;
                    $session->login_blocked_until = null;
                    $session->remaining_block_time = null;
                    $session->remaining_block_time_formatted = null;
                }
                
                return $session;
            });

        // Get all users for the filter dropdown (all roles)
        $users = User::orderBy('name')->get();

        return view('admin.sessions', compact('sessions', 'users'));
    }

    /**
     * Revoke a session
     */
    public function revokeSession(Request $request, string $sessionId)
    {
        // Prevent revoking own session
        if ($sessionId === session()->getId()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot revoke your own active session.'
            ], 403);
        }

        // Get the user ID from the session before deleting it
        $session = DB::table('sessions')->where('id', $sessionId)->first();
        
        if (!$session || !$session->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or has no associated user.'
            ], 404);
        }

        $userId = $session->user_id;

        // Validate the request
        $request->validate([
            'blocked_until' => 'required|date|after:now',
        ], [
            'blocked_until.required' => 'Please specify when the user can login again.',
            'blocked_until.date' => 'Please provide a valid date and time.',
            'blocked_until.after' => 'The unblock time must be in the future.',
        ]);

        // Parse the datetime from datetime-local input
        // datetime-local sends time in the user's browser timezone (Tanzania)
        // We need to interpret it as Tanzania timezone (Africa/Dar_es_Salaam)
        $tanzaniaTimezone = 'Africa/Dar_es_Salaam';
        
        // Parse the datetime string assuming it's in Tanzania timezone
        // The datetime-local format is: YYYY-MM-DDTHH:mm (no timezone info)
        // We interpret this as Tanzania local time
        $blockedUntil = Carbon::createFromFormat('Y-m-d\TH:i', $request->blocked_until, $tanzaniaTimezone);
        
        // Laravel stores timestamps in UTC, so convert Tanzania time to UTC for storage
        // Use setTimezone('UTC') instead of utc() to avoid double conversion
        $blockedUntilForStorage = $blockedUntil->copy()->setTimezone('UTC');

        // Delete the session
        DB::table('sessions')->where('id', $sessionId)->delete();

        // Block user from logging in until specified time (stored in UTC)
        // Use update() with fresh() to ensure the change is saved immediately
        $affected = User::where('id', $userId)->update([
            'login_blocked_until' => $blockedUntilForStorage
        ]);
        
        // Verify the block was saved (for debugging)
        $verifyUser = User::find($userId);
        if (!$verifyUser || !$verifyUser->login_blocked_until) {
            \Log::error('Failed to save login_blocked_until', [
                'user_id' => $userId,
                'blocked_until' => $blockedUntilForStorage->format('Y-m-d H:i:s'),
                'affected_rows' => $affected
            ]);
        }

        // Log this activity
        try {
            $user = User::find($userId);
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'revoke',
                'description' => "Revoked session for {$user->name} (ID: {$userId}) and blocked login until {$blockedUntil->format('Y-m-d H:i:s')}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        $user = User::find($userId);
        // Use Tanzania timezone explicitly
        $tanzaniaTimezone = 'Africa/Dar_es_Salaam';
        $appTimezone = $tanzaniaTimezone; // For backward compatibility
        
        // Get the raw timestamp from database to avoid timezone conversion issues
        $rawBlockedUntil = DB::table('users')
            ->where('id', $userId)
            ->value('login_blocked_until');
        
        if ($rawBlockedUntil) {
            // Parse as UTC directly (how it's stored in database)
            $blockedUntilUtc = Carbon::createFromFormat('Y-m-d H:i:s', $rawBlockedUntil, 'UTC');
            $now = Carbon::now('UTC');
            
            // Convert to Tanzania timezone for display
            $blockedUntilDisplay = $blockedUntilUtc->copy()->setTimezone($appTimezone);
            $blockedUntilFormatted = $blockedUntilDisplay->format('Y-m-d H:i:s');
            
            // Calculate the actual time remaining from now (both in UTC)
            // diffInMinutes returns: $this - $other
            // So blockedUntilUtc->diffInMinutes($now, false) = blockedUntil - now
            // Positive if blockedUntil is in the future, negative if in the past
            $remainingMinutes = (int)$blockedUntilUtc->diffInMinutes($now, false);
            
            // Only show positive remaining time
            if ($remainingMinutes <= 0) {
                $blockedUntilHuman = "now (block has expired)";
            } elseif ($remainingMinutes < 60) {
                $blockedUntilHuman = "{$remainingMinutes} minute(s) from now";
            } elseif ($remainingMinutes < 1440) {
                $hours = floor($remainingMinutes / 60);
                $minutes = $remainingMinutes % 60;
                if ($minutes > 0) {
                    $blockedUntilHuman = "{$hours} hour(s) and {$minutes} minute(s) from now";
                } else {
                    $blockedUntilHuman = "{$hours} hour(s) from now";
                }
            } else {
                $days = floor($remainingMinutes / 1440);
                $hours = floor(($remainingMinutes % 1440) / 60);
                if ($hours > 0) {
                    $blockedUntilHuman = "{$days} day(s) and {$hours} hour(s) from now";
                } else {
                    $blockedUntilHuman = "{$days} day(s) from now";
                }
            }
        } else {
            $blockedUntilFormatted = 'N/A';
            $blockedUntilHuman = 'N/A';
            $remainingMinutes = 0;
        }
        
        return response()->json([
            'success' => true,
            'message' => "Session revoked successfully. {$user->name} cannot login until {$blockedUntilFormatted} ({$blockedUntilHuman}).",
            'blocked_until' => $blockedUntilFormatted,
            'blocked_until_human' => $blockedUntilHuman,
        ]);
    }

    /**
     * Display user management
     * Only shows leaders (pastor, secretary, treasurer) and administrators
     * Regular members are managed in the member management page
     */
    public function users()
    {
        $users = User::withCount('activityLogs')
            ->whereIn('role', ['admin', 'pastor', 'secretary', 'treasurer'])
            ->orderBy('role')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $user->is_login_blocked = $user->isLoginBlocked();
                $user->remaining_block_time = $user->getRemainingBlockTime();
                return $user;
            });

        return view('admin.users', compact('users'));
    }

    /**
     * Show the form for creating a new user
     * Only members who are leaders can have user accounts created
     */
    public function create()
    {
        // Get member IDs that already have user accounts with leader/admin roles
        // Regular member accounts (role='member') don't count - they're separate
        $membersWithUsers = User::whereNotNull('member_id')
            ->whereIn('role', ['admin', 'pastor', 'secretary', 'treasurer'])
            ->pluck('member_id')
            ->toArray();
        
        // Debug: Get all leaders first to see what we're working with
        $allLeaders = \App\Models\Leader::with('member')
            ->whereIn('position', ['pastor', 'assistant_pastor', 'secretary', 'assistant_secretary', 'treasurer', 'assistant_treasurer'])
            ->get();
        
        // Debug: Log for troubleshooting
        Log::info('User creation - All leaders found', [
            'total_leaders' => $allLeaders->count(),
            'leaders_details' => $allLeaders->map(function($l) use ($membersWithUsers) {
                return [
                    'id' => $l->id,
                    'position' => $l->position,
                    'is_active' => $l->is_active,
                    'member_id' => $l->member_id,
                    'member_name' => $l->member ? $l->member->full_name : 'NO MEMBER',
                    'has_user_account' => in_array($l->member_id, $membersWithUsers),
                    'member_has_user' => $l->member && $l->member->user ? 'YES' : 'NO'
                ];
            })->toArray(),
            'members_with_users' => $membersWithUsers
        ]);
        
        // Get active leaders with their member information
        // Only show leaders who don't already have a user account
        // Show all active leaders regardless of end_date (admin can still create account)
        $leaders = \App\Models\Leader::with('member')
            ->where('is_active', true)
            ->whereNotIn('member_id', $membersWithUsers)
            ->whereIn('position', ['pastor', 'assistant_pastor', 'secretary', 'assistant_secretary', 'treasurer', 'assistant_treasurer'])
            ->whereHas('member') // Ensure member relationship exists
            ->orderBy('position')
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function($leader) {
                // Skip if member relationship is missing
                if (!$leader->member) {
                    return null;
                }
                
                // Map leader position to user role
                $role = match($leader->position) {
                    'pastor', 'assistant_pastor' => 'pastor',
                    'secretary', 'assistant_secretary' => 'secretary',
                    'treasurer', 'assistant_treasurer' => 'treasurer',
                    default => null
                };
                
                return [
                    'id' => $leader->id,
                    'member_id' => $leader->member_id,
                    'member_name' => $leader->member->full_name ?? 'Unknown',
                    'member_email' => $leader->member->email ?? '',
                    'member_phone' => $leader->member->phone_number ?? '',
                    'position' => $leader->position,
                    'position_display' => $leader->position_display,
                    'role' => $role,
                    'appointment_date' => $leader->appointment_date ? $leader->appointment_date->format('Y-m-d') : '',
                    'end_date' => $leader->end_date ? $leader->end_date->format('Y-m-d') : null,
                ];
            })
            ->filter(function($leader) {
                // Only include positions that map to user roles and have valid data
                return $leader !== null && $leader['role'] !== null;
            })
            ->values(); // Re-index array after filtering
        
        // Debug: Log final leaders that will be shown
        Log::info('User creation - Final leaders to display', [
            'count' => $leaders->count(),
            'leaders' => $leaders->toArray()
        ]);

        // For admin accounts, we still allow direct creation (not tied to a member/leader)
        $allowAdminCreation = true;

        return view('admin.users.create', compact('leaders', 'allowAdminCreation'));
    }

    /**
     * Store a newly created user
     * For leaders: must be a member who is appointed as a leader
     * For admin: can be created directly (not tied to a member)
     */
    public function store(Request $request)
    {
        // Debug: Log the request
        Log::info('User store method called', [
            'all_input' => $request->all(),
            'account_type' => $request->input('account_type'),
            'leader_id' => $request->input('leader_id'),
            'email' => $request->input('email'),
            'has_email' => $request->has('email'),
        ]);
        
        // Check if creating for a leader (member-based) or admin (direct creation)
        $isAdminCreation = $request->input('account_type') === 'admin';
        
        if ($isAdminCreation) {
            // Direct admin creation (not tied to a member)
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone_number' => [
                    'nullable',
                    'string',
                    'max:20',
                    function ($attribute, $value, $fail) {
                        if (!empty($value)) {
                            // Format phone number for validation
                            $phone = trim($value);
                            // Remove any existing +255 prefix
                            $phone = preg_replace('/^\+255/', '', $phone);
                            // Remove any leading zeros
                            $phone = ltrim($phone, '0');
                            // Prepend +255
                            $formattedPhone = '+255' . $phone;
                            
                            // Check if this formatted phone number already exists
                            if (User::where('phone_number', $formattedPhone)->exists()) {
                                $fail('This phone number is already in use by another user. Each user must have a unique phone number.');
                            }
                        }
                    },
                ],
            ]);
            
            $role = 'admin';
            $memberId = null;
            $userName = $validated['name'];
            $userEmail = $validated['email'];
            $phoneNumber = null;
            if (!empty($validated['phone_number'])) {
                $phone = trim($validated['phone_number']);
                $phone = preg_replace('/^\+255/', '', $phone);
                $phone = ltrim($phone, '0');
                $phoneNumber = '+255' . $phone;
            }
        } else {
            // Leader creation (must be tied to a member who is a leader)
            // Email is required for leader accounts
            // Check if email is provided before validation
            $emailProvided = $request->filled('email') && trim($request->input('email')) !== '';
            
            Log::info('Before validation check', [
                'email_provided' => $emailProvided,
                'email_value' => $request->input('email'),
                'has_email' => $request->has('email'),
                'all_input_keys' => array_keys($request->all()),
            ]);
            
            $validated = $request->validate([
                'leader_id' => 'required|exists:leaders,id',
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                ],
            ], [
                'leader_id.required' => 'Please select a leader from the dropdown.',
                'leader_id.exists' => 'The selected leader is invalid.',
                'email.required' => 'Email address is required. Please enter an email address for the user account.',
                'email.email' => 'Please enter a valid email address.',
            ]);
            
            $leader = \App\Models\Leader::with('member')->findOrFail($validated['leader_id']);
            
            // Verify leader doesn't already have a leader/admin user account
            // Regular member accounts (role='member') are allowed - a person can have both
            $existingUser = $leader->member->user;
            if ($existingUser && in_array($existingUser->role, ['admin', 'pastor', 'secretary', 'treasurer'])) {
                return redirect()->back()
                    ->with('error', 'This leader already has a user account with role: ' . ucfirst($existingUser->role) . '.')
                    ->withInput();
            }
            
            // Map leader position to user role
            $role = match($leader->position) {
                'pastor', 'assistant_pastor' => 'pastor',
                'secretary', 'assistant_secretary' => 'secretary',
                'treasurer', 'assistant_treasurer' => 'treasurer',
                default => null
            };
            
            if (!$role) {
                return redirect()->back()
                    ->with('error', 'This leadership position does not require a user account.')
                    ->withInput();
            }
            
            // Get member information
            $member = $leader->member;
            $memberId = $member->id;
            $userName = $member->full_name;
            
            // Get email from validated data
            $userEmail = $validated['email'];
            
            // Debug: Log email value
            Log::info('Email from validation', [
                'email' => $userEmail,
                'email_empty' => empty($userEmail),
                'email_trimmed' => trim($userEmail ?? ''),
            ]);
            
            // Validate email uniqueness - allow same email if it's the member's own regular account
            $emailValidation = Validator::make(['email' => $userEmail], [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) use ($existingUser) {
                        // If member has a regular member account, allow using the same email
                        $emailExists = User::where('email', $value)
                            ->where(function($query) use ($existingUser) {
                                if ($existingUser && $existingUser->role === 'member') {
                                    // Exclude the member's own regular account
                                    $query->where('id', '!=', $existingUser->id);
                                }
                            })
                            ->exists();
                        
                        if ($emailExists) {
                            $fail('This email address is already in use by another user account.');
                        }
                    },
                ],
            ]);
            
            if ($emailValidation->fails()) {
                return redirect()->back()
                    ->withErrors($emailValidation)
                    ->withInput();
            }
            
            // Format phone number from member
            $phoneNumber = null;
            if (!empty($member->phone_number)) {
                $phone = trim($member->phone_number);
                $phone = preg_replace('/^\+255/', '', $phone);
                $phone = ltrim($phone, '0');
                $phoneNumber = '+255' . $phone;
            }
            
            // Validate phone number uniqueness if provided
            // Allow same phone if it's the same member's regular account
            if ($phoneNumber) {
                $existingUserWithPhone = User::where('phone_number', $phoneNumber)
                    ->where(function($query) use ($existingUser, $memberId) {
                        // Exclude member's own regular account if it exists
                        if ($existingUser && $existingUser->role === 'member') {
                            $query->where('id', '!=', $existingUser->id);
                        }
                        // Exclude if it's the same member's other account
                        $query->where(function($q) use ($memberId) {
                            $q->whereNull('member_id')
                              ->orWhere('member_id', '!=', $memberId);
                        });
                    })
                    ->first();
                
                if ($existingUserWithPhone) {
                    return redirect()->back()
                        ->with('error', 'This phone number is already in use by another user.')
                        ->withInput();
                }
            }
        }

        // Generate a strong password automatically
        $generatedPassword = $this->generateStrongPassword();

        // Debug: Log before user creation
        Log::info('About to create user', [
            'name' => $userName,
            'email' => $userEmail,
            'role' => $role,
            'member_id' => $memberId,
            'phone_number' => $phoneNumber,
        ]);

        // Create the user
        try {
            $user = User::create([
                'name' => $userName,
                'email' => $userEmail,
                'password' => Hash::make($generatedPassword),
                'role' => $role,
                'phone_number' => $phoneNumber,
                'member_id' => $memberId,
                'can_approve_finances' => $role === 'pastor' || $role === 'admin',
            ]);
            
            Log::info('User created successfully', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create user account: ' . $e->getMessage())
                ->withInput();
        }

        // Send SMS notification with credentials
        $smsSent = false;
        $smsError = null;
        $smsReason = null;
        
        if (!empty($phoneNumber)) {
            try {
                // Check if SMS is enabled first
                $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                if (!$smsEnabled) {
                    $smsError = 'SMS notifications are disabled in system settings';
                    $smsReason = 'disabled';
                    Log::info('SMS not sent - notifications disabled', [
                        'user_id' => $user->id,
                        'phone' => $phoneNumber
                    ]);
                } else {
                    $smsService = app(SmsService::class);
                    $churchName = SettingsService::get('church_name', 'AIC Moshi Kilimanjaro');
                    
                    $roleLabel = ucfirst($role);
                    $message = "Hongera {$user->name}! Akaunti yako ya {$roleLabel} imeundwa kikamilifu kwenye mfumo wa {$churchName}.\n\n";
                    $message .= "Unaweza kuingia kwenye akaunti yako kwa kutumia:\n";
                    $message .= "Username: {$user->email}\n";
                    $message .= "Password: {$generatedPassword}\n\n";
                    $message .= "Tafadhali badilisha nenosiri baada ya kuingia kwa mara ya kwanza. Mungu akubariki!";
                    
                    // Use sendDebug to get detailed response
                    $smsResult = $smsService->sendDebug($phoneNumber, $message);
                    $smsSent = $smsResult['ok'] ?? false;
                    $smsReason = $smsResult['reason'] ?? null;
                    
                    if ($smsSent) {
                        Log::info('User account credentials SMS sent successfully', [
                            'user_id' => $user->id,
                            'phone' => $phoneNumber,
                            'response' => $smsResult
                        ]);
                    } else {
                        // Determine error message based on reason
                        switch ($smsReason) {
                            case 'disabled':
                                $smsError = 'SMS notifications are disabled';
                                break;
                            case 'config_missing':
                                $smsError = 'SMS configuration is missing (username/password or API key)';
                                break;
                            default:
                                $smsError = $smsResult['error'] ?? $smsResult['body'] ?? 'Unknown error occurred';
                                break;
                        }
                        
                        Log::warning('User account credentials SMS failed', [
                            'user_id' => $user->id,
                            'phone' => $phoneNumber,
                            'response' => $smsResult,
                            'reason' => $smsReason
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $smsError = 'Exception: ' . $e->getMessage();
                Log::error('Error sending user credentials SMS: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'phone' => $phoneNumber,
                    'exception' => $e->getTraceAsString()
                ]);
            }
        } else {
            $smsError = 'No phone number provided';
            Log::info('SMS not sent - no phone number provided', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Created new user account: {$user->name} ({$user->email}) with role: {$role}. Password generated automatically. SMS " . ($smsSent ? 'sent' : 'not sent'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        // Store credentials in session for SweetAlert popup
        return redirect()->route('admin.users')
            ->with('success', "User account for {$user->name} has been created successfully.")
            ->with('user_created', true)
            ->with('user_name', $user->name)
            ->with('user_email', $user->email)
            ->with('user_password', $generatedPassword)
            ->with('user_role', $role)
            ->with('sms_sent', $smsSent)
            ->with('sms_error', $smsError)
            ->with('sms_reason', $smsReason)
            ->with('phone_number', $phoneNumber);
    }

    /**
     * Generate a strong random password that meets all requirements
     * Minimum 12 characters, uppercase, lowercase, numbers, symbols
     */
    private function generateStrongPassword($length = 16)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        // Ensure at least one character from each set
        $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Fill the rest randomly from all character sets
        $all = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Shuffle to randomize position
        return str_shuffle($password);
    }

    /**
     * Unblock a user from logging in
     */
    public function unblockUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update(['login_blocked_until' => null]);

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'unblock',
                'description' => "Manually unblocked user: {$user->name} (ID: {$userId})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        // Return JSON response for AJAX requests (from sessions page)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "User {$user->name} has been unblocked and can now login."
            ]);
        }

        // Return redirect for regular form submissions (from users page)
        return back()->with('success', "User {$user->name} has been unblocked and can now login.");
    }

    /**
     * Display roles and permissions
     */
    public function rolesPermissions()
    {
        $roles = ['admin', 'pastor', 'secretary', 'treasurer'];
        $permissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');

        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role] = DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('role_permissions.role', $role)
                ->pluck('permissions.slug')
                ->toArray();
        }

        return view('admin.roles-permissions', compact('roles', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,pastor,secretary,treasurer',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,slug',
        ]);

        $role = $request->role;
        $permissionSlugs = $request->permissions ?? [];

        // Get permission IDs
        $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id');

        // Delete existing permissions for this role
        DB::table('role_permissions')->where('role', $role)->delete();

        // Insert new permissions
        foreach ($permissionIds as $permissionId) {
            DB::table('role_permissions')->insert([
                'role' => $role,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Log this activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "Updated permissions for role: {$role}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->route()->getName(),
            'method' => $request->method(),
        ]);

        return back()->with('success', "Permissions updated successfully for {$role} role.");
    }

    /**
     * View user activity
     */
    public function userActivity($userId)
    {
        $user = User::findOrFail($userId);
        $activities = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.user-activity', compact('user', 'activities'));
    }

    /**
     * Reset user password - Admin only
     * Generates a new password and optionally sends it via SMS
     */
    public function resetPassword(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Generate a strong password automatically
            $newPassword = $this->generateStrongPassword();
            
            // Update password
            $user->password = Hash::make($newPassword);
            $user->save();

            // Log the password reset
            Log::info('User password reset by admin', [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);

            // Send SMS with new password if phone number exists
            $smsSent = false;
            $smsError = null;
            $smsReason = null;
            
            if (!empty($user->phone_number)) {
                try {
                    // Check if SMS is enabled first
                    $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                    if (!$smsEnabled) {
                        $smsError = 'SMS notifications are disabled in system settings';
                        $smsReason = 'disabled';
                        Log::info('SMS not sent - notifications disabled', [
                            'user_id' => $user->id,
                            'phone' => $user->phone_number
                        ]);
                    } else {
                        $smsService = app(SmsService::class);
                        $churchName = SettingsService::get('church_name', 'AIC Moshi Kilimanjaro');
                        
                        $roleLabel = ucfirst($user->role);
                        $message = "Shalom {$user->name}, nenosiri lako jipya la akaunti yako ya {$roleLabel} ni: {$newPassword}.\n\n";
                        $message .= "Tafadhali badilisha nenosiri baada ya kuingia kwa mara ya kwanza. Mungu akubariki!";
                        
                        // Use sendDebug to get detailed response
                        $smsResult = $smsService->sendDebug($user->phone_number, $message);
                        $smsSent = $smsResult['ok'] ?? false;
                        $smsReason = $smsResult['reason'] ?? null;
                        
                        if ($smsSent) {
                            Log::info('User password reset SMS sent successfully', [
                                'user_id' => $user->id,
                                'phone' => $user->phone_number,
                                'response' => $smsResult
                            ]);
                        } else {
                            // Determine error message based on reason
                            switch ($smsReason) {
                                case 'disabled':
                                    $smsError = 'SMS notifications are disabled';
                                    break;
                                case 'config_missing':
                                    $smsError = 'SMS configuration is missing (username/password or API key)';
                                    break;
                                default:
                                    $smsError = $smsResult['error'] ?? $smsResult['body'] ?? 'Unknown error occurred';
                                    break;
                            }
                            
                            Log::warning('User password reset SMS failed', [
                                'user_id' => $user->id,
                                'phone' => $user->phone_number,
                                'response' => $smsResult,
                                'reason' => $smsReason
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $smsError = 'Exception: ' . $e->getMessage();
                    Log::error('Error sending user password reset SMS: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'phone' => $user->phone_number,
                        'exception' => $e->getTraceAsString()
                    ]);
                }
            } else {
                $smsError = 'No phone number provided';
                Log::info('SMS not sent - no phone number provided', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            // Log this activity
            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'reset_password',
                    'description' => "Reset password for user: {$user->name} ({$user->email}) with role: {$user->role}. SMS " . ($smsSent ? 'sent' : 'not sent'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'route' => $request->route()->getName(),
                    'method' => $request->method(),
                ]);
            } catch (\Exception $e) {
                // Silently continue if logging fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
                'password' => $newPassword, // Return password so admin can see/copy it
                'sms_sent' => $smsSent,
                'sms_error' => $smsError,
                'sms_reason' => $smsReason,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'phone_number' => $user->phone_number
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting user password: ' . $e->getMessage(), [
                'user_id' => $userId,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing a user
     */
    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        
        $roles = [
            'pastor' => 'Pastor',
            'secretary' => 'Secretary',
            'treasurer' => 'Treasurer',
            'admin' => 'Administrator',
        ];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update a user
     */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'role' => 'required|in:admin,pastor,secretary,treasurer',
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($userId) {
                    if (!empty($value)) {
                        // Format phone number for validation
                        $phone = trim($value);
                        // Remove any existing +255 prefix
                        $phone = preg_replace('/^\+255/', '', $phone);
                        // Remove any leading zeros
                        $phone = ltrim($phone, '0');
                        // Prepend +255
                        $formattedPhone = '+255' . $phone;
                        
                        // Check if this formatted phone number already exists (excluding current user)
                        if (User::where('phone_number', $formattedPhone)->where('id', '!=', $userId)->exists()) {
                            $fail('This phone number is already in use by another user. Each user must have a unique phone number.');
                        }
                    }
                },
            ],
            'can_approve_finances' => 'nullable|boolean',
        ]);

        // Format phone number: prepend +255 if not already present
        $phoneNumber = null;
        if (!empty($validated['phone_number'])) {
            $phone = trim($validated['phone_number']);
            // Remove any existing +255 prefix
            $phone = preg_replace('/^\+255/', '', $phone);
            // Remove any leading zeros
            $phone = ltrim($phone, '0');
            // Prepend +255
            $phoneNumber = '+255' . $phone;
        }

        // Update the user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone_number' => $phoneNumber,
            'can_approve_finances' => $validated['can_approve_finances'] ?? false,
        ]);

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Updated user account: {$user->name} ({$user->email}) with role: {$user->role}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        return redirect()->route('admin.users')
            ->with('success', "User account for {$user->name} has been updated successfully.");
    }

    /**
     * Delete a user
     */
    public function destroy(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        // Prevent deleting the last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last administrator account.'
                ], 403);
            }
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // Delete the user
        $user->delete();

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Deleted user account: {$userName} ({$userEmail})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        return response()->json([
            'success' => true,
            'message' => "User account for {$userName} has been deleted successfully."
        ]);
    }

    /**
     * Display unified logs page with dropdown
     */
    public function logs(Request $request)
    {
        $logType = $request->get('type', 'activity'); // activity, system, failed-login
        
        // Merge type parameter into request so child methods know they're called from unified view
        $request->merge(['type' => $logType, 'unified' => true]);
        
        if ($logType === 'system') {
            return $this->systemLogs($request);
        } elseif ($logType === 'failed-login') {
            return $this->failedLoginLogs($request);
        } else {
            return $this->activityLogs($request);
        }
    }

    /**
     * Display system logs with device properties
     */
    public function systemLogs(Request $request)
    {
        $query = SystemLog::with('user');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);
        
        $users = User::orderBy('name')->get();
        $levels = SystemLog::distinct()->pluck('level');
        $categories = SystemLog::distinct()->pluck('category')->filter();

        return view('admin.logs', [
            'logs' => $logs,
            'users' => $users,
            'levels' => $levels,
            'categories' => $categories,
            'logType' => 'system',
        ]);
    }

    /**
     * Display failed login logs
     */
    public function failedLoginLogs(Request $request)
    {
        $query = FailedLoginAttempt::query();

        // Filters
        if ($request->filled('email')) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', "%{$request->ip_address}%");
        }

        if ($request->filled('blocked_only')) {
            $query->where('ip_blocked', true);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);
        
        $blockedIps = BlockedIp::active()->pluck('ip_address')->toArray();

        return view('admin.logs', [
            'logs' => $logs,
            'blockedIps' => $blockedIps ?? [],
            'logType' => 'failed-login',
        ]);
    }

    /**
     * Block an IP address
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:500',
        ]);

        $ipAddress = $request->ip_address;
        $reason = $request->reason ?? 'Blocked due to failed login attempts';

        // Check if already blocked
        $existingBlock = BlockedIp::where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->first();

        if ($existingBlock) {
            return response()->json([
                'success' => false,
                'message' => 'IP address is already blocked.',
            ], 400);
        }

        // Create block record
        BlockedIp::create([
            'ip_address' => $ipAddress,
            'reason' => $reason,
            'blocked_by' => Auth::id(),
            'blocked_at' => now(),
            'is_active' => true,
        ]);

        // Update failed login attempts for this IP
        FailedLoginAttempt::where('ip_address', $ipAddress)
            ->where('ip_blocked', false)
            ->update([
                'ip_blocked' => true,
                'ip_blocked_at' => now(),
            ]);

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'block_ip',
                'description' => "Blocked IP address: {$ipAddress}. Reason: {$reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        return response()->json([
            'success' => true,
            'message' => "IP address {$ipAddress} has been blocked successfully.",
        ]);
    }

    /**
     * Unblock an IP address
     */
    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $ipAddress = $request->ip_address;

        // Find and deactivate block
        $block = BlockedIp::where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->first();

        if (!$block) {
            return response()->json([
                'success' => false,
                'message' => 'IP address is not currently blocked.',
            ], 400);
        }

        $block->update([
            'is_active' => false,
            'unblocked_at' => now(),
        ]);

        // Update failed login attempts for this IP
        FailedLoginAttempt::where('ip_address', $ipAddress)
            ->where('ip_blocked', true)
            ->update([
                'ip_blocked' => false,
                'ip_unblocked_at' => now(),
            ]);

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'unblock_ip',
                'description' => "Unblocked IP address: {$ipAddress}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
        }

        return response()->json([
            'success' => true,
            'message' => "IP address {$ipAddress} has been unblocked successfully.",
        ]);
    }

    /**
     * Get device details for a system log
     */
    public function getDeviceDetails($logId)
    {
        $log = SystemLog::findOrFail($logId);
        
        return response()->json([
            'device_type' => $log->device_type,
            'device_name' => $log->device_name,
            'browser' => $log->browser,
            'os' => $log->os,
            'mac_address' => $log->mac_address,
            'screen_resolution' => $log->screen_resolution,
            'timezone' => $log->timezone,
            'language' => $log->language,
            'device_properties' => $log->device_properties,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
        ]);
    }

    /**
     * Display system monitoring page
     */
    public function systemMonitor()
    {
        return view('admin.system-monitor');
    }

    /**
     * Get system information (AJAX endpoint)
     */
    public function getSystemInfo()
    {
        try {
            $systemInfo = SystemMonitorService::getSystemInfo();
            
            return response()->json([
                'success' => true,
                'data' => $systemInfo,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get system info: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve system information: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache(Request $request)
    {
        try {
            $cacheType = $request->input('type', 'all');
            $results = [];
            $errors = [];

            // Helper function to safely call Artisan commands
            $safeArtisanCall = function($command, $successMessage) use (&$results, &$errors) {
                try {
                    Artisan::call($command);
                    $results[$command] = $successMessage;
                    return true;
                } catch (\Exception $e) {
                    $errors[$command] = $e->getMessage();
                    Log::warning("Failed to execute artisan command {$command}: " . $e->getMessage());
                    return false;
                }
            };

            switch ($cacheType) {
                case 'all':
                    $safeArtisanCall('cache:clear', 'Application cache cleared');
                    $safeArtisanCall('config:clear', 'Configuration cache cleared');
                    $safeArtisanCall('route:clear', 'Route cache cleared');
                    $safeArtisanCall('view:clear', 'View cache cleared');
                    
                    // Try optimize:clear, but don't fail if it doesn't exist
                    try {
                        Artisan::call('optimize:clear');
                        $results['optimize'] = 'Optimization cache cleared';
                    } catch (\Exception $e) {
                        // Command might not exist in older Laravel versions
                        Log::info('optimize:clear command not available: ' . $e->getMessage());
                        $results['optimize'] = 'Optimization cache skipped (command not available)';
                    }
                    
                    // Clear Laravel cache
                    try {
                        Cache::flush();
                        $results['laravel'] = 'Laravel cache flushed';
                    } catch (\Exception $e) {
                        $errors['laravel'] = $e->getMessage();
                        Log::warning('Failed to flush Laravel cache: ' . $e->getMessage());
                    }
                    break;
                    
                case 'application':
                    if (!$safeArtisanCall('cache:clear', 'Application cache cleared')) {
                        throw new \Exception('Failed to clear application cache');
                    }
                    break;
                    
                case 'config':
                    if (!$safeArtisanCall('config:clear', 'Configuration cache cleared')) {
                        throw new \Exception('Failed to clear config cache');
                    }
                    break;
                    
                case 'route':
                    if (!$safeArtisanCall('route:clear', 'Route cache cleared')) {
                        throw new \Exception('Failed to clear route cache');
                    }
                    break;
                    
                case 'view':
                    if (!$safeArtisanCall('view:clear', 'View cache cleared')) {
                        throw new \Exception('Failed to clear view cache');
                    }
                    break;
                    
                case 'optimize':
                    try {
                        Artisan::call('optimize:clear');
                        $results['optimize'] = 'Optimization cache cleared';
                    } catch (\Exception $e) {
                        throw new \Exception('Failed to clear optimization cache: ' . $e->getMessage());
                    }
                    break;
                    
                case 'laravel':
                    try {
                        Cache::flush();
                        $results['laravel'] = 'Laravel cache flushed';
                    } catch (\Exception $e) {
                        throw new \Exception('Failed to flush Laravel cache: ' . $e->getMessage());
                    }
                    break;
                    
                default:
                    throw new \Exception('Invalid cache type: ' . $cacheType);
            }

            // Log the action
            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'clear_cache',
                    'description' => "Cleared {$cacheType} cache",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // Don't fail if logging fails
                Log::warning('Failed to log cache clear action: ' . $e->getMessage());
            }

            // Prepare response
            $responseData = [
                'success' => true,
                'message' => 'Cache cleared successfully',
                'results' => $results,
            ];

            // Include warnings if any
            if (!empty($errors)) {
                $responseData['warnings'] = $errors;
                $responseData['message'] = 'Cache cleared with some warnings';
            }

            // Always return JSON for AJAX/JSON requests
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE);
            }

            return redirect()->route('admin.system-monitor')
                ->with('success', $responseData['message'])
                ->with('cache_results', $results)
                ->with('cache_warnings', $errors);
                
        } catch (\Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'cache_type' => $request->input('type', 'all'),
            ]);
            
            $errorMessage = 'Failed to clear cache: ' . $e->getMessage();
            
            // Always return JSON for AJAX/JSON requests
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $e->getMessage(),
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }

            return redirect()->route('admin.system-monitor')
                ->with('error', $errorMessage);
        }
    }
}

