<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Leader;
use App\Models\Campus;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\SundayService;
use App\Models\ServiceAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BranchDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display branch dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $campus = $user->getCampus();

        if (!$campus) {
            abort(403, 'You must be assigned to a branch to access the dashboard.');
        }

        // If Usharika admin, show Usharika dashboard instead
        if ($campus->is_main_campus && $user->isUsharikaAdmin()) {
            return redirect()->route('usharika.dashboard');
        }

        // Branch dashboard statistics
        $stats = $this->getBranchStatistics($campus);
        
        // Recent members
        $recentMembers = Member::where('campus_id', $campus->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent leaders
        $recentLeaders = Leader::where('campus_id', $campus->id)
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // This month's new members
        $newMembersThisMonth = Member::where('campus_id', $campus->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Member growth (last 6 months)
        $memberGrowth = $this->getMemberGrowth($campus);

        return view('branch.dashboard', compact('campus', 'stats', 'recentMembers', 'recentLeaders', 'newMembersThisMonth', 'memberGrowth'));
    }

    /**
     * Get branch statistics
     */
    private function getBranchStatistics($campus)
    {
        $totalMembers = Member::where('campus_id', $campus->id)->count();
        
        $totalLeaders = Leader::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->count();

        // Financial stats (sum by members in this branch)
        $memberIds = Member::where('campus_id', $campus->id)->pluck('id');
        
        $totalTithes = 0;
        $totalOfferings = 0;
        
        if ($memberIds->count() > 0) {
            $totalTithes = Tithe::whereIn('member_id', $memberIds)
                ->where('approval_status', 'approved')
                ->sum('amount');
                
            $totalOfferings = Offering::whereIn('member_id', $memberIds)
                ->where('approval_status', 'approved')
                ->sum('amount');
        }

        // Attendance stats (sum by members in this branch)
        $totalServices = 0;
        $totalAttendance = 0;
        
        if ($memberIds->count() > 0) {
            // Count services where members from this branch attended
            $totalAttendance = ServiceAttendance::whereIn('member_id', $memberIds)->count();
        }

        return [
            'total_members' => $totalMembers,
            'total_leaders' => $totalLeaders,
            'total_tithes' => $totalTithes,
            'total_offerings' => $totalOfferings,
            'total_services' => $totalServices,
            'total_attendance' => $totalAttendance,
        ];
    }

    /**
     * Get member growth over last 6 months
     */
    private function getMemberGrowth($campus)
    {
        $growth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Member::where('campus_id', $campus->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $growth[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        
        return $growth;
    }
}

