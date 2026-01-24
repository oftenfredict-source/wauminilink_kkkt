<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Member;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\SpecialEvent;
use App\Models\Celebration;
use App\Models\SundayService;
use App\Models\Announcement;
use App\Models\AnnouncementView;
use App\Models\Leader;
use Carbon\Carbon;

class MemberDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display member dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        // Church elders and other leadership roles are also members and should access their member portal
        if (!$user->member_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['role' => 'Unauthorized access.']);
        }

        $member = $user->member;
        
        if (!$member) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['member' => 'Member record not found.']);
        }

        // Get member information
        $memberInfo = [
            'member_id' => $member->member_id,
            'full_name' => $member->full_name,
            'email' => $member->email,
            'phone_number' => $member->phone_number,
            'date_of_birth' => $member->date_of_birth,
            'gender' => $member->gender,
            'membership_type' => $member->membership_type,
            'member_type' => $member->member_type,
            'profession' => $member->profession,
            'address' => $member->address,
            'region' => $member->region,
            'district' => $member->district,
        ];

        // Get financial summary
        $financialSummary = $this->getFinancialSummary($member);

        // Get announcements (upcoming events and celebrations)
        $announcements = $this->getAnnouncements($member);

        // Get unread announcements count for badge
        $unreadCount = $this->getUnreadAnnouncementsCount($member);

        // Get leadership data
        $leadershipData = $this->getLeadershipData($member);

        return view('members.dashboard', compact('member', 'memberInfo', 'financialSummary', 'announcements', 'unreadCount', 'leadershipData'));
    }

    /**
     * Get financial summary for the member
     */
    private function getFinancialSummary($member)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Get tithes - use approved scope method
        $totalTithes = Tithe::where('member_id', $member->id)
            ->approved()
            ->sum('amount');
        
        $monthlyTithes = Tithe::where('member_id', $member->id)
            ->approved()
            ->whereYear('tithe_date', $currentYear)
            ->whereMonth('tithe_date', $currentMonth)
            ->sum('amount');

        // Get offerings - use approved scope method
        $totalOfferings = Offering::where('member_id', $member->id)
            ->approved()
            ->sum('amount');
        
        $monthlyOfferings = Offering::where('member_id', $member->id)
            ->approved()
            ->whereYear('offering_date', $currentYear)
            ->whereMonth('offering_date', $currentMonth)
            ->sum('amount');

        // Get donations - use approved scope method
        $totalDonations = Donation::where('member_id', $member->id)
            ->approved()
            ->sum('amount');
        
        $monthlyDonations = Donation::where('member_id', $member->id)
            ->approved()
            ->whereYear('donation_date', $currentYear)
            ->whereMonth('donation_date', $currentMonth)
            ->sum('amount');

        // Get pledges - use pledge_amount column
        $totalPledges = Pledge::where('member_id', $member->id)->sum('pledge_amount');
        $totalPledgePayments = PledgePayment::whereHas('pledge', function($query) use ($member) {
            $query->where('member_id', $member->id);
        })->approved()->sum('amount');
        $remainingPledges = $totalPledges - $totalPledgePayments;

        // Recent transactions
        $recentTithes = Tithe::where('member_id', $member->id)
            ->approved()
            ->orderBy('tithe_date', 'desc')
            ->take(5)
            ->get();

        $recentOfferings = Offering::where('member_id', $member->id)
            ->approved()
            ->orderBy('offering_date', 'desc')
            ->take(5)
            ->get();

        $recentDonations = Donation::where('member_id', $member->id)
            ->approved()
            ->orderBy('donation_date', 'desc')
            ->take(5)
            ->get();

        return [
            'total_tithes' => $totalTithes,
            'monthly_tithes' => $monthlyTithes,
            'total_offerings' => $totalOfferings,
            'monthly_offerings' => $monthlyOfferings,
            'total_donations' => $totalDonations,
            'monthly_donations' => $monthlyDonations,
            'total_pledges' => $totalPledges,
            'total_pledge_payments' => $totalPledgePayments,
            'remaining_pledges' => $remainingPledges,
            'recent_tithes' => $recentTithes,
            'recent_offerings' => $recentOfferings,
            'recent_donations' => $recentDonations,
        ];
    }

    /**
     * Get announcements (upcoming events, celebrations, and church announcements)
     */
    private function getAnnouncements($member = null)
    {
        $now = Carbon::now();
        $next30Days = $now->copy()->addDays(30);

        // Get active church announcements (pinned first, then by date)
        $announcements = Announcement::active()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark which announcements are unread for this member
        if ($member) {
            $viewedAnnouncementIds = AnnouncementView::where('member_id', $member->id)
                ->whereIn('announcement_id', $announcements->pluck('id'))
                ->pluck('announcement_id')
                ->toArray();
            
            foreach ($announcements as $announcement) {
                $announcement->is_unread = !in_array($announcement->id, $viewedAnnouncementIds);
            }
        }

        // Get upcoming special events
        $events = SpecialEvent::whereDate('event_date', '>=', $now->toDateString())
            ->whereDate('event_date', '<=', $next30Days->toDateString())
            ->orderBy('event_date')
            ->get();

        // Get upcoming celebrations
        $celebrations = Celebration::whereDate('celebration_date', '>=', $now->toDateString())
            ->whereDate('celebration_date', '<=', $next30Days->toDateString())
            ->orderBy('celebration_date')
            ->get();

        // Get upcoming Sunday services
        $sundayServices = SundayService::whereDate('service_date', '>=', $now->toDateString())
            ->whereDate('service_date', '<=', $next30Days->toDateString())
            ->orderBy('service_date')
            ->get();

        return [
            'announcements' => $announcements,
            'events' => $events,
            'celebrations' => $celebrations,
            'sunday_services' => $sundayServices,
        ];
    }

    /**
     * Display member information page
     */
    public function information()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $member = $user->member;
        
        return view('members.information', compact('member'));
    }

    /**
     * Display member finance page
     */
    public function finance()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $member = $user->member;
        $financialSummary = $this->getFinancialSummary($member);

        // Get all pledges with payments for detailed view
        $pledges = Pledge::where('member_id', $member->id)
            ->with(['payments' => function($query) {
                $query->approved()->orderBy('payment_date', 'desc');
            }])
            ->orderBy('pledge_date', 'desc')
            ->get();

        // Get all offerings for detailed view
        $allOfferings = Offering::where('member_id', $member->id)
            ->approved()
            ->orderBy('offering_date', 'desc')
            ->get();

        return view('members.finance', compact('member', 'financialSummary', 'pledges', 'allOfferings'));
    }

    /**
     * Display member announcements page
     */
    public function announcements()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $member = $user->member;
        $announcements = $this->getAnnouncements($member);

        // Mark all announcements as viewed when member visits the announcements page
        if (isset($announcements['announcements'])) {
            foreach ($announcements['announcements'] as $announcement) {
                if (!$announcement->isViewedBy($member->id)) {
                    AnnouncementView::firstOrCreate([
                        'announcement_id' => $announcement->id,
                        'member_id' => $member->id,
                    ], [
                        'viewed_at' => now(),
                    ]);
                }
            }
        }

        // Refresh announcements to update unread status after marking as viewed
        $announcements = $this->getAnnouncements($member);

        // Get unread count for dashboard badge (should be 0 after viewing)
        $unreadCount = $this->getUnreadAnnouncementsCount($member);

        return view('members.announcements', compact('announcements', 'unreadCount'));
    }

    /**
     * Get leadership data for the member
     */
    private function getLeadershipData($member)
    {
        // Get all active leaders with their member information
        $allLeaders = Leader::with('member')
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->orderBy('position')
            ->orderBy('appointment_date', 'desc')
            ->get();

        // Get current member's leadership positions
        $memberLeadershipPositions = $member->activeLeadershipPositions()
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->get();

        // Get unread leader appointment notifications
        $unreadLeaderNotifications = $member->notifications()
            ->where('type', 'App\Notifications\LeaderAppointmentNotification')
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'all_leaders' => $allLeaders,
            'member_positions' => $memberLeadershipPositions,
            'unread_notifications' => $unreadLeaderNotifications,
            'has_leadership_position' => $memberLeadershipPositions->count() > 0
        ];
    }

    /**
     * Get count of unread announcements for a member
     */
    private function getUnreadAnnouncementsCount($member)
    {
        $activeAnnouncements = Announcement::active()->pluck('id');
        
        $viewedAnnouncementIds = AnnouncementView::where('member_id', $member->id)
            ->whereIn('announcement_id', $activeAnnouncements)
            ->pluck('announcement_id');
        
        return $activeAnnouncements->diff($viewedAnnouncementIds)->count();
    }

    /**
     * Display all church leaders
     */
    public function leaders()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $member = $user->member;
        
        // Get member's campus and community
        $memberCampusId = $member->campus_id;
        $memberCommunityId = $member->community_id;
        
        // Get all active leaders with their member information
        $allLeaders = Leader::with(['member', 'communities'])
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->get();
        
        // Get senior pastors from users table (users with role='pastor' or can_approve_finances=true)
        $seniorPastors = \App\Models\User::where(function($query) {
                $query->where('role', 'pastor')
                      ->orWhere(function($q) {
                          $q->where('can_approve_finances', true)
                            ->where('role', '!=', 'admin'); // Exclude admins
                      });
            })
            ->whereNotNull('member_id') // Only include pastors who are also members
            ->with('member')
            ->get();
        
        // Create pseudo-leader objects for senior pastors who aren't in the leaders table
        $seniorPastorLeaders = collect();
        foreach ($seniorPastors as $pastorUser) {
            if ($pastorUser->member) {
                // Check if this pastor is already in the leaders list
                $existsInLeaders = $allLeaders->contains(function($leader) use ($pastorUser) {
                    return $leader->member_id === $pastorUser->member_id && 
                           in_array($leader->position, ['pastor', 'assistant_pastor']);
                });
                
                // If not in leaders table, create a pseudo-leader object
                if (!$existsInLeaders) {
                    $pseudoLeader = new class {
                        public $id;
                        public $member_id;
                        public $member;
                        public $position;
                        public $position_display;
                        public $appointment_date;
                        public $end_date;
                        public $is_active;
                        public $campus_id;
                        public $communities;
                        
                        public function isCurrentlyActive() {
                            return true;
                        }
                    };
                    
                    $pseudoLeader->id = 'pastor_' . $pastorUser->id;
                    $pseudoLeader->member_id = $pastorUser->member_id;
                    $pseudoLeader->member = $pastorUser->member;
                    $pseudoLeader->position = 'pastor';
                    $pseudoLeader->position_display = 'Mchungaji Mkuu';
                    $pseudoLeader->appointment_date = \Carbon\Carbon::parse($pastorUser->created_at ?? now());
                    $pseudoLeader->end_date = null;
                    $pseudoLeader->is_active = true;
                    $pseudoLeader->campus_id = null;
                    $pseudoLeader->communities = collect();
                    
                    $seniorPastorLeaders->push($pseudoLeader);
                }
            }
        }
        
        // Combine leaders from leaders table and senior pastors
        $allLeaders = $allLeaders->merge($seniorPastorLeaders);
        
        // Filter leaders based on position and member's location
        $leaders = $allLeaders->filter(function($leader) use ($memberCampusId, $memberCommunityId) {
            // Always show Pastor and Assistant Pastor
            if (in_array($leader->position, ['pastor', 'assistant_pastor'])) {
                return true;
            }
            
            // Always show Secretary and Assistant Secretary
            if (in_array($leader->position, ['secretary', 'assistant_secretary'])) {
                return true;
            }
            
            // For Evangelism Leaders: Only show if from same campus/branch
            if ($leader->position === 'evangelism_leader') {
                return $leader->campus_id == $memberCampusId;
            }
            
            // For Church Elders: Only show if assigned to member's community
            if ($leader->position === 'elder') {
                if (!$memberCommunityId) {
                    return false; // Member has no community, so don't show any church elders
                }
                // Check if this leader is assigned to the member's community
                // The communities relationship returns communities where this leader is the church_elder_id
                return $leader->communities->contains('id', $memberCommunityId);
            }
            
            // For other positions, show all (or you can add more specific filtering)
            return true;
        })->values(); // Reset keys after filtering

        // Get current member's leadership positions
        $memberPositions = $member->activeLeadershipPositions()
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->get();

        // Group leaders by position
        $leadersByPosition = $leaders->groupBy('position');

        return view('members.leaders', compact('leaders', 'memberPositions', 'leadersByPosition', 'member'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($notificationId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if notification belongs to user (either through member or directly)
        $notification = null;
        
        if ($user->member_id && $user->member) {
            // Try to find notification through member
            $notification = $user->member->notifications()->where('id', $notificationId)->first();
        }
        
        // If not found through member, try user notifications directly (for pastors, admins, etc.)
        if (!$notification) {
            $notification = $user->notifications()->where('id', $notificationId)->first();
        }

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Show password change form
     */
    public function showChangePassword()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        return view('members.change-password');
    }

    /**
     * Update member password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        \Log::info('Member password changed', [
            'user_id' => $user->id,
            'member_id' => $user->member_id,
        ]);

        return redirect()->route('member.change-password')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Show member settings page
     */
    public function settings()
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $member = $user->member;
        
        return view('members.settings', compact('member', 'user'));
    }

    /**
     * Update member profile (photo, phone, email)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Allow access if user has a member_id (includes members, church elders, evangelism leaders, etc.)
        if (!$user->member_id) {
            return redirect()->route('member.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $member = $user->member;

        $validator = Validator::make($request->all(), [
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:members,email,' . $member->id,
        ], [
            'profile_picture.image' => 'The profile picture must be an image.',
            'profile_picture.mimes' => 'The profile picture must be a JPEG, PNG, or JPG file.',
            'profile_picture.max' => 'The profile picture must not be larger than 2MB.',
            'phone_number.max' => 'Phone number must not exceed 20 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use by another member.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updated = false;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            
            // Delete old profile picture if exists (handle both old public path and storage path)
            if ($member->profile_picture) {
                // Check if it's an old public path (assets/images/...)
                if (strpos($member->profile_picture, 'assets/images/') === 0) {
                    $oldPath = public_path($member->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                // Check if it's a storage path (member/profile-pictures/...)
                if (Storage::disk('public')->exists($member->profile_picture)) {
                    Storage::disk('public')->delete($member->profile_picture);
                }
            }
            
            // Save to storage/app/public/member/profile-pictures/ using Laravel Storage
            $profilePicturePath = $file->store('member/profile-pictures', 'public');
            $member->profile_picture = $profilePicturePath;
            $updated = true;
        }

        // Update phone number if provided
        if ($request->filled('phone_number') && $request->phone_number !== $member->phone_number) {
            $member->phone_number = $request->phone_number;
            $updated = true;
        }

        // Update email if provided
        if ($request->filled('email') && $request->email !== $member->email) {
            $member->email = $request->email;
            $updated = true;
        }

        if ($updated) {
            $member->save();
            
            \Log::info('Member profile updated', [
                'user_id' => $user->id,
                'member_id' => $member->id,
            ]);

            return redirect()->route('member.settings')
                ->with('success', 'Profile updated successfully!');
        }

        return redirect()->route('member.settings')
            ->with('info', 'No changes were made.');
    }
}

