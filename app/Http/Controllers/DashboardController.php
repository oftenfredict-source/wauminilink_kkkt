<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SpecialEvent;
use App\Models\Celebration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total members count from database
        $totalMembers = Member::count();
        
        // Get active events count (events that are upcoming or today)
        $activeEvents = SpecialEvent::where('event_date', '>=', now()->toDateString())->count();
        
        // Get upcoming celebrations count (celebrations that are upcoming or today)
        $upcomingCelebrations = Celebration::where('celebration_date', '>=', now()->toDateString())->count();
        
        // You can add more dashboard statistics here in the future
        // For example:
        // $monthlyDonations = Donation::whereMonth('created_at', now()->month)->sum('amount');
        // $upcomingServices = Service::where('date', '>=', now())->count();
        
        return view('layouts.index', compact('totalMembers', 'activeEvents', 'upcomingCelebrations'));
    }
}
