<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total members count from database
        $totalMembers = Member::count();
        
        // You can add more dashboard statistics here in the future
        // For example:
        // $activeEvents = Event::where('status', 'active')->count();
        // $monthlyDonations = Donation::whereMonth('created_at', now()->month)->sum('amount');
        // $upcomingServices = Service::where('date', '>=', now())->count();
        
        return view('layouts.index', compact('totalMembers'));
    }
}
