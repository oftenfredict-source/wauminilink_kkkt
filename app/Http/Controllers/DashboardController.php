<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Child;
use App\Models\SpecialEvent;
use App\Models\Celebration;
use App\Models\ServiceAttendance;
use App\Models\SundayService;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        \Log::info('DashboardController@index called');
        
        // Get basic member counts
        $registeredMembers = Member::count();
        
        // Get active events count (events that are upcoming or today)
        $activeEvents = SpecialEvent::where('event_date', '>=', now()->toDateString())->count();
        
        // Get upcoming celebrations count (celebrations that are upcoming or today)
        $upcomingCelebrations = Celebration::where('celebration_date', '>=', now()->toDateString())->count();
        
        // Latest announcements (latest 5 active announcements)
        $latestAnnouncements = Announcement::active()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Upcoming events list (next 5)
        $upcomingEvents = SpecialEvent::where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(5)
            ->get();

        // Monthly finance analytics (approved only where applicable)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyTithes = Tithe::whereMonth('tithe_date', $currentMonth)
            ->whereYear('tithe_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');

        $monthlyOfferings = Offering::whereMonth('offering_date', $currentMonth)
            ->whereYear('offering_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');

        $monthlyDonations = Donation::whereMonth('donation_date', $currentMonth)
            ->whereYear('donation_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');

        $monthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->where('approval_status', 'approved')
            ->sum('amount');

        $netIncome = ($monthlyTithes + $monthlyOfferings + $monthlyDonations) - $monthlyExpenses;

        // Calculate family-inclusive demographics
        $familyDemographics = $this->calculateFamilyDemographics();
        
        return view('dashboard', compact(
            'registeredMembers',
            'activeEvents', 
            'upcomingCelebrations',
            'latestAnnouncements',
            'upcomingEvents',
            'monthlyTithes',
            'monthlyOfferings',
            'monthlyDonations',
            'monthlyExpenses',
            'netIncome'
        ) + $familyDemographics);
    }
    
    private function calculateFamilyDemographics()
    {
        // Get registered members demographics (case-insensitive)
        $maleMembers = Member::whereRaw('LOWER(gender) = ?', ['male'])->count();
        $femaleMembers = Member::whereRaw('LOWER(gender) = ?', ['female'])->count();
        
        // Count spouses - only count spouses who are NOT separate members
        // A spouse is someone who has spouse information but is not a separate member record
        $maleSpouses = Member::whereNotNull('spouse_full_name')
            ->where('spouse_full_name', '!=', '')
            ->where('spouse_member_id', null) // Not a separate member
            ->where(function($query) {
                $query->where('spouse_gender', 'Male')
                      ->orWhere(function($q) {
                          // Fallback: if spouse_gender is null, assume opposite of member gender
                          $q->whereNull('spouse_gender')->whereRaw('LOWER(gender) = ?', ['female']);
                      });
            })
            ->count();
            
        $femaleSpouses = Member::whereNotNull('spouse_full_name')
            ->where('spouse_full_name', '!=', '')
            ->where('spouse_member_id', null) // Not a separate member
            ->where(function($query) {
                $query->where('spouse_gender', 'Female')
                      ->orWhere(function($q) {
                          // Fallback: if spouse_gender is null, assume opposite of member gender
                          $q->whereNull('spouse_gender')->whereRaw('LOWER(gender) = ?', ['male']);
                      });
            })
            ->count();
        
        // Count children from children table (case-insensitive)
        $maleChildren = Child::whereRaw('LOWER(gender) = ?', ['male'])->count();
        $femaleChildren = Child::whereRaw('LOWER(gender) = ?', ['female'])->count();
        
        // Calculate total family members (only registered members + their spouses + children)
        $totalMembers = $maleMembers + $femaleMembers + $maleSpouses + $femaleSpouses + $maleChildren + $femaleChildren;
        
        // Calculate gender totals including family
        $totalMaleMembers = $maleMembers + $maleSpouses + $maleChildren;
        $totalFemaleMembers = $femaleMembers + $femaleSpouses + $femaleChildren;
        
        // Calculate age groups including family
        // Count all adult members (18+) - this includes both main members and spouse members
        $totalAdults = Member::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')->count();
        
        // Count all child members (< 18) plus children from children table
        $childMembers = Member::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')->count();
        $totalChildren = $childMembers + Child::count();
        
        // Debug logging
        \Log::info('Family Demographics Calculation', [
            'maleMembers' => $maleMembers,
            'femaleMembers' => $femaleMembers,
            'maleSpouses' => $maleSpouses,
            'femaleSpouses' => $femaleSpouses,
            'maleChildren' => $maleChildren,
            'femaleChildren' => $femaleChildren,
            'totalMembers' => $totalMembers,
            'totalMaleMembers' => $totalMaleMembers,
            'totalFemaleMembers' => $totalFemaleMembers,
        ]);

        return [
            'totalMembers' => $totalMembers,
            'maleMembers' => $totalMaleMembers,
            'femaleMembers' => $totalFemaleMembers,
            'totalChildren' => $totalChildren,
            'adultMembers' => $totalAdults,
            'registeredMembers' => $registeredMembers ?? Member::count(),
            'familyBreakdown' => [
                'registered_males' => $maleMembers,
                'registered_females' => $femaleMembers,
                'spouse_males' => $maleSpouses,
                'spouse_females' => $femaleSpouses,
                'child_males' => $maleChildren,
                'child_females' => $femaleChildren,
            ]
        ];
    }

    /**
     * Show password change form for leaders (pastor, secretary, treasurer)
     */
    public function showChangePassword()
    {
        $user = Auth::user();
        
        // Check if user is a leader (pastor, secretary, treasurer) or admin
        if (!$user->isPastor() && !$user->isSecretary() && !$user->isTreasurer() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        return view('leaders.change-password');
    }

    /**
     * Update leader password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a leader (pastor, secretary, treasurer) or admin
        if (!$user->isPastor() && !$user->isSecretary() && !$user->isTreasurer() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Unauthorized access.']);
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

        \Log::info('Leader password changed', [
            'user_id' => $user->id,
            'role' => $user->role,
            'name' => $user->name,
        ]);

        return redirect()->route('leader.change-password')
            ->with('success', 'Password changed successfully!');
    }
}
