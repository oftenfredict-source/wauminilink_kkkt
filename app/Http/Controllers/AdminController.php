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
            // Add 'evangelism_leader' and 'elder' to the list of roles to show
            ->whereIn('role', ['admin', 'pastor', 'secretary', 'treasurer', 'evangelism_leader', 'elder'])
            // OR include users who are members but also active leaders (backward compatibility)
            ->orWhere(function($query) {
                $query->where('role', 'member')
                      ->whereHas('member.activeLeadershipPositions', function($q) {
                          $q->whereIn('position', ['evangelism_leader', 'elder', 'deacon', 'deaconess', 'presiding_elder']);
                      });
            })
            // Optimize by eager loading the member and their active leadership positions
            ->with(['member.activeLeadershipPositions' => function($query) {
                // Determine which positions to fetch - we'll sort them in PHP
                $query->whereIn('position', ['evangelism_leader', 'elder', 'deacon', 'deaconess', 'presiding_elder']);
            }])
            ->orderBy('role')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $user->is_login_blocked = $user->isLoginBlocked();
                $user->remaining_block_time = $user->getRemainingBlockTime();
                
                // Determine display role and leader status
                // Default values
                $user->display_role = ucfirst(str_replace('_', ' ', $user->role));
                $user->is_leader_member = false;
                $user->leader_position = null;
                
                // If user has specific leader role (elder/evangelism_leader), set display properly
                if (in_array($user->role, ['elder', 'evangelism_leader'])) {
                     $user->is_leader_member = true; // Treat as leader member for badge styling
                     $user->leader_position = $user->role;
                     $user->display_role = $user->role === 'evangelism_leader' ? 'Evangelism Leader' : 'Church Elder';
                }
                // Handle 'member' role users who are actually leaders (Legacy/Mixed accounts)
                elseif ($user->role === 'member' && $user->member && $user->member->activeLeadershipPositions->isNotEmpty()) {
                    // Logic remains the same for member-role leaders
                    $positions = $user->member->activeLeadershipPositions;
                    
                    // Priority map
                    $priority = [
                        'evangelism_leader' => 10,
                        'elder' => 20,
                        'presiding_elder' => 15,
                        'deacon' => 5,
                        'deaconess' => 5
                    ];
                    
                    $highestPriority = 0;
                    $bestPosition = null;
                    
                    foreach ($positions as $pos) {
                        $p = $priority[$pos->position] ?? 0;
                        if ($p > $highestPriority) {
                            $highestPriority = $p;
                            $bestPosition = $pos;
                        }
                    }
                    
                    if ($bestPosition) {
                        $user->is_leader_member = true;
                        $user->leader_position = $bestPosition->position;
                        // Use the nice display name if available, otherwise format the position code
                        $user->display_role = $bestPosition->position_display ?? ucfirst(str_replace('_', ' ', $bestPosition->position));
                    }
                }
                
                return $user;
            })
            // Verify and deduplicate users
            // If a member has BOTH a 'member' account and a 'leader' (elder/evangelism_leader) account,
            // we should only show the 'leader' account (to avoid duplicates).
            ->groupBy('member_id')
            ->flatMap(function($groupedUsers) {
                // If only one user for this member_id, return it
                if ($groupedUsers->count() <= 1) {
                    return $groupedUsers;
                }
                
                // If multiple users, check if we have a specific leader account
                $leaderAccount = $groupedUsers->first(function($u) {
                    return in_array($u->role, ['elder', 'evangelism_leader', 'pastor', 'secretary', 'treasurer', 'admin']);
                });
                
                // If we found a leader account, use that one and ignore the generic 'member' account
                if ($leaderAccount) {
                    return collect([$leaderAccount]);
                }
                
                // Otherwise return all (fallback)
                return $groupedUsers;
            })
            ->values(); // Re-index keys

        return view('admin.users', compact('users'));
    }

    /**
     * Show the form for creating a new user
     * Only members who are leaders can have user accounts created
     */
    public function create()
    {
        // Get member IDs that already have user accounts with specific leader roles
    // We only exclude members who ALREADY have a user account for their specific leadership role
    // This allows a member to have a 'member' account AND an 'elder' account
    $existingLeaderUserIds = User::whereNotNull('member_id')
        ->whereIn('role', ['admin', 'pastor', 'secretary', 'treasurer', 'evangelism_leader', 'elder'])
        ->pluck('member_id')
        ->toArray();
    
    // Debug: Get all leaders first to see what we're working with
    $allLeaders = \App\Models\Leader::with('member')
        ->whereIn('position', ['pastor', 'assistant_pastor', 'secretary', 'assistant_secretary', 'treasurer', 'assistant_treasurer', 'evangelism_leader', 'elder'])
        ->get();
    
    // Debug: Log for troubleshooting
    Log::info('User creation - All leaders found', [
        'total_leaders' => $allLeaders->count(),
        'leaders_details' => $allLeaders->map(function($l) use ($existingLeaderUserIds) {
            return [
                'id' => $l->id,
                'position' => $l->position,
                'is_active' => $l->is_active,
                'member_id' => $l->member_id,
                'member_name' => $l->member ? $l->member->full_name : 'NO MEMBER',
                'has_leader_account' => in_array($l->member_id, $existingLeaderUserIds),
                'member_has_user' => $l->member && $l->member->user ? 'YES' : 'NO'
            ];
        })->toArray(),
        'existing_leader_user_ids' => $existingLeaderUserIds
    ]);
    
    // Get active leaders with their member information
    // Only filter out if they already have a matching LEADER account
    $leaders = \App\Models\Leader::with('member')
        ->where('is_active', true)
        ->whereNotIn('member_id', $existingLeaderUserIds)
        ->whereIn('position', ['pastor', 'assistant_pastor', 'secretary', 'assistant_secretary', 'treasurer', 'assistant_treasurer', 'evangelism_leader', 'elder'])
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
            // Now mapping elders and evangelism leaders to their own roles instead of 'member'
            $role = match($leader->position) {
                'pastor', 'assistant_pastor' => 'pastor',
                'secretary', 'assistant_secretary' => 'secretary',
                'treasurer', 'assistant_treasurer' => 'treasurer',
                'evangelism_leader' => 'evangelism_leader', // DISTINCT ROLE
                'elder' => 'elder', // DISTINCT ROLE
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
            $phoneNumberWarning = null;
            if (!empty($validated['phone_number'])) {
                $phone = trim($validated['phone_number']);
                $phone = preg_replace('/^\+255/', '', $phone);
                $phone = ltrim($phone, '0');
                $phoneNumber = '+255' . $phone;
                
                // Check if phone number already exists
                if (User::where('phone_number', $phoneNumber)->exists()) {
                    $phoneNumber = null;
                    $phoneNumberWarning = "Note: The admin account was created without a phone number because this phone number is already in use by another user account.";
                }
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
            

            
            // Map leader position to user role
            // Now mapping elders and evangelism leaders to their own roles instead of 'member'
            $role = match($leader->position) {
                'pastor', 'assistant_pastor' => 'pastor',
                'secretary', 'assistant_secretary' => 'secretary',
                'treasurer', 'assistant_treasurer' => 'treasurer',
                'evangelism_leader' => 'evangelism_leader', 
                'elder' => 'elder',
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
            $existingUser = $member->user; // Define for validation usage
            
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
            
            // Handle phone number for leader account creation
            // Note: Member may already have a "member" role account with this phone number
            // Since it's the same person, we'll allow the leader account to use the same phone
            // by temporarily removing it from the member account
            $phoneNumberWarning = null;
            $memberAccountPhoneToRestore = null;
            
            if ($phoneNumber) {
                // First, check if member already has a user account with this phone
                if ($existingUser && $existingUser->phone_number === $phoneNumber) {
                    // Member already has an account (likely "member" role) with this phone
                    // Since it's the same person, we'll allow the leader account to use the phone
                    // We'll temporarily remove it from the member account to avoid constraint violation
                    Log::info('Member already has user account with same phone number - transferring phone to leader account', [
                        'member_id' => $memberId,
                        'member_name' => $member->full_name,
                        'existing_user_id' => $existingUser->id,
                        'existing_role' => $existingUser->role,
                        'new_role' => $role,
                        'phone' => $phoneNumber
                    ]);
                    
                    // Store the phone to restore later if needed (though we'll use it for leader account)
                    $memberAccountPhoneToRestore = $phoneNumber;
                    
                    // Temporarily remove phone from member account to allow leader account to use it
                    // The leader account will have the phone number (more important for SMS notifications)
                    $existingUser->phone_number = null;
                    $existingUser->save();
                    
                    $phoneNumberWarning = "Note: The phone number has been transferred from the member account to the leader account. The member account ({$existingUser->role} role) no longer has a phone number, but the leader account ({$role} role) now has it for SMS notifications.";
                } else {
                    // Check if any OTHER user (different member) has this phone number
                    $existingUserWithPhone = User::where('phone_number', $phoneNumber)
                        ->where(function($q) use ($memberId, $existingUser) {
                            // Exclude this member's accounts
                            if ($existingUser) {
                                $q->where('id', '!=', $existingUser->id);
                            }
                            // Also exclude any other accounts for this member
                            $q->where(function($subQ) use ($memberId) {
                                $subQ->whereNull('member_id')
                                     ->orWhere('member_id', '!=', $memberId);
                            });
                        })
                        ->first();
                    
                    if ($existingUserWithPhone) {
                        $existingUserRole = $existingUserWithPhone->role;
                        $existingUserName = $existingUserWithPhone->name;
                        $existingMemberId = $existingUserWithPhone->member_id;
                        
                        // Get member name if it's a member account
                        $memberName = $existingUserName;
                        if ($existingMemberId) {
                            $existingMember = \App\Models\Member::find($existingMemberId);
                            if ($existingMember) {
                                $memberName = $existingMember->full_name;
                            }
                        }
                        
                        // Another user (different member) has this phone - set to null and continue
                        Log::warning('Phone number already used by different member - creating leader account without phone', [
                            'member_id' => $memberId,
                            'member_name' => $member->full_name,
                            'existing_user_id' => $existingUserWithPhone->id,
                            'existing_member_name' => $memberName,
                            'existing_role' => $existingUserRole,
                            'phone' => $phoneNumber
                        ]);
                        
                        $phoneNumber = null;
                        $phoneNumberWarning = "Note: The leader account was created without a phone number because phone number {$member->phone_number} is already in use by {$memberName}'s account ({$existingUserRole} role). Each user account must have a unique phone number.";
                    }
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
                    $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
                    
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

        // Build success message
        $successMessage = "User account for {$user->name} has been created successfully.";
        if ($phoneNumberWarning) {
            $successMessage .= " {$phoneNumberWarning}";
        }
        
        // Store credentials in session for SweetAlert popup
        return redirect()->route('admin.users')
            ->with('success', $successMessage)
            ->with('user_created', true)
            ->with('user_name', $user->name)
            ->with('user_email', $user->email)
            ->with('user_password', $generatedPassword)
            ->with('user_role', $role)
            ->with('sms_sent', $smsSent)
            ->with('sms_error', $smsError)
            ->with('sms_reason', $smsReason)
            ->with('phone_number', $phoneNumber)
            ->with('phone_warning', $phoneNumberWarning ?? null);
    }

    /**
     * Generate a strong random password that meets all requirements
     * Minimum 5 characters, uppercase, lowercase, numbers, symbols
     */
    private function generateStrongPassword($length = 8)
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
        
        // Fill the rest randomly from all character sets (minimum 5 characters total)
        $all = $uppercase . $lowercase . $numbers . $symbols;
        $minLength = max(5, $length); // Ensure minimum 5 characters
        for ($i = strlen($password); $i < $minLength; $i++) {
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
        $roles = ['admin', 'pastor', 'secretary', 'evangelism_leader'];
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
            'role' => 'required|in:admin,pastor,secretary,evangelism_leader',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,slug',
        ]);

        $role = $request->role;
        $permissionSlugs = $request->permissions ?? [];

        // Get permission IDs
        $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id');

        // Retry logic for deadlock handling
        $maxRetries = 3;
        $retryCount = 0;
        $retryDelay = 100; // milliseconds

        while ($retryCount < $maxRetries) {
            try {
                DB::transaction(function () use ($role, $permissionIds) {
                    // Delete existing permissions for this role
                    DB::table('role_permissions')->where('role', $role)->delete();

                    // Prepare batch insert data
                    $insertData = [];
                    $now = now();
                    foreach ($permissionIds as $permissionId) {
                        $insertData[] = [
                            'role' => $role,
                            'permission_id' => $permissionId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // Batch insert all permissions at once (more efficient and reduces lock time)
                    if (!empty($insertData)) {
                        DB::table('role_permissions')->insert($insertData);
                    }
                }, 5); // 5 attempts for the transaction itself

                // If we get here, the transaction succeeded
                break;

            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a deadlock error
                // MySQL deadlock: SQLSTATE[40001] with error code 1213
                // Also check for "Deadlock" in the message
                $isDeadlock = $e->getCode() == 40001 || 
                              str_contains($e->getMessage(), 'Deadlock') ||
                              str_contains($e->getMessage(), '1213');
                
                if ($isDeadlock) {
                    $retryCount++;
                    
                    if ($retryCount >= $maxRetries) {
                        Log::error('Deadlock retry limit exceeded in updateRolePermissions', [
                            'role' => $role,
                            'retry_count' => $retryCount,
                            'error' => $e->getMessage(),
                            'error_code' => $e->getCode(),
                        ]);
                        
                        return back()->with('error', 'Failed to update permissions due to a database conflict. Please try again in a moment.');
                    }
                    
                    // Wait before retrying (exponential backoff)
                    usleep($retryDelay * 1000 * $retryCount);
                    continue;
                }
                
                // If it's not a deadlock, re-throw the exception
                throw $e;
            }
        }

        // Log this activity
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Updated permissions for role: {$role}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
            ]);
        } catch (\Exception $e) {
            // Silently continue if logging fails
            Log::warning('Failed to log role permissions update: ' . $e->getMessage());
        }

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
                        $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
                        
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

