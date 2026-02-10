<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AhadiPledge;
use App\Models\Member;
use App\Models\Community;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AhadiPledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AhadiPledge::with(['member', 'community', 'campus']);

        // Filter by member
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        // Filter by item type
        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by fellowship (Jumuiya)
        if ($request->filled('community_id')) {
            $query->where('community_id', $request->community_id);
        }

        // Get all filtered pledges
        $allPledges = $query->orderBy('year', 'desc')->orderBy('created_at', 'desc')->get();
        
        // Group pledges by member
        $groupedPledges = $allPledges->groupBy('member_id')->map(function($memberPledges) {
            $totalPromised = $memberPledges->sum('estimated_value');
            $totalFulfilled = $memberPledges->sum(function($pledge) {
                return ($pledge->quantity_fulfilled / max($pledge->quantity_promised, 1)) * ($pledge->estimated_value ?? 0);
            });
            
            return [
                'member' => $memberPledges->first()->member,
                'community' => $memberPledges->first()->community,
                'pledges' => $memberPledges,
                'total_items' => $memberPledges->count(),
                'total_value' => $totalPromised,
                'overall_progress' => $memberPledges->avg('progress_percentage'),
                'fully_fulfilled_count' => $memberPledges->where('status', 'fully_fulfilled')->count(),
                'partially_fulfilled_count' => $memberPledges->where('status', 'partially_fulfilled')->count(),
                'promised_count' => $memberPledges->where('status', 'promised')->count(),
            ];
        })->values();
        
        // Manual pagination for grouped results
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedGroups = $groupedPledges->slice($offset, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedGroups,
            $groupedPledges->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $members = Member::orderBy('full_name')->get();
        $communities = Community::orderBy('name')->get();
        $itemTypes = AhadiPledge::ITEMS;

        return view('finance.ahadi.index', compact('paginator', 'members', 'communities', 'itemTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'year' => 'required|integer|min:2000|max:2100',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.quantity_promised' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.estimated_value' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $recordedBy = auth()->user()->name ?? 'System';

        foreach ($validated['items'] as $item) {
            AhadiPledge::create([
                'member_id' => $member->id,
                'community_id' => $member->community_id,
                'campus_id' => $member->campus_id,
                'year' => $validated['year'],
                'item_type' => $item['item_type'],
                'quantity_promised' => $item['quantity_promised'],
                'unit' => $item['unit'] ?? null,
                'estimated_value' => $item['estimated_value'] ?? null,
                'status' => 'promised',
                'recorded_by' => $recordedBy,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'In-kind pledges recorded successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AhadiPledge $ahadiPledge)
    {
        return response()->json($ahadiPledge);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AhadiPledge $ahadiPledge)
    {
        // Check if this is a fulfillment update or full pledge edit
        if ($request->has('quantity_fulfilled') && !$request->has('item_type')) {
            // Fulfillment update
            $validated = $request->validate([
                'quantity_fulfilled' => 'required|numeric|min:0',
                'fulfillment_date' => 'nullable|date',
                'notes' => 'nullable|string|max:500',
            ]);

            $totalFulfilled = $validated['quantity_fulfilled'];
            $status = 'promised';

            if ($totalFulfilled >= $ahadiPledge->quantity_promised) {
                $status = 'fully_fulfilled';
            } elseif ($totalFulfilled > 0) {
                $status = 'partially_fulfilled';
            }

            $ahadiPledge->update([
                'quantity_fulfilled' => $totalFulfilled,
                'fulfillment_date' => $validated['fulfillment_date'] ?? now(),
                'status' => $status,
                'notes' => $validated['notes'] ?? $ahadiPledge->notes,
            ]);

            return redirect()->back()->with('success', 'Pledge fulfillment updated successfully.');
        } else {
            // Full pledge edit
            $validated = $request->validate([
                'item_type' => 'required|string',
                'quantity_promised' => 'required|numeric|min:0.01',
                'unit' => 'nullable|string|max:50',
                'estimated_value' => 'nullable|numeric|min:0',
                'year' => 'required|integer|min:2000|max:2100',
                'notes' => 'nullable|string|max:500',
            ]);

            $ahadiPledge->update($validated);

            return redirect()->back()->with('success', 'Pledge updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AhadiPledge $ahadiPledge)
    {
        $ahadiPledge->delete();
        return redirect()->back()->with('success', 'Pledge removed successfully.');
    }

    /**
     * Display a report of in-kind pledges.
     */
    public function reports(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Summary by Item Type
        $itemSummaries = AhadiPledge::where('year', $year)
            ->select('item_type', 'unit',
                \DB::raw('count(*) as total_pledges'),
                \DB::raw('sum(quantity_promised) as total_promised'),
                \DB::raw('sum(quantity_fulfilled) as total_fulfilled'),
                \DB::raw('sum(estimated_value) as total_value')
            )
            ->groupBy('item_type', 'unit')
            ->get();

        // Summary by Community
        $communitySummaries = AhadiPledge::where('year', $year)
            ->join('communities', 'ahadi_pledges.community_id', '=', 'communities.id')
            ->select('communities.name as community_name',
                \DB::raw('count(*) as total_pledges'),
                \DB::raw('sum(estimated_value) as total_estimated_value'),
                \DB::raw('sum(CASE WHEN status = "fully_fulfilled" THEN 1 ELSE 0 END) as fully_fulfilled_count'),
                \DB::raw('sum(CASE WHEN status = "partially_fulfilled" THEN 1 ELSE 0 END) as partially_fulfilled_count'),
                \DB::raw('sum(CASE WHEN status = "promised" THEN 1 ELSE 0 END) as promised_count')
            )
            ->groupBy('communities.name')
            ->get();

        // Fulfillment status counts for charts
        $statusCounts = AhadiPledge::where('year', $year)
            ->select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Years available for filtering
        $availableYears = AhadiPledge::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if ($availableYears->isEmpty()) $availableYears = [date('Y')];

        return view('finance.ahadi.reports', compact('itemSummaries', 'communitySummaries', 'statusCounts', 'year', 'availableYears'));
    }
}
