<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchOffering;
use App\Models\CommunityOffering;
use App\Models\Campus;
use App\Models\SundayService;
use App\Models\Community;
use App\Models\Leader;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChurchElderOfferingController extends Controller
{
    /**
     * Display a listing of offerings collected by the elder.
     */
    public function index()
    {
        $user = Auth::user();

        // Get elder communities for filtering
        $elderCommunities = $user->isChurchElder() ? $user->elderCommunities() : collect();
        $elderCommunityIds = $elderCommunities->pluck('id');
        $elderCampusIds = $elderCommunities->pluck('campus_id')->unique();

        // Fetch community offerings for their communities
        $communityOfferings = CommunityOffering::whereIn('community_id', $elderCommunityIds)
            ->with(['community', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch branch (campus) offerings for their campuses
        $branchOfferings = BranchOffering::whereIn('campus_id', $elderCampusIds)
            ->with(['campus', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Combine and sort
        $offerings = $communityOfferings->concat($branchOfferings)->sortByDesc('created_at');

        return view('church-elder.offerings.index', compact('offerings'));
    }

    /**
     * Show the form for creating a new offering collection.
     */
    public function create()
    {
        $user = Auth::user();

        // Only Church Elders can access
        if (!$user->isChurchElder()) {
            abort(403);
        }

        // Get the Elder's assigned communities
        $assignedCommunities = $user->elderCommunities();
        $elderCampusIds = $assignedCommunities->pluck('campus_id')->unique();

        // Get campuses where the elder has a community
        $campuses = Campus::whereIn('id', $elderCampusIds)
            ->where('is_active', true)
            ->get();

        // Get recent services
        $services = SundayService::orderBy('service_date', 'desc')->take(5)->get();

        return view('church-elder.offerings.create', compact('assignedCommunities', 'campuses', 'services'));
    }

    /**
     * Store a newly created offering in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'collection_context' => 'required|in:community,campus',
            'community_id' => 'required_if:collection_context,community',
            'campus_id' => 'required_if:collection_context,campus',
            'amount' => 'required|numeric|min:0',
            'offering_date' => 'required|date',
            'offering_type' => 'required', // 'tithe', 'offering', etc. for Community, or 'main' for Branch
        ]);

        $user = Auth::user();
        if (!$user->isChurchElder()) {
            abort(403);
        }

        $elderCommunities = $user->elderCommunities();

        if ($request->collection_context === 'community') {
            // Verify community ownership
            if (!$elderCommunities->pluck('id')->contains($request->community_id)) {
                abort(403, 'Unauthorized community access.');
            }

            CommunityOffering::create([
                'community_id' => $request->community_id,
                'church_elder_id' => $user->id,
                'amount' => $request->amount,
                'offering_type' => $request->offering_type,
                'offering_date' => $request->offering_date,
                'service_id' => $request->service_id,
                'status' => 'pending_secretary',
                'collection_method' => 'cash',
                'notes' => $request->notes
            ]);
        } else {
            // Verify campus ownership
            $elderCampusIds = $elderCommunities->pluck('campus_id')->unique();
            if (!$elderCampusIds->contains($request->campus_id)) {
                abort(403, 'Unauthorized campus access.');
            }

            BranchOffering::create([
                'campus_id' => $request->campus_id,
                'church_elder_id' => $user->id,
                'amount' => $request->amount,
                'offering_date' => $request->offering_date,
                'service_id' => $request->service_id,
                'status' => 'pending_secretary',
                'collection_method' => 'cash',
                'notes' => $request->notes
            ]);
        }

        return redirect()->route('church-elder.offerings.index')
            ->with('success', 'Offering collected and submitted successfully.');
    }
}
