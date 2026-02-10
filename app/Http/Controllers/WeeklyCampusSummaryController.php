<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommunityOffering;
use App\Models\OfferingCollectionSession;
use App\Models\Campus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyCampusSummaryController extends Controller
{
    /**
     * Display weekly summary for all campuses
     */
    public function index(Request $request)
    {
        // Get date range from request or default to current week
        $startDate = $request->input('start_date', Carbon::now()->startOfWeek());
        $endDate = $request->input('end_date', Carbon::now()->endOfWeek());

        // Convert to Carbon instances if strings
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Get all active campuses
        $campuses = Campus::where('is_active', true)->get();

        $summaryData = [];

        foreach ($campuses as $campus) {
            // Get community offerings (excluding Sunday Offering combo for general total if we want to separate them)
            // Actually, in the UI, 'Community Offerings' usually means the general ones. 
            // In the new system, we distinguish between 'Sunday Offering' (combo) and single types.

            // General Community Offerings (not including the combined Sunday Offering entries)
            $communityOfferingsTotal = CommunityOffering::whereHas('community', function ($query) use ($campus) {
                $query->where('campus_id', $campus->id);
            })
                ->whereBetween('offering_date', [$startDate, $endDate])
                ->whereIn('status', ['received_by_evangelism', 'finalized', 'completed'])
                ->where('offering_type', '!=', 'Sunday Offering')
                ->sum('amount');

            // Get Sunday service offerings from campus flow
            $campusSundayOfferingsTotal = OfferingCollectionSession::where('campus_id', $campus->id)
                ->whereBetween('collection_date', [$startDate, $endDate])
                ->whereIn('status', ['submitted', 'received'])
                ->sum('total_amount');

            // Get Sunday service offerings from community flow (the new combo type)
            $communitySundayOfferingsTotal = CommunityOffering::whereHas('community', function ($query) use ($campus) {
                $query->where('campus_id', $campus->id);
            })
                ->whereBetween('offering_date', [$startDate, $endDate])
                ->whereIn('status', ['received_by_evangelism', 'finalized', 'completed'])
                ->where('offering_type', 'Sunday Offering')
                ->sum('amount');

            $totalSundayOfferings = $campusSundayOfferingsTotal + $communitySundayOfferingsTotal;

            // Get detailed breakdown of Sunday offerings from CAMPUS flow
            $campusSundayBreakdown = DB::table('offering_collection_sessions as ocs')
                ->join('offering_collection_items as oci', 'ocs.id', '=', 'oci.offering_collection_session_id')
                ->where('ocs.campus_id', $campus->id)
                ->whereBetween('ocs.collection_date', [$startDate, $endDate])
                ->whereIn('ocs.status', ['submitted', 'received'])
                ->select(
                    DB::raw('SUM(oci.amount_unity) as unity_total'),
                    DB::raw('SUM(oci.amount_building) as building_total'),
                    DB::raw('SUM(oci.amount_pledge) as pledge_total'),
                    DB::raw('SUM(oci.amount_other) as other_total')
                )
                ->first();

            // Get detailed breakdown of Sunday offerings from COMMUNITY flow
            $communitySundayBreakdown = CommunityOffering::whereHas('community', function ($query) use ($campus) {
                $query->where('campus_id', $campus->id);
            })
                ->whereBetween('offering_date', [$startDate, $endDate])
                ->whereIn('status', ['received_by_evangelism', 'finalized', 'completed', 'pending_secretary'])
                ->select(
                    DB::raw('SUM(amount_umoja) as unity_total'),
                    DB::raw('SUM(amount_jengo) as building_total'),
                    DB::raw('SUM(amount_ahadi) as pledge_total'),
                    DB::raw('SUM(amount_other) as other_total')
                )
                ->first();

            $summaryData[] = [
                'campus' => $campus,
                'community_offerings' => $communityOfferingsTotal,
                'sunday_offerings' => $totalSundayOfferings,
                'sunday_breakdown' => [
                    'unity' => ($campusSundayBreakdown->unity_total ?? 0) + ($communitySundayBreakdown->unity_total ?? 0),
                    'building' => ($campusSundayBreakdown->building_total ?? 0) + ($communitySundayBreakdown->building_total ?? 0),
                    'pledge' => ($campusSundayBreakdown->pledge_total ?? 0) + ($communitySundayBreakdown->pledge_total ?? 0),
                    'other' => ($campusSundayBreakdown->other_total ?? 0) + ($communitySundayBreakdown->other_total ?? 0),
                ],
                'total' => $communityOfferingsTotal + $totalSundayOfferings,
            ];
        }

        // Calculate grand totals
        $grandTotal = [
            'community_offerings' => collect($summaryData)->sum('community_offerings'),
            'sunday_offerings' => collect($summaryData)->sum('sunday_offerings'),
            'total' => collect($summaryData)->sum('total'),
        ];

        return view('finance.weekly-campus-summary', compact('summaryData', 'grandTotal', 'startDate', 'endDate'));
    }
}
