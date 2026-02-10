<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Leader;
use App\Models\Campus;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UsharikaDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display Usharika dashboard (all branches overview)
     */
    public function index()
    {
        $user = Auth::user();
        $campus = $user->getCampus();

        // Check if user is Usharika admin or main campus user
        if (!$campus || !$campus->is_main_campus) {
            // If branch user, redirect to branch dashboard
            if ($campus && !$campus->is_main_campus) {
                return redirect()->route('branch.dashboard');
            }
            abort(403, 'Only Usharika administrators can access this dashboard.');
        }

        // Get all branches
        $branches = Campus::where('is_main_campus', false)
            ->where('is_active', true)
            ->withCount('members')
            ->orderBy('name')
            ->get();

        // Get main campus stats
        $mainCampus = $campus;
        $mainCampusStats = $this->getCampusStatistics($mainCampus);

        // Get branch statistics
        $branchStats = [];
        foreach ($branches as $branch) {
            $branchStats[] = [
                'branch' => $branch,
                'stats' => $this->getCampusStatistics($branch),
                'new_members_this_month' => Member::where('campus_id', $branch->id)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'communities_count' => Community::where('campus_id', $branch->id)
                    ->where('is_active', true)
                    ->count(),
            ];
        }

        // Total statistics across all branches (including children who are church members)
        $campusIds = $branches->pluck('id')->merge([$mainCampus->id]);
        $adultMembers = Member::whereIn('campus_id', $campusIds)->count();
        $childMembers = \App\Models\Child::where('is_church_member', true)
            ->whereHas('member', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->count();
        $totalMembers = $adultMembers + $childMembers;
        $newMembersThisMonth = Member::whereIn('campus_id', $branches->pluck('id')->merge([$mainCampus->id]))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Recent registrations across all branches
        $recentMembers = Member::whereIn('campus_id', $branches->pluck('id')->merge([$mainCampus->id]))
            ->with('campus')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('usharika.dashboard', compact(
            'mainCampus',
            'branches',
            'mainCampusStats',
            'branchStats',
            'totalMembers',
            'newMembersThisMonth',
            'recentMembers'
        ));
    }

    /**
     * Get statistics for a campus
     */
    private function getCampusStatistics($campus)
    {
        $adultMembers = Member::where('campus_id', $campus->id)->count();
        $childMembers = \App\Models\Child::where('is_church_member', true)
            ->whereHas('member', function($query) use ($campus) {
                $query->where('campus_id', $campus->id);
            })
            ->count();
        $totalMembers = $adultMembers + $childMembers;
        
        $totalLeaders = Leader::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->count();

        return [
            'total_members' => $totalMembers,
            'total_leaders' => $totalLeaders,
        ];
    }
}



