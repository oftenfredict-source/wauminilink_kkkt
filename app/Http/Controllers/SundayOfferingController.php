<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfferingCollectionSession;
use App\Models\OfferingCollectionItem;
use App\Models\Campus;
use App\Models\Community;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SundayOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // List sessions managed by this user or all if admin/treasurer
        $user = Auth::user();

        $query = OfferingCollectionSession::with(['campus', 'leadElder'])
            ->orderBy('collection_date', 'desc');

        if ($user->isChurchElder()) {
            // Get communities where user is elder
            $elderCommunities = $user->elderCommunities();
            $campusIds = $elderCommunities->pluck('campus_id')->unique();

            // Elder can see sessions for campuses they belong to
            $query->whereIn('campus_id', $campusIds);
        }

        $sessions = $query->paginate(10);

        return view('sunday-offering.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource (Step 1).
     */
    public function create()
    {
        $user = Auth::user();

        // Get active Sub-Campuses (Mtaa)
        $query = Campus::where('is_active', true)
            ->whereNotNull('parent_id'); // Only sub-campuses

        if ($user->isChurchElder()) {
            $elderCommunities = $user->elderCommunities();
            $campusIds = $elderCommunities->pluck('campus_id')->unique();
            $query->whereIn('id', $campusIds);
        }

        $campuses = $query->get();

        // If no sub-campuses, maybe usage is different, fallback to all active
        if ($campuses->isEmpty() && !$user->isChurchElder()) {
            $campuses = Campus::where('is_active', true)->get();
        }

        return view('sunday-offering.create', compact('campuses'));
    }

    /**
     * Store a newly created resource in storage (Step 1 -> Step 2).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'collection_date' => 'required|date',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        // Authorization check for Church Elder
        if ($user->isChurchElder()) {
            $elderCommunities = $user->elderCommunities();
            $campusIds = $elderCommunities->pluck('campus_id')->unique();
            if (!$campusIds->contains($request->campus_id)) {
                abort(403, 'You are not authorized to create sessions for this Mtaa.');
            }
        }

        // Check if session already exists for this campus/date
        $exists = OfferingCollectionSession::where('collection_date', $request->collection_date)
            ->where('campus_id', $request->campus_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'A session for this Mtaa and Date already exists.']);
        }

        $session = OfferingCollectionSession::create([
            'collection_date' => $request->collection_date,
            'campus_id' => $request->campus_id,
            'lead_elder_id' => $user->id,
            'status' => 'draft',
            'total_amount' => 0,
        ]);

        return redirect()->route('sunday-offering.entry', $session->id);
    }

    /**
     * Show the form for entering data (Step 2).
     */
    public function entry(OfferingCollectionSession $session)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->isChurchElder()) {
            $elderCommunities = $user->elderCommunities();
            $campusIds = $elderCommunities->pluck('campus_id')->unique();
            if (!$campusIds->contains($session->campus_id)) {
                abort(403, 'You are not authorized to access this session.');
            }
        }

        // Ensure editable
        if ($session->status !== 'draft') {
            return redirect()->route('sunday-offering.show', $session->id)
                ->with('error', 'This session is locked.');
        }

        // Get communities for this campus
        $query = Community::where('campus_id', $session->campus_id)
            ->where('is_active', true);

        // Filter communities for Church Elder (Only show theirs)
        if ($user->isChurchElder()) {
            $elderCommunityIds = $user->elderCommunities()->pluck('id');
            $query->whereIn('id', $elderCommunityIds);
        }

        $communities = $query->orderBy('name')->get();

        // Get existing items keyed by community_id
        $minItems = $session->items->keyBy('community_id');

        return view('sunday-offering.entry', compact('session', 'communities', 'minItems'));
    }

    /**
     * Update the specified resource in storage (Save Step 2).
     */
    public function updateEntry(Request $request, OfferingCollectionSession $session)
    {
        if ($session->status !== 'draft') {
            return abort(403, 'Cannot edit locked session');
        }

        $data = $request->input('items', []);
        $user = Auth::user();

        DB::transaction(function () use ($session, $data, $user) {
            $total = 0;

            // Get elder communities for validation if applicable
            $elderCommunityIds = $user->isChurchElder() ? $user->elderCommunities()->pluck('id')->toArray() : null;

            foreach ($data as $communityId => $amounts) {
                // If elder, only allow their own communities
                if ($elderCommunityIds !== null && !in_array($communityId, $elderCommunityIds)) {
                    continue;
                }

                // amounts: unity, building, pledge, other
                $amtUnity = $amounts['unity'] ?? 0;
                $amtBuilding = $amounts['building'] ?? 0;
                $amtPledge = $amounts['pledge'] ?? 0;
                $amtOther = $amounts['other'] ?? 0;

                $itemTotal = $amtUnity + $amtBuilding + $amtPledge + $amtOther;
                $total += $itemTotal;

                OfferingCollectionItem::updateOrCreate(
                    [
                        'offering_collection_session_id' => $session->id,
                        'community_id' => $communityId
                    ],
                    [
                        'amount_unity' => $amtUnity,
                        'amount_building' => $amtBuilding,
                        'amount_pledge' => $amtPledge,
                        'amount_other' => $amtOther,
                    ]
                );
            }

            // If it's a Church Elder, we must NOT overwrite the entire session total 
            // if other communities have data. We should only increment/update the items.
            // Wait, currently Sunday Sessions are often filled by one person.
            // If multiple people fill it, we need care.

            // Re-calculate session total from all items (including those not in this request)
            $sessionTotal = OfferingCollectionItem::where('offering_collection_session_id', $session->id)->sum(DB::raw('amount_unity + amount_building + amount_pledge + amount_other'));
            $session->update(['total_amount' => $sessionTotal]);
        });

        if ($request->has('save_and_submit')) {
            $session->update(['status' => 'submitted']);
            return redirect()->route('sunday-offering.show', $session->id)
                ->with('success', 'Collection submitted successfully.');
        }

        return redirect()->route('sunday-offering.entry', $session->id)
            ->with('success', 'Draft saved.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfferingCollectionSession $session)
    {
        $session->load(['items.community', 'campus', 'leadElder', 'receivedBy']);
        return view('sunday-offering.show', compact('session'));
    }

    /**
     * General Secretary action to receive/verify.
     */
    public function receive(Request $request, OfferingCollectionSession $session)
    {
        // Enforce Secretary or Admin role
        if (!Auth::user()->isSecretary() && !Auth::user()->isAdmin()) {
            abort(403, 'Only the General Secretary can verify contributions.');
        }

        $session->update([
            'status' => 'received',
            'received_by' => Auth::id(),
            'received_at' => now(),
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Funds acknowledged as received by General Secretary.');
    }
}
