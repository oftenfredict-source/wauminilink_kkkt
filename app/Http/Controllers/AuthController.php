<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\FailedLoginAttempt;
use App\Models\BlockedIp;
use App\Models\User;
use App\Models\LoginOtp;
use App\Services\DeviceInfoService;
use App\Services\SmsService;
use App\Services\SettingsService;

class AuthController extends Controller
{
    // OTP Feature Flag - Set to false to disable OTP temporarily
    private const ENABLE_OTP = false;

    // Show login form
    public function showLogin()
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            $campus = $user->getCampus();

            // 1. Super Administrator prioritized check
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Check evangelism leader FIRST (before branch check) - they should always go to evangelism dashboard
            if ($user->isEvangelismLeader()) {
                return redirect()->route('evangelism-leader.dashboard');
            }

            // Church elder
            if ($user->isChurchElder()) {
                return redirect()->route('church-elder.dashboard');
            }

            // Check if branch user
            if ($campus && !$campus->is_main_campus) {
                return redirect()->route('branch.dashboard');
            }

            // Check if Usharika admin
            if ($user->isUsharikaAdmin() || ($campus && $campus->is_main_campus && $user->isAdmin())) {
                return redirect()->route('usharika.dashboard');
            }

            // Default role-based redirects
            if ($user->isPastor()) {
                return redirect()->route('dashboard.pastor');
            } elseif ($user->isTreasurer()) {
                return redirect()->route('finance.dashboard');
            } elseif ($user->isParishWorker()) {
                return redirect()->route('parish-worker.dashboard');
            } elseif ($user->isMember()) {
                return redirect()->route('member.dashboard');
            } else {
                return redirect()->route('dashboard.secretary');
            }
        }

        // Prevent caching of login page
        return response()->view('login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $emailOrMemberId = $request->input('email');
        $password = $request->input('password');

        try {
            // Check if input is an email or member_id
            // We now support logging in with EITHER:
            // 1. The 'email' column (which stores real email for leaders, or member_id user for members)
            // 2. The 'member_id' column (if input matches a member_id string)

            $user = null;

            if (strpos($emailOrMemberId, '@') !== false) {
                // It looks like an email - check email column
                $user = \App\Models\User::where('email', $emailOrMemberId)->first();
            } else {
                // It looks like a member ID or username

                // 1. Try finding by 'email' column (where we store member_id username for standard members)
                $user = \App\Models\User::where('email', $emailOrMemberId)->first();

                // 2. If not found, try finding by 'member_id' column (if we have a numeric or string ID)
                // Note: users table member_id is usually integer FK, but input might be string "2025-001"
                // So we might need to find the Member first, then get the User?
                // Actually, for now, let's stick to 'email' column as the primary identifier
                // unless we want to support finding user by their Member's string ID?

                // If the user entered "2025-001", and we stored it in 'email' column, the above check works.
                // If we stored "john@example.com" in 'email', but they typed "2025-001"...
                // We need to find the user via the Member relationship.

                if (!$user) {
                    // Try to find a Member with this member_id string
                    $member = \App\Models\Member::where('member_id', $emailOrMemberId)->first();
                    if ($member) {
                        // Find a user associated with this member
                        // Warning: A member might have multiple users (Member Role + Leader Role)
                        // We should prefer the 'member' role for this login method, or just pick the first?
                        // If they type Member ID, they probably expect the Member account.
                        $user = \App\Models\User::where('member_id', $member->id)
                            ->where('role', 'member')
                            ->first();

                        // If no member role found, try any user for this member
                        if (!$user) {
                            $user = \App\Models\User::where('member_id', $member->id)->first();
                        }
                    }
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Database connection error
            if (
                strpos($e->getMessage(), 'Connection refused') !== false ||
                strpos($e->getMessage(), 'No connection could be made') !== false
            ) {
                return back()->withErrors([
                    'email' => 'Database connection failed. Please ensure MySQL is running in XAMPP.',
                ])->withInput($request->only('email'));
            }
            throw $e; // Re-throw if it's a different database error
        }

        // Check if IP is blocked
        $ipAddress = $request->ip();
        if (BlockedIp::isBlocked($ipAddress)) {
            $this->logFailedLogin($request, $emailOrMemberId, 'IP address is blocked');
            return back()->withErrors([
                'email' => 'Your IP address has been blocked. Please contact the administrator.',
            ])->withInput($request->only('email'));
        }

        // IMPORTANT: Check if user is blocked BEFORE attempting authentication
        // This must happen before Auth::attempt() to prevent login
        if ($user) {
            // Get fresh data from database to ensure we have the latest block status
            // Use fresh() to bypass model cache and get latest from database
            $freshUser = \App\Models\User::find($user->id);

            if ($freshUser && $freshUser->login_blocked_until) {
                // Compare in UTC (how it's stored in database)
                // Get the raw timestamp value from database to avoid timezone conversion issues
                // Laravel's datetime cast converts based on app timezone, so we get raw value
                $rawBlockedUntil = DB::table('users')
                    ->where('id', $freshUser->id)
                    ->value('login_blocked_until');

                if ($rawBlockedUntil) {
                    // Parse the raw database value - it's stored as UTC timestamp
                    // Use createFromFormat with UTC timezone to avoid conversion
                    $blockedUntil = Carbon::createFromFormat('Y-m-d H:i:s', $rawBlockedUntil, 'UTC');
                    $now = Carbon::now('UTC');

                    // If block has expired, clear it
                    if ($blockedUntil->lte($now)) {
                        $freshUser->update(['login_blocked_until' => null]);
                        $freshUser->refresh();
                    } else {
                        // User is still blocked - prevent login
                        // Use Tanzania timezone for display
                        $tanzaniaTimezone = 'Africa/Dar_es_Salaam';
                        $blockedUntilDisplay = $blockedUntil->copy()->setTimezone($tanzaniaTimezone);
                        $unblockTime = $blockedUntilDisplay->format('F j, Y \a\t g:i A');
                        $message = "Your account is temporarily blocked from logging in. Please try again on {$unblockTime}.";

                        $this->logFailedLogin($request, $emailOrMemberId, 'Account is temporarily blocked');
                        return back()->withErrors([
                            'email' => $message,
                        ])->withInput($request->only('email'));
                    }
                }
            }
        }

        // Prepare credentials for authentication
        $credentials = [
            'email' => $emailOrMemberId,
            'password' => $password,
        ];

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if OTP is enabled
                if (self::ENABLE_OTP) {
                    // Generate and send OTP instead of logging in directly
                    $otp = $this->generateAndSendOtp($user, $emailOrMemberId, $request);

                    if ($otp) {
                        // Store user ID in session temporarily for OTP verification
                        $request->session()->put('otp_user_id', $user->id);
                        $request->session()->put('otp_email', $emailOrMemberId);
                        // Reset resend attempts counter for new OTP session
                        $request->session()->put('otp_resend_attempts', 0);

                        // Logout the user (they'll be logged in after OTP verification)
                        Auth::logout();

                        // Redirect to OTP verification page
                        return redirect()->route('login.otp.verify')
                            ->with('info', 'An OTP has been sent to your phone number. Please enter it to complete login.');
                    } else {
                        // If OTP sending failed, allow login but log the issue
                        Auth::logout();
                        \Log::warning('OTP generation failed, login blocked', [
                            'user_id' => $user->id,
                            'email' => $emailOrMemberId
                        ]);

                        return back()->withErrors([
                            'email' => 'Unable to send OTP. Please contact administrator or try again later.',
                        ])->withInput($request->only('email'));
                    }
                } else {
                    // OTP is disabled - proceed with direct login
                    $request->session()->regenerate();

                    // Update the session record with user_id
                    try {
                        $sessionId = $request->session()->getId();
                        DB::table('sessions')
                            ->updateOrInsert(
                                ['id' => $sessionId],
                                [
                                    'user_id' => Auth::id(),
                                    'ip_address' => $request->ip(),
                                    'user_agent' => $request->userAgent(),
                                    'last_activity' => time(),
                                ]
                            );
                    } catch (\Exception $e) {
                        // Silently continue if session table doesn't exist
                    }

                    // Log login activity
                    try {
                        \App\Models\ActivityLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'login',
                            'description' => 'User logged in (OTP disabled)',
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'route' => 'login',
                            'method' => 'POST',
                        ]);
                    } catch (\Exception $e) {
                        // Silently continue if table doesn't exist
                    }

                    // 1. Super Administrator prioritized check
                    if ($user->isAdmin()) {
                        return redirect()->route('admin.dashboard')
                            ->with('success', 'Login successful! Welcome Admin.');
                    }

                    // FIRST: Check if user has member_id but no active leadership positions → Member Portal
                    if ($user->member_id && $user->member) {
                        $activePositions = $user->member->activeLeadershipPositions()
                            ->where('is_active', true)
                            ->where(function ($query) {
                                $query->whereNull('end_date')
                                    ->orWhere('end_date', '>=', now()->toDateString());
                            })
                            ->get();

                        if ($activePositions->isEmpty()) {
                            // No active positions → Member Portal
                            return redirect()->route('member.dashboard')
                                ->with('success', 'Login successful! Welcome.');
                        }
                    }

                    // Check evangelism leader (before branch check) - they should always go to evangelism dashboard
                    if ($user->isEvangelismLeader()) {
                        return redirect()->route('evangelism-leader.dashboard')
                            ->with('success', 'Login successful! Welcome Evangelism Leader.');
                    }

                    // Check church elder BEFORE branch check (users in branch can be elders)
                    if ($user->isChurchElder()) {
                        return redirect()->route('church-elder.dashboard')
                            ->with('success', 'Login successful! Welcome Church Elder.');
                    }

                    // Check if user is branch user (only if they have active leadership positions)
                    $campus = $user->getCampus();
                    if ($campus && !$campus->is_main_campus) {
                        // Only redirect to branch dashboard if they have active positions or are admin/secretary
                        if ($user->isAdmin() || $user->isSecretary() || ($user->member_id && $user->member)) {
                            $hasActivePositions = false;
                            if ($user->member_id && $user->member) {
                                $activePositions = $user->member->activeLeadershipPositions()
                                    ->where('is_active', true)
                                    ->where(function ($query) {
                                        $query->whereNull('end_date')
                                            ->orWhere('end_date', '>=', now()->toDateString());
                                    })
                                    ->get();
                                $hasActivePositions = $activePositions->isNotEmpty();
                            }

                            // Only show branch dashboard if they have active positions or are admin/secretary
                            if ($hasActivePositions || $user->isAdmin() || $user->isSecretary()) {
                                return redirect()->route('branch.dashboard')
                                    ->with('success', 'Login successful! Welcome to ' . $campus->name . ' branch.');
                            }
                        }
                    }

                    // Check if Usharika admin
                    if ($user->isUsharikaAdmin() || ($campus && $campus->is_main_campus && $user->isAdmin())) {
                        return redirect()->route('usharika.dashboard')
                            ->with('success', 'Login successful! Welcome to Usharika Dashboard.');
                    }



                    // Redirect based on role (for main campus users)
                    if ($user->role === 'secretary') {
                        return redirect()->route('dashboard.secretary')
                            ->with('success', 'Login successful! Welcome back.');
                    } elseif ($user->role === 'pastor') {
                        return redirect()->route('dashboard.pastor')
                            ->with('success', 'Login successful! Welcome Pastor.');
                    } elseif ($user->role === 'treasurer') {
                        return redirect()->route('finance.dashboard')
                            ->with('success', 'Login successful! Welcome Treasurer.');
                    } elseif ($user->role === 'parish_worker') {
                        return redirect()->route('parish-worker.dashboard')
                            ->with('success', 'Login successful! Welcome Parish Worker.');
                    } elseif ($user->role === 'member') {
                        return redirect()->route('member.dashboard')
                            ->with('success', 'Login successful! Welcome.');
                    } else {
                        Auth::logout();
                        return back()->withErrors(['role' => 'Unauthorized role.']);
                    }
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Database connection error during authentication
            if (
                strpos($e->getMessage(), 'Connection refused') !== false ||
                strpos($e->getMessage(), 'No connection could be made') !== false
            ) {
                return back()->withErrors([
                    'email' => 'Database connection failed. Please ensure MySQL is running in XAMPP.',
                ])->withInput($request->only('email'));
            }
            throw $e; // Re-throw if it's a different database error
        }

        // Log failed login attempt
        $failureReason = $user ? 'Invalid password' : 'User not found';
        $this->logFailedLogin($request, $emailOrMemberId, $failureReason);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Log failed login attempt
     */
    private function logFailedLogin(Request $request, string $email, string $reason): void
    {
        try {
            $deviceInfo = DeviceInfoService::getDeviceInfo($request);

            FailedLoginAttempt::create([
                'email' => $email,
                'ip_address' => $deviceInfo['ip_address'],
                'mac_address' => $deviceInfo['mac_address'],
                'user_agent' => $deviceInfo['user_agent'],
                'device_type' => $deviceInfo['device_type'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'device_name' => $deviceInfo['device_name'],
                'failure_reason' => $reason,
                'ip_blocked' => false,
            ]);
        } catch (\Exception $e) {
            // Silently fail logging to not break the application
        }
    }

    /**
     * Generate and send OTP to user
     */
    private function generateAndSendOtp(User $user, string $email, Request $request): ?LoginOtp
    {
        $otp = null;

        try {
            // Generate 6-digit OTP
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // OTP expires in 2 minutes
            $expiresAt = now()->addMinutes(2);

            // Invalidate any existing unused OTPs for this user
            try {
                LoginOtp::where('user_id', $user->id)
                    ->where('is_used', false)
                    ->where('expires_at', '>', now())
                    ->update(['is_used' => true]);
            } catch (\Exception $e) {
                \Log::warning('Failed to invalidate existing OTPs: ' . $e->getMessage(), [
                    'user_id' => $user->id
                ]);
                // Continue even if invalidation fails
            }

            // Create new OTP - this is critical, must succeed
            try {
                $otp = LoginOtp::create([
                    'user_id' => $user->id,
                    'otp_code' => $otpCode,
                    'email' => $email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'expires_at' => $expiresAt,
                    'is_used' => false,
                    'attempts' => 0,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create OTP record: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $email,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // If OTP creation fails, we cannot proceed
                return null;
            }

            // Get phone number from user or member - wrap in try-catch to prevent exceptions
            $phoneNumber = null;
            try {
                if (!empty($user->phone_number)) {
                    $phoneNumber = $user->phone_number;
                } elseif (!empty($user->member_id)) {
                    // Safely access member relationship
                    try {
                        $member = $user->member;
                        if ($member && !empty($member->phone_number)) {
                            $phoneNumber = $member->phone_number;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to access member relationship: ' . $e->getMessage(), [
                            'user_id' => $user->id,
                            'member_id' => $user->member_id
                        ]);
                        // Continue without phone number
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get phone number: ' . $e->getMessage(), [
                    'user_id' => $user->id
                ]);
                // Continue without phone number
            }

            // Send OTP via SMS - this is optional, OTP should still be returned even if SMS fails
            if (!empty($phoneNumber)) {
                try {
                    $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                    if ($smsEnabled) {
                        $smsService = app(SmsService::class);
                        $churchName = SettingsService::get('church_name', 'Waumini Church');

                        $message = "Shalom {$user->name}, nambari yako ya kuthibitisha kuingia kwenye {$churchName} ni: {$otpCode}\n\n";
                        $message .= "Nambari hii inaisha muda wa dakika 5. Usishirikishe na mtu yeyote.\n\n";
                        $message .= "Mungu akubariki!";

                        $smsResult = $smsService->sendDebug($phoneNumber, $message);
                        $smsSent = $smsResult['ok'] ?? false;

                        if ($smsSent) {
                            \Log::info('Login OTP sent successfully', [
                                'user_id' => $user->id,
                                'phone' => $phoneNumber,
                                'otp_id' => $otp->id
                            ]);
                        } else {
                            \Log::warning('Login OTP SMS failed', [
                                'user_id' => $user->id,
                                'phone' => $phoneNumber,
                                'error' => $smsResult['reason'] ?? $smsResult['error'] ?? 'Unknown error',
                                'otp_id' => $otp->id,
                                'response' => $smsResult
                            ]);
                        }
                    } else {
                        \Log::info('Login OTP SMS skipped: SMS notifications disabled', [
                            'user_id' => $user->id,
                            'phone' => $phoneNumber
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Exception while sending OTP SMS: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'phone' => $phoneNumber,
                        'exception' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue even if SMS sending fails
                }
            } else {
                \Log::warning('Login OTP SMS skipped: No phone number', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'has_user_phone' => !empty($user->phone_number),
                    'has_member_id' => !empty($user->member_id)
                ]);
            }

            // Always return OTP if it was created successfully, even if SMS failed
            return $otp;

        } catch (\Exception $e) {
            \Log::error('Failed to generate OTP: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'email' => $email,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Only return null if OTP was never created
            // If OTP exists, return it even if there were other errors
            return $otp;
        }
    }

    /**
     * Show OTP verification page
     */
    public function showOtpVerification(Request $request)
    {
        // Check if user has pending OTP verification
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Please login first to receive an OTP.');
        }

        $userId = $request->session()->get('otp_user_id');
        $email = $request->session()->get('otp_email');

        // Get the latest unused OTP for this user
        $otp = LoginOtp::where('user_id', $userId)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            // If no valid OTP, still show the page but allow resend
            // User can resend once even if expired
            return view('login-otp', [
                'email' => $email,
                'otp_expires_at' => null, // No valid OTP, but allow resend
            ]);
        }

        return view('login-otp', [
            'email' => $email,
            'otp_expires_at' => $otp->expires_at,
        ]);
    }

    /**
     * Verify OTP and complete login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        // Check if user has pending OTP verification
        if (!$request->session()->has('otp_user_id')) {
            return back()->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $userId = $request->session()->get('otp_user_id');
        $email = $request->session()->get('otp_email');
        $otpCode = $request->input('otp');

        // Find the latest unused OTP for this user
        $otp = LoginOtp::where('user_id', $userId)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp' => 'No valid OTP found. Please login again.']);
        }

        // Check if OTP is expired
        if ($otp->isExpired()) {
            $otp->incrementAttempts();
            return back()->withErrors(['otp' => 'OTP has expired. Please login again to receive a new OTP.']);
        }

        // Check if too many attempts (max 5 attempts)
        if ($otp->attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many failed attempts. Please login again to receive a new OTP.']);
        }

        // Verify OTP code
        if ($otp->otp_code !== $otpCode) {
            $otp->incrementAttempts();
            $remainingAttempts = 5 - $otp->attempts;

            if ($remainingAttempts <= 0) {
                return back()->withErrors(['otp' => 'Invalid OTP. Maximum attempts reached. Please login again.']);
            }

            return back()->withErrors(['otp' => "Invalid OTP. You have {$remainingAttempts} attempt(s) remaining."]);
        }

        // OTP is valid - mark as used and complete login
        $otp->markAsUsed();

        // Get user and login
        $user = User::findOrFail($userId);

        // Login the user
        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();

        // Clear OTP session data including resend attempts
        $request->session()->forget(['otp_user_id', 'otp_email', 'otp_resend_attempts']);

        // Update the session record with user_id
        try {
            $sessionId = $request->session()->getId();
            DB::table('sessions')
                ->updateOrInsert(
                    ['id' => $sessionId],
                    [
                        'user_id' => Auth::id(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'last_activity' => time(),
                    ]
                );
        } catch (\Exception $e) {
            // Silently continue if session table doesn't exist
        }

        // Log login activity
        try {
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'description' => 'User logged in with OTP verification',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => 'login.otp.verify',
                'method' => 'POST',
            ]);
        } catch (\Exception $e) {
            // Silently continue if table doesn't exist
        }

        // 1. Super Administrator prioritized check
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Login successful! Welcome Admin.');
        }

        // FIRST: Check if user has member_id but no active leadership positions → Member Portal
        if ($user->member_id && $user->member) {
            $activePositions = $user->member->activeLeadershipPositions()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->get();

            if ($activePositions->isEmpty()) {
                // No active positions → Member Portal
                return redirect()->route('member.dashboard')
                    ->with('success', 'Login successful! Welcome.');
            }
        }

        // Check evangelism leader (before branch check) - they should always go to evangelism dashboard
        if ($user->isEvangelismLeader()) {
            return redirect()->route('evangelism-leader.dashboard')
                ->with('success', 'Login successful! Welcome Evangelism Leader.');
        }

        // Check parish worker
        if ($user->isParishWorker()) {
            return redirect()->route('parish-worker.dashboard')
                ->with('success', 'Login successful! Welcome Parish Worker.');
        }

        // Check church elder BEFORE branch check (users in branch can be elders)
        if ($user->isChurchElder()) {
            // Redirect to the first community they are assigned to, or a general dashboard
            $community = $user->elderCommunities()->first();
            if ($community) {
                return redirect()->route('church-elder.community.show', $community->id)
                    ->with('success', 'Login successful! Welcome Church Elder.');
            }
            return redirect()->route('church-elder.dashboard')
                ->with('success', 'Login successful! Welcome Church Elder.');
        }

        // Check if user is branch user (only if they have active leadership positions)
        $campus = $user->getCampus();
        if ($campus && !$campus->is_main_campus) {
            // Only redirect to branch dashboard if they have active positions or are admin/secretary
            if ($user->isAdmin() || $user->isSecretary() || ($user->member_id && $user->member)) {
                $hasActivePositions = false;
                if ($user->member_id && $user->member) {
                    $activePositions = $user->member->activeLeadershipPositions()
                        ->where('is_active', true)
                        ->where(function ($query) {
                            $query->whereNull('end_date')
                                ->orWhere('end_date', '>=', now()->toDateString());
                        })
                        ->get();
                    $hasActivePositions = $activePositions->isNotEmpty();
                }

                // Only show branch dashboard if they have active positions or are admin/secretary
                if ($hasActivePositions || $user->isAdmin() || $user->isSecretary()) {
                    return redirect()->route('branch.dashboard')
                        ->with('success', 'Login successful! Welcome to ' . $campus->name . ' branch.');
                }
            }
        }

        // Check if Usharika admin
        if ($user->isUsharikaAdmin() || ($campus && $campus->is_main_campus && $user->isAdmin())) {
            return redirect()->route('usharika.dashboard')
                ->with('success', 'Login successful! Welcome to Usharika Dashboard.');
        }


        // Redirect based on role (for main campus users)
        if ($user->role === 'secretary') {
            return redirect()->route('dashboard.secretary')
                ->with('success', 'Login successful! Welcome back.');
        } elseif ($user->role === 'pastor') {
            return redirect()->route('dashboard.pastor')
                ->with('success', 'Login successful! Welcome Pastor.');
        } elseif ($user->role === 'treasurer') {
            return redirect()->route('finance.dashboard')
                ->with('success', 'Login successful! Welcome Treasurer.');
        } elseif ($user->role === 'member') {
            return redirect()->route('member.dashboard')
                ->with('success', 'Login successful! Welcome.');
        } else {
            Auth::logout();
            return back()->withErrors(['role' => 'Unauthorized role.']);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        // Check if user has pending OTP verification
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        $userId = $request->session()->get('otp_user_id');
        $email = $request->session()->get('otp_email');

        // Track resend attempts - allow first resend even if expired, block second
        $resendAttempts = $request->session()->get('otp_resend_attempts', 0);

        // If this is the second resend attempt, redirect to login
        if ($resendAttempts >= 1) {
            // Clear OTP session data
            $request->session()->forget('otp_user_id');
            $request->session()->forget('otp_email');
            $request->session()->forget('otp_resend_attempts');

            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        // Increment resend attempts counter
        $request->session()->put('otp_resend_attempts', $resendAttempts + 1);

        $user = User::findOrFail($userId);

        // Generate and send new OTP (allow even if previous OTP expired)
        $otp = $this->generateAndSendOtp($user, $email, $request);

        if ($otp) {
            // Reset resend attempts on successful send (optional - allows unlimited resends if successful)
            // Or keep the counter to limit to 2 total attempts
            return back()->with('success', 'A new OTP has been sent to your phone number.');
        } else {
            return back()->withErrors(['otp' => 'Failed to send OTP. Please try again or contact administrator.']);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        // Log logout activity (if table exists)
        if (Auth::check()) {
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'logout',
                    'description' => 'User logged out',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'route' => 'logout',
                    'method' => 'POST',
                ]);
            } catch (\Exception $e) {
                // Table might not exist yet - silently continue
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'You have been logged out successfully.');
    }

    // Show forgot password form
    public function showForgotPassword()
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isPastor()) {
                return redirect()->route('dashboard.pastor');
            } elseif ($user->isTreasurer()) {
                return redirect()->route('finance.dashboard');
            } elseif ($user->isEvangelismLeader()) {
                return redirect()->route('evangelism-leader.dashboard');
            } elseif ($user->isChurchElder()) {
                // Redirect to the first community they are assigned to, or a general dashboard
                $community = $user->elderCommunities()->first();
                if ($community) {
                    return redirect()->route('church-elder.community.show', $community->id);
                }
                return redirect()->route('church-elder.dashboard');
            } elseif ($user->isMember()) {
                return redirect()->route('member.dashboard');
            } else {
                return redirect()->route('dashboard.secretary');
            }
        }

        return view('forgot-password');
    }

    // Send password reset link
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
        ]);

        $emailOrMemberId = $request->input('email');

        // Find user by email or member_id
        $user = User::where('email', $emailOrMemberId)->first();

        if (!$user) {
            // Don't reveal if user exists for security
            return back()->with('status', 'If that email address exists in our system, we will send a password reset link.');
        }

        // Generate password reset token
        $token = Str::random(64);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send reset link via SMS if phone number exists
        $smsSent = false;
        $smsError = null;
        $phoneNumber = null;

        // Get phone number from user or member
        if (!empty($user->phone_number)) {
            $phoneNumber = $user->phone_number;
        } elseif ($user->member_id && $user->member) {
            // Fallback to member's phone number if user doesn't have one
            $phoneNumber = $user->member->phone_number ?? null;
        }

        if (!empty($phoneNumber)) {
            try {
                $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                if (!$smsEnabled) {
                    $smsError = 'SMS notifications are disabled in system settings';
                    \Log::info('Password reset SMS skipped: SMS notifications disabled', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'phone' => $phoneNumber
                    ]);
                } else {
                    $smsService = app(SmsService::class);
                    $resetUrl = url('/reset-password/' . $token);
                    $churchName = SettingsService::get('church_name', 'Waumini Church');

                    $message = "Shalom {$user->name}, umepokea ombi la kubadilisha nenosiri la akaunti yako ya {$churchName}.\n\n";
                    $message .= "Bofya kiungo hiki kubadilisha nenosiri:\n{$resetUrl}\n\n";
                    $message .= "Kiungo hiki kitakwisha muda wa saa 1. Usishirikishe kiungo hiki na mtu yeyote.\n\n";
                    $message .= "Mungu akubariki!";

                    \Log::info('Attempting to send password reset SMS', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'phone' => $phoneNumber,
                        'reset_url' => $resetUrl
                    ]);

                    $smsResult = $smsService->sendDebug($phoneNumber, $message);
                    $smsSent = $smsResult['ok'] ?? false;

                    if ($smsSent) {
                        \Log::info('Password reset SMS sent successfully', [
                            'user_id' => $user->id,
                            'phone' => $phoneNumber,
                            'response' => $smsResult
                        ]);
                    } else {
                        $smsError = $smsResult['reason'] ?? $smsResult['error'] ?? 'SMS sending failed';
                        \Log::warning('Password reset SMS failed', [
                            'user_id' => $user->id,
                            'phone' => $phoneNumber,
                            'error' => $smsError,
                            'response' => $smsResult
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $smsError = $e->getMessage();
                \Log::error('Failed to send password reset SMS: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'phone' => $phoneNumber,
                    'exception' => $e
                ]);
            }
        } else {
            $smsError = 'No phone number found for this user';
            \Log::info('Password reset SMS skipped: No phone number', [
                'user_id' => $user->id,
                'email' => $user->email,
                'has_user_phone' => !empty($user->phone_number),
                'has_member' => !empty($user->member_id),
                'has_member_phone' => ($user->member_id && $user->member) ? !empty($user->member->phone_number) : false
            ]);
        }

        // Log the password reset request
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'password_reset_requested',
                'description' => 'Password reset link requested',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => 'password.email',
                'method' => 'POST',
            ]);
        } catch (\Exception $e) {
            // Silently continue if table doesn't exist
        }

        if ($smsSent) {
            return back()->with('status', 'Password reset link has been sent to your phone number. Please check your SMS.');
        } else {
            // Build error message
            $errorMessage = 'Password reset link generated. ';

            if (empty($phoneNumber)) {
                $errorMessage .= 'No phone number is registered for your account. ';
            } elseif ($smsError === 'SMS notifications are disabled in system settings') {
                $errorMessage .= 'SMS notifications are currently disabled. ';
            } else {
                $errorMessage .= 'SMS could not be sent. ';
            }

            $errorMessage .= 'Please contact the administrator or use the reset link below.';

            // Show reset link for development/testing (remove in production or make it admin-only)
            return back()->with([
                'status' => $errorMessage,
                'reset_token' => $token, // For development/testing
                'reset_url' => url('/reset-password/' . $token),
                'sms_error' => $smsError
            ]);
        }
    }

    // Show reset password form
    public function showResetPassword(Request $request, $token)
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isPastor()) {
                return redirect()->route('dashboard.pastor');
            } elseif ($user->isTreasurer()) {
                return redirect()->route('finance.dashboard');
            } elseif ($user->isEvangelismLeader()) {
                return redirect()->route('evangelism-leader.dashboard');
            } elseif ($user->isChurchElder()) {
                // Redirect to the first community they are assigned to, or a general dashboard
                $community = $user->elderCommunities()->first();
                if ($community) {
                    return redirect()->route('church-elder.community.show', $community->id);
                }
                return redirect()->route('church-elder.dashboard');
            } elseif ($user->isMember()) {
                return redirect()->route('member.dashboard');
            } else {
                return redirect()->route('dashboard.secretary');
            }
        }

        return view('reset-password', ['token' => $token]);
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $emailOrMemberId = $request->input('email');
        $token = $request->input('token');
        $password = $request->input('password');

        // Find user
        $user = User::where('email', $emailOrMemberId)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Verify token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is valid (created within last hour)
        $tokenAge = Carbon::parse($passwordReset->created_at)->diffInHours(now());
        if ($tokenAge > 1) {
            // Token expired
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            return back()->withErrors(['email' => 'This password reset token has expired. Please request a new one.']);
        }

        // Verify token matches
        if (!Hash::check($token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Log the password reset
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'password_reset',
                'description' => 'Password reset successfully',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => 'password.update',
                'method' => 'POST',
            ]);
        } catch (\Exception $e) {
            // Silently continue if table doesn't exist
        }

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully. You can now login with your new password.');
    }
}