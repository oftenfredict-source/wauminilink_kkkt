<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Child;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\CommunityOffering;
use App\Models\Expense;
use App\Models\ServiceAttendance;
use App\Models\SundayService;
use App\Models\SpecialEvent;
use App\Models\Celebration;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'year'); // Default to yearly view
        $year = $request->get('year', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Financial Analytics
        $financialData = $this->getFinancialAnalytics($filter, $year, $startDate, $endDate);
        
        // Member Analytics
        $memberData = $this->getMemberAnalytics($filter, $year, $startDate, $endDate);
        
        // Attendance Analytics removed
        // $attendanceData = $this->getAttendanceAnalytics($filter, $year, $startDate, $endDate);
        
        // Event Analytics
        $eventData = $this->getEventAnalytics($filter, $year, $startDate, $endDate);
        
        // Get all available years for the year filter
        $availableYears = DB::table('tithes')
            ->selectRaw('YEAR(tithe_date) as year')
            ->union(DB::table('offerings')->selectRaw('YEAR(offering_date)'))
            // Donations removed
            ->union(DB::table('expenses')->selectRaw('YEAR(expense_date)'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([Carbon::now()->year]);
        }

        return view('analytics', compact(
            'financialData',
            'memberData',
            // 'attendanceData',
            'eventData',
            'filter',
            'year',
            'startDate',
            'endDate',
            'availableYears'
        ));
    }
    
    private function getFinancialAnalytics($filter, $year, $startDate, $endDate)
    {
        $queryFilter = function($query, $dateColumn) use ($filter, $year, $startDate, $endDate) {
            if ($filter === 'year') {
                $query->whereYear($dateColumn, $year);
            } elseif ($filter === 'custom' && $startDate && $endDate) {
                $query->whereBetween($dateColumn, [$startDate, $endDate]);
            }
        };

        // Total financial summaries for the selected period
        $totalTithes = Tithe::where('approval_status', 'approved')
            ->where(function($q) use ($queryFilter) { $queryFilter($q, 'tithe_date'); })
            ->sum('amount');
        $totalOfferings = Offering::where('approval_status', 'approved')
            ->where(function($q) use ($queryFilter) { $queryFilter($q, 'offering_date'); })
            ->sum('amount');
        $totalCommunityOfferings = CommunityOffering::where('status', 'completed')
            ->where(function($q) use ($queryFilter) { $queryFilter($q, 'offering_date'); })
            ->sum('amount');
        // Donations removed
        $totalExpenses = Expense::where('approval_status', 'approved')
            ->where(function($q) use ($queryFilter) { $queryFilter($q, 'expense_date'); })
            ->sum('amount');
        $netIncome = ($totalTithes + $totalOfferings + $totalCommunityOfferings) - $totalExpenses;
        
        // Monthly trends for the selected period
        $monthlyFinancials = [];
        if ($filter === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::createFromDate($year, $m, 1);
                $monthName = $date->format('M');
                
                $tithes = Tithe::whereYear('tithe_date', $year)
                    ->whereMonth('tithe_date', $m)
                    ->where('approval_status', 'approved')
                    ->sum('amount');
                $offerings = Offering::whereYear('offering_date', $year)
                    ->whereMonth('offering_date', $m)
                    ->where('approval_status', 'approved')
                    ->sum('amount');
                $communityOfferings = CommunityOffering::whereYear('offering_date', $year)
                    ->whereMonth('offering_date', $m)
                    ->where('status', 'completed')
                    ->sum('amount');
                // Donations removed
                $expenses = Expense::whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $m)
                    ->where('approval_status', 'approved')
                    ->sum('amount');
                
                $monthlyFinancials[] = [
                    'month' => $monthName,
                    'tithes' => $tithes,
                    'offerings' => $offerings + $communityOfferings,
                    'expenses' => $expenses,
                    'income' => $tithes + $offerings + $communityOfferings,
                    'net' => ($tithes + $offerings + $communityOfferings) - $expenses
                ];
            }
        } else {
            // Custom/Other views logic if needed
        }
        
        // Yearly trends (last 5 years)
        $yearlyFinancials = [];
        for ($i = 4; $i >= 0; $i--) {
            $y = Carbon::now()->subYears($i)->year;
            
            $tithes = Tithe::whereYear('tithe_date', $y)->where('approval_status', 'approved')->sum('amount');
            $offerings = Offering::whereYear('offering_date', $y)->where('approval_status', 'approved')->sum('amount');
            $communityOfferings = CommunityOffering::whereYear('offering_date', $y)->where('status', 'completed')->sum('amount');
            // Donations removed
            $expenses = Expense::whereYear('expense_date', $y)->where('approval_status', 'approved')->sum('amount');
            
            $yearlyFinancials[] = [
                'year' => $y,
                'tithes' => $tithes,
                'offerings' => $offerings + $communityOfferings,
                'expenses' => $expenses,
                'income' => $tithes + $offerings + $communityOfferings,
                'net' => ($tithes + $offerings + $communityOfferings) - $expenses
            ];
        }
        
        return [
            'totals' => [
                'tithes' => $totalTithes,
                'offerings' => $totalOfferings + $totalCommunityOfferings,
                // 'donations' => $totalDonations,
                'expenses' => $totalExpenses,
                'net_income' => $netIncome
            ],
            'monthly_trends' => $monthlyFinancials,
            'yearly_trends' => $yearlyFinancials
        ];
    }
    
    private function getMemberAnalytics($filter, $year, $startDate, $endDate)
    {
        $queryFilter = function($query, $dateColumn) use ($filter, $year, $startDate, $endDate) {
            if ($filter === 'year') {
                $query->whereYear($dateColumn, $year);
            } elseif ($filter === 'custom' && $startDate && $endDate) {
                $query->whereBetween($dateColumn, [$startDate, $endDate]);
            }
        };

        // Total counts for the period
        $totalMembers = Member::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })->count();
        $maleMembers = Member::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })
            ->whereRaw('LOWER(gender) = ?', ['male'])->count();
        $femaleMembers = Member::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })
            ->whereRaw('LOWER(gender) = ?', ['female'])->count();
        $totalChildren = Child::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })->count();
        
        // Member type distribution
        $memberTypes = Member::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })
            ->selectRaw('member_type, COUNT(*) as count')
            ->groupBy('member_type')
            ->pluck('count', 'member_type');
        
        // Membership type distribution
        $membershipTypes = Member::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })
            ->selectRaw('membership_type, COUNT(*) as count')
            ->groupBy('membership_type')
            ->pluck('count', 'membership_type');
        
        // Monthly registration trends for the selected year
        $monthlyRegistrations = [];
        if ($filter === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::createFromDate($year, $m, 1);
                $count = Member::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->count();
                
                $monthlyRegistrations[] = [
                    'month' => $date->format('M'),
                    'count' => $count
                ];
            }
        }
        
        // Age group distribution
        $ageGroups = Member::where(function($q) use ($queryFilter) { $queryFilter($q, 'created_at'); })
            ->selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN "Under 18"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 25 THEN "18-25"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 26 AND 35 THEN "26-35"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 36 AND 50 THEN "36-50"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 51 AND 65 THEN "51-65"
                    ELSE "65+"
                END as age_group,
                COUNT(*) as count
            ')
            ->groupBy('age_group')
            ->pluck('count', 'age_group');
        
        return [
            'totals' => [
                'total' => $totalMembers,
                'male' => $maleMembers,
                'female' => $femaleMembers,
                'children' => $totalChildren
            ],
            'member_types' => $memberTypes,
            'membership_types' => $membershipTypes,
            'monthly_registrations' => $monthlyRegistrations,
            'age_groups' => $ageGroups
        ];
    }
    
    private function getAttendanceAnalytics($filter, $year, $startDate, $endDate)
    {
        $queryFilter = function($query, $dateColumn) use ($filter, $year, $startDate, $endDate) {
            if ($filter === 'year') {
                $query->whereYear($dateColumn, $year);
            } elseif ($filter === 'custom' && $startDate && $endDate) {
                $query->whereBetween($dateColumn, [$startDate, $endDate]);
            }
        };

        // Total attendance for the period
        $totalAttendance = ServiceAttendance::where(function($q) use ($queryFilter) { $queryFilter($q, 'attended_at'); })->count();
        
        // Monthly attendance trends for the selected year
        $monthlyAttendance = [];
        if ($filter === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::createFromDate($year, $m, 1);
                $count = ServiceAttendance::whereYear('attended_at', $year)
                    ->whereMonth('attended_at', $m)
                    ->count();
                
                $monthlyAttendance[] = [
                    'month' => $date->format('M'),
                    'count' => $count
                ];
            }
        }
        
        // Service type distribution
        $serviceTypes = ServiceAttendance::where(function($q) use ($queryFilter) { $queryFilter($q, 'attended_at'); })
            ->selectRaw('service_type, COUNT(*) as count')
            ->groupBy('service_type')
            ->pluck('count', 'service_type');
        
        // Top attending members (for the selected period)
        $topAttendeesQuery = ServiceAttendance::where(function($q) use ($queryFilter) { $queryFilter($q, 'attended_at'); })
            ->orderBy('attendance_count', 'desc') // This column doesn't exist on ServiceAttendance usually, assuming optimization
            ->limit(10);
            
        // Correcting top attendees logic: Group by member_id
        $topMemberAttendees = ServiceAttendance::where(function($q) use ($queryFilter) { $queryFilter($q, 'attended_at'); })
            ->selectRaw('member_id, COUNT(*) as attendance_count')
            ->whereNotNull('member_id')
            ->groupBy('member_id')
            ->orderBy('attendance_count', 'desc')
            ->with('member')
            ->limit(10)
            ->get()
            ->map(function($att) {
                return [
                    'type' => 'member',
                    'id' => $att->member_id,
                    'name' => $att->member ? $att->member->full_name : 'Unknown',
                    'attendance_count' => $att->attendance_count
                ];
            });
        
        // Average attendance per service
        $avgAttendance = 0;
        $services = SundayService::where(function($q) use ($queryFilter) { $queryFilter($q, 'service_date'); })
            ->orderBy('service_date', 'desc')
            ->get();
            
        if ($services->count() > 0) {
            $avgAttendance = round($services->avg('attendance_count'), 1);
        }
        
        return [
            'total' => $totalAttendance,
            'monthly_trends' => $monthlyAttendance,
            'service_types' => $serviceTypes,
            'top_attendees' => $topMemberAttendees, // Simplified to members mainly
            'average_attendance' => $avgAttendance,
            'recent_services' => $services->take(10)
        ];
    }
    
    private function getEventAnalytics($filter, $year, $startDate, $endDate)
    {
        $queryFilter = function($query, $dateColumn) use ($filter, $year, $startDate, $endDate) {
            if ($filter === 'year') {
                $query->whereYear($dateColumn, $year);
            } elseif ($filter === 'custom' && $startDate && $endDate) {
                $query->whereBetween($dateColumn, [$startDate, $endDate]);
            }
        };

        $totalEvents = SpecialEvent::where(function($q) use ($queryFilter) { $queryFilter($q, 'event_date'); })->count();
        $totalCelebrations = Celebration::where(function($q) use ($queryFilter) { $queryFilter($q, 'celebration_date'); })->count();
        
        // Monthly event trends for the selected year
        $monthlyEvents = [];
        if ($filter === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::createFromDate($year, $m, 1);
                $events = SpecialEvent::whereYear('event_date', $year)
                    ->whereMonth('event_date', $m)
                    ->count();
                $celebrations = Celebration::whereYear('celebration_date', $year)
                    ->whereMonth('celebration_date', $m)
                    ->count();
                
                $monthlyEvents[] = [
                    'month' => $date->format('M'),
                    'events' => $events,
                    'celebrations' => $celebrations,
                    'total' => $events + $celebrations
                ];
            }
        }
        
        return [
            'events' => ['total' => $totalEvents],
            'celebrations' => ['total' => $totalCelebrations],
            'monthly_trends' => $monthlyEvents
        ];
    }
}
