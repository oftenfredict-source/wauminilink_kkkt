<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Campus;
use App\Models\Community;
use App\Models\Child;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SocialWelfareReportController extends Controller
{
    /**
     * Check if the logged-in user has permission to access welfare reports.
     * Only Pastors and Admins are allowed.
     */
    private function checkPermission()
    {
        if (!auth()->check()) {
            abort(401, 'Please log in to access this page.');
        }
        
        $user = auth()->user();
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Only Pastors and Admins can access social welfare reports.');
        }
    }

    /**
     * Display the welfare reports dashboard.
     */
    public function index()
    {
        $this->checkPermission();
        
        // Summary Statistics
        $totalOrphans = Member::where('orphan_status', '!=', 'not_orphan')->count();
        $totalDisabled = Member::where('disability_status', true)->count();
        $totalVulnerable = Member::where('vulnerable_status', true)->count();
        
        // Breakdown for Orphans
        $orphanBreakdown = Member::where('orphan_status', '!=', 'not_orphan')
            ->select('orphan_status', DB::raw('count(*) as total'))
            ->groupBy('orphan_status')
            ->pluck('total', 'orphan_status')
            ->toArray();
            
        // Breakdown for Disability Types (Top 5)
        $disabilityTypes = Member::where('disability_status', true)
            ->whereNotNull('disability_type')
            ->select('disability_type', DB::raw('count(*) as total'))
            ->groupBy('disability_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
            
        // Breakdown for Vulnerable Types (Top 5)
        $vulnerableTypes = Member::where('vulnerable_status', true)
            ->whereNotNull('vulnerable_type')
            ->select('vulnerable_type', DB::raw('count(*) as total'))
            ->groupBy('vulnerable_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Summary Statistics for Children
        $totalChildOrphans = Child::where('orphan_status', '!=', 'not_orphan')->count();
        $totalChildDisabled = Child::where('disability_status', true)->count();
        $totalChildVulnerable = Child::where('vulnerable_status', true)->count();
        
        // Breakdown for Child Orphans
        $childOrphanBreakdown = Child::where('orphan_status', '!=', 'not_orphan')
            ->select('orphan_status', DB::raw('count(*) as total'))
            ->groupBy('orphan_status')
            ->pluck('total', 'orphan_status')
            ->toArray();

        return view('reports.welfare.index', compact(
            'totalOrphans', 
            'totalDisabled', 
            'totalVulnerable',
            'orphanBreakdown',
            'disabilityTypes',
            'vulnerableTypes',
            'totalChildOrphans',
            'totalChildDisabled',
            'totalChildVulnerable',
            'childOrphanBreakdown'
        ));
    }

    /**
     * Display the Orphans Report.
     */
    public function orphanReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Member::where('orphan_status', '!=', 'not_orphan')
            ->with(['campus', 'community']);

        // Filtering
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('orphan_status')) {
            $query->where('orphan_status', $request->orphan_status);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $orphans = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.orphans', compact('orphans', 'campuses'));
    }

    /**
     * Display the Disability Report.
     */
    public function disabilityReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Member::where('disability_status', true)
            ->with(['campus', 'community']);

        // Filtering
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('disability_type')) {
            $query->where('disability_type', 'like', "%{$request->disability_type}%");
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.disabilities', compact('members', 'campuses'));
    }

    /**
     * Display the Vulnerable Members Report.
     */
    public function vulnerableReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Member::where('vulnerable_status', true)
            ->with(['campus', 'community']);

        // Filtering
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('vulnerable_type')) {
            $query->where('vulnerable_type', 'like', "%{$request->vulnerable_type}%");
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.vulnerable', compact('members', 'campuses'));
    }

    /**
     * Display the Children Orphans Report.
     */
    public function childrenOrphanReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Child::where('orphan_status', '!=', 'not_orphan')
            ->with(['campus', 'community', 'member']);

        // Filtering
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('orphan_status')) {
            $query->where('orphan_status', $request->orphan_status);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $orphans = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.children_orphans', compact('orphans', 'campuses'));
    }

    /**
     * Display the Children Disability Report.
     */
    public function childrenDisabilityReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Child::where('disability_status', true)
            ->with(['campus', 'community', 'member']);

        // Filtering
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('disability_type')) {
            $query->where('disability_type', 'like', "%{$request->disability_type}%");
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $children = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.children_disabilities', compact('children', 'campuses'));
    }

    /**
     * Display the Children Vulnerable Report.
     */
    public function childrenVulnerableReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Child::where('vulnerable_status', true)
            ->with(['campus', 'community', 'member']);

        // Filtering
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('vulnerable_type')) {
            $query->where('vulnerable_type', 'like', "%{$request->vulnerable_type}%");
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $children = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.children_vulnerable', compact('children', 'campuses'));
    }

    /**
     * Display the Consolidated Children Social Welfare Report.
     */
    public function childrenSocialReport(Request $request)
    {
        $this->checkPermission();
        
        $query = Child::where(function($q) {
                $q->where('orphan_status', '!=', 'not_orphan')
                  ->orWhere('disability_status', true)
                  ->orWhere('vulnerable_status', true);
            })
            ->with(['campus', 'community', 'member']);

        // Filtering
        if ($request->filled('category')) {
            $category = $request->category;
            if ($category === 'orphan') {
                $query->where('orphan_status', '!=', 'not_orphan');
            } elseif ($category === 'disabled') {
                $query->where('disability_status', true);
            } elseif ($category === 'vulnerable') {
                $query->where('vulnerable_status', true);
            }
        }
        
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $children = $query->latest()->paginate(20)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.children_social', compact('children', 'campuses'));
    }

    // Export methods will be added here
    public function exportChildrenOrphanReport(Request $request)
    {
        $this->checkPermission();
        
        // Placeholder for export logic
        return back()->with('error', 'Export functionality for children reports is not implemented yet.');
    }

    public function exportChildrenDisabilityReport(Request $request)
    {
        $this->checkPermission();
        
        // Placeholder for export logic
        return back()->with('error', 'Export functionality for children reports is not implemented yet.');
    }

    public function exportChildrenVulnerableReport(Request $request)
    {
        $this->checkPermission();
        
        // Placeholder for export logic
        return back()->with('error', 'Export functionality for children reports is not implemented yet.');
    }

    /**
     * Display the Unified Social Welfare Report (Adults & Children).
     */
    public function unifiedSocialReport(Request $request)
    {
        $this->checkPermission();

        // Query for Adult Members
        $memberQuery = DB::table('members')
            ->select(
                'id',
                'full_name',
                'gender',
                'date_of_birth',
                'orphan_status',
                'disability_status',
                'disability_type',
                'vulnerable_status',
                'vulnerable_type',
                'campus_id',
                'member_id as identifier',
                'guardian_name as guardian',
                'phone_number as contact_phone',
                DB::raw("'Adult' as type")
            )
            ->where(function($q) {
                $q->where('orphan_status', '!=', 'not_orphan')
                  ->orWhere('disability_status', true)
                  ->orWhere('vulnerable_status', true);
            });

        // Query for Children
        $childQuery = DB::table('children')
            ->select(
                'id',
                'full_name',
                'gender',
                'date_of_birth',
                'orphan_status',
                'disability_status',
                'disability_type',
                'vulnerable_status',
                'vulnerable_type',
                'campus_id',
                'member_id as identifier', // Parent member link
                'parent_name as guardian',
                'parent_phone as contact_phone',
                DB::raw("'Child' as type")
            )
            ->where(function($q) {
                $q->where('orphan_status', '!=', 'not_orphan')
                  ->orWhere('disability_status', true)
                  ->orWhere('vulnerable_status', true);
            });

        // Combine using UNION in a subquery for proper pagination and filtering
        $combinedSql = "(" . $memberQuery->toSql() . ") UNION (" . $childQuery->toSql() . ")";
        $combinedQuery = DB::table(DB::raw("({$combinedSql}) as combined"))
            ->mergeBindings($memberQuery)
            ->mergeBindings($childQuery);

        // Filtering
        if ($request->filled('category')) {
            $category = $request->category;
            if ($category === 'orphan') {
                $combinedQuery->where('orphan_status', '!=', 'not_orphan');
            } elseif ($category === 'disabled') {
                $combinedQuery->where('disability_status', 1);
            } elseif ($category === 'vulnerable') {
                $combinedQuery->where('vulnerable_status', 1);
            }
        }
        
        if ($request->filled('campus_id')) {
            $combinedQuery->where('campus_id', $request->campus_id);
        }
        
        if ($request->filled('gender')) {
            $combinedQuery->where('gender', $request->gender);
        }
        
        if ($request->filled('type')) {
            $combinedQuery->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = "%{$request->search}%";
            $combinedQuery->where(function($q) use ($search) {
                $q->where('full_name', 'like', $search)
                  ->orWhere('identifier', 'like', $search);
            });
        }

        $results = $combinedQuery->orderBy('full_name', 'asc')->paginate(25)->withQueryString();
        $campuses = Campus::all();

        return view('reports.welfare.unified', compact('results', 'campuses'));
    }

    public function exportChildrenSocialReport(Request $request)
    {
        $this->checkPermission();
        
        // Placeholder for export logic
        return back()->with('error', 'Export functionality for children reports is not implemented yet.');
    }
}
