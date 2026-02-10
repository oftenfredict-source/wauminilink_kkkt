<?php

namespace App\Http\Controllers;

use App\Models\CommunityOffering;
use App\Models\Community;
use App\Models\SundayService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommunityOfferingController extends Controller
{
    /**
     * Display a listing of community offerings based on user role.
     */
    public function index(Community $community = null)
    {
        $user = auth()->user();

        if ($user->isChurchElder()) {
            // Get all communities where user is elder
            $elderCommunities = $user->elderCommunities();

            // Verify user is elder of this community if community is provided
            if ($community && !$elderCommunities->pluck('id')->contains($community->id)) {
                abort(403, 'You are not authorized to view offerings for this community.');
            }

            // Elder sees their own submissions
            // If community is provided, filter by that community
            // Otherwise, show offerings from all communities they manage
            $offerings = CommunityOffering::where('church_elder_id', $user->id)
                ->when($community, function ($query) use ($community) {
                    return $query->where('community_id', $community->id);
                }, function ($query) use ($elderCommunities) {
                    // If no community specified, show from all their communities
                    return $query->whereIn('community_id', $elderCommunities->pluck('id'));
                })
                ->with(['community', 'service'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // If no community specified but elder has communities, use first one for context
            if (!$community && $elderCommunities->count() > 0) {
                $community = $elderCommunities->first();
            }

            return view('church-elder.community-offerings.index', compact('offerings', 'community'));
        } elseif ($user->isEvangelismLeader()) {
            // Get the evangelism leader's campus
            $campus = $user->getCampus();

            if (!$campus) {
                abort(404, 'Campus not found for this evangelism leader.');
            }

            // Get all community IDs for this campus
            $communityIds = Community::where('campus_id', $campus->id)
                ->pluck('id')
                ->toArray();

            // Leader sees ALL submissions from communities in their campus
            // Show BOTH pending_evangelism (waiting for them) and pending_secretary (forwarded by them)
            $offerings = CommunityOffering::whereIn('community_id', $communityIds)
                ->whereIn('status', ['pending_evangelism', 'pending_secretary'])
                ->with(['community', 'service', 'churchElder'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $confirmedOfferings = CommunityOffering::whereIn('community_id', $communityIds)
                ->where('status', 'completed')
                ->with(['community', 'service'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            // Get consolidated totals (only for offerings they have NOT yet confirmed)
            $consolidatedTotal = CommunityOffering::whereIn('community_id', $communityIds)
                ->where('status', 'pending_evangelism')
                ->sum('amount');
            $consolidatedCount = CommunityOffering::whereIn('community_id', $communityIds)
                ->where('status', 'pending_evangelism')
                ->count();

            return view('evangelism-leader.offerings.index', compact('offerings', 'confirmedOfferings', 'consolidatedTotal', 'consolidatedCount'));
        } elseif ($user->isSecretary() || $user->isAdmin() || $user->isPastor()) {
            // Secretary/Admin/Pastor sees submissions ready for secretary OR still with leader
            $offerings = CommunityOffering::whereIn('status', ['pending_evangelism', 'pending_secretary'])
                ->with(['community', 'service', 'churchElder', 'evangelismLeader'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $completedOfferings = CommunityOffering::where('status', 'completed')
                ->with(['community', 'service'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            return view('secretary.offerings.index', compact('offerings', 'completedOfferings'));
        }

        abort(403);
    }

    /**
     * Show the form for creating a new offering (Church Elder).
     */
    public function create(Community $community)
    {
        $user = auth()->user();
        if (!$user->isChurchElder()) {
            abort(403);
        }

        // Verify user is elder of this community
        if (!$user->elderCommunities()->pluck('id')->contains($community->id)) {
            abort(403, 'You are not authorized to create offerings for this community.');
        }

        // Get communities where user is elder
        $communities = $user->elderCommunities();
        $campusIds = $communities->pluck('campus_id')->unique();

        // Get services: 
        // 1. Sunday Services from the elder's campus(es)
        // 2. Mid-week services led by this elder
        $midWeekTypes = ['prayer_meeting', 'bible_study', 'youth_service', 'women_fellowship', 'men_fellowship', 'evangelism'];
        $serviceTypes = array_merge(['sunday_service'], $midWeekTypes);

        $services = SundayService::whereIn('service_type', $serviceTypes)
            ->whereIn('campus_id', $campusIds)
            ->where('service_date', '>=', now()->subDays(30))
            ->orderBy('service_date', 'desc')
            ->get();

        $offeringCategories = $this->getOfferingCategories();

        return view('church-elder.community-offerings.create', compact('communities', 'services', 'community', 'offeringCategories'));
    }

    /**
     * Show form to create offering from a specific service
     */
    public function createFromService(Community $community, SundayService $service)
    {
        $user = auth()->user();
        if (!$user->isChurchElder()) {
            abort(403);
        }

        // Verify user is elder of this community
        if (!$user->elderCommunities()->pluck('id')->contains($community->id)) {
            abort(403, 'You are not authorized to create offerings for this community.');
        }

        // Verify service belongs to this elder OR belongs to the same campus as the elder's communities
        $elderCampusIds = $user->elderCommunities()->pluck('campus_id')->unique();
        if ($service->church_elder_id != $user->member_id && !$elderCampusIds->contains($service->campus_id)) {
            abort(403, 'This service does not belong to you or your campus.');
        }

        // Check if offering already exists for this service
        $existingOffering = CommunityOffering::where('service_id', $service->id)
            ->where('community_id', $community->id)
            ->first();

        // Check date and time restriction for offering
        $canRecordOffering = true;
        $timeRestrictionMessage = '';
        $serviceDate = $service->service_date ?? null;
        $startTime = $service->start_time ?? null;
        $now = now();

        if ($serviceDate) {
            // First check if service date has been reached
            $serviceDateOnly = \Carbon\Carbon::parse($serviceDate->format('Y-m-d'))->startOfDay();
            $today = $now->copy()->startOfDay();

            if ($today->lt($serviceDateOnly)) {
                $canRecordOffering = false;
                $timeRestrictionMessage = 'Offering cannot be recorded before the service date. Service date is ' .
                    $serviceDateOnly->format('d/m/Y') . '. Today is ' .
                    $today->format('d/m/Y') . '.';
            } elseif ($startTime) {
                // If date is reached, check if start time has been reached
                try {
                    $timeString = $startTime;
                    if ($startTime instanceof \Carbon\Carbon) {
                        $timeString = $startTime->format('H:i:s');
                    } elseif (is_object($startTime) && method_exists($startTime, 'format')) {
                        $timeString = $startTime->format('H:i:s');
                    } elseif (is_string($startTime)) {
                        if (strlen($startTime) === 5) {
                            $timeString = $startTime . ':00';
                        }
                    }

                    $serviceStartDateTime = \Carbon\Carbon::parse($serviceDate->format('Y-m-d') . ' ' . $timeString);

                    if ($now->lt($serviceStartDateTime)) {
                        $canRecordOffering = false;
                        $timeRestrictionMessage = 'Offering cannot be recorded before the service start time. Service starts at ' .
                            $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' .
                            $now->format('d/m/Y h:i A') . '.';
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to parse service start time for offering restriction', [
                        'service_id' => $service->id,
                        'start_time' => $startTime,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $offeringCategories = $this->getOfferingCategories();

        return view('church-elder.community-offerings.create-from-service', compact('community', 'service', 'existingOffering', 'canRecordOffering', 'timeRestrictionMessage', 'offeringCategories'));
    }

    /**
     * Get offering categories and sub-types for "Other" offerings
     */
    private function getOfferingCategories()
    {
        return [
            'Mapato ya Injili' => [
                'KOLEKTI' => 'Kolekti ya Jumapili',
                'S/SCHOOL' => 'Sadaka ya Sunday School',
                'SHUKRANI' => 'Sadaka ya Shukrani',
                'CHAKULA CHA BWANA' => 'Sadaka ya Chakula cha Bwana',
                'FUNGU LA KUMI' => 'Fungu la Kumi (Zaka)',
                'SHUKRANI YA NDOA' => 'Shukrani ya Ndoa',
                'SHUKRANI UBATIZO' => 'Shukrani ya Ubatizo',
                'MALIMBUKO' => 'Sadaka ya Malimbuko',
                'MAVUNO' => 'Mavuno',
                'HURUMA' => 'Sadaka ya Huruma',
                'MKOPO KUTOKA VIKUNDI' => 'Mkopo kutoka Vikundi',
            ],
            'Mapato ya Vikundi' => [
                'OMBENI KWAYA' => 'Ombeni Kwaya',
                'ALPHA NA OMEGA KWAYA' => 'Alpha na Omega Kwaya',
                'TUMAINI KWAYA' => 'Tumaini Kwaya',
                'SIFUNI KWAYA' => 'Sifuni Kwaya',
                'JUMUIYA' => 'Jumuiya',
                'WANAWAKE' => 'Wanawake',
                'MASIFU YA ASUBUHI' => 'Masifu ya Asubuhi',
                'DIAKONIA/BCC' => 'Diakonia/BCC',
                'VIJANA' => 'Vijana',
                'PRAISE TEAM' => 'Praise Team',
                'UAMSHO' => 'Uamsho',
                'UIMBAJI' => 'Uimbaji',
                'SEMINA/KONGAMANO' => 'Semina/Kongamano',
                'MICHAEL NA WATOTO' => 'Michael na Watoto',
                'NYUMBA YA MAOMBI' => 'Nyumba ya Maombi',
                'UKARIMU WAGENI' => 'Ukarimu Wageni',
                'KUSTAAFISHA WAZEE' => 'Kustaafisha Wazee',
                'USCF KWAYA' => 'USCF Kwaya',
                'KAMBA PORI' => 'Kamba Pori',
                'MCHANGO KIPAIMARA' => 'Mchango Kipaimara',
                'MCHANGO MAALUMU' => 'Mchango Maalumu',
                'VITI WATOTO S/SCHOOL' => 'Viti Watoto S/School',
                'MAPAMBO KANISA' => 'Mapambo Kanisa',
            ],
            'Mapato ya Majengo' => [
                'JENGO SENTA' => 'Jengo Senta',
                'JENGO KIFUMBU' => 'Jengo Kifumbu',
                'JENGO MWEKA' => 'Jengo Mweka',
                'JENGO CCP' => 'Jengo CCP',
                'JENGO S/SCHOOL SENTA' => 'Jengo S/School Senta',
                'ALAMA KIPAIMARA' => 'Alama ya Kipaimara',
            ]
        ];
    }

    /**
     * Store a newly created offering (Church Elder).
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isChurchElder()) {
            abort(403);
        }

        $validated = $request->validate([
            'community_id' => 'required|exists:communities,id',
            'service_id' => 'nullable|exists:sunday_services,id',
            'service_type' => 'nullable|string',
            'offering_type' => 'required|in:general,sadaka_umoja,sadaka_jengo,sadaka_ahadi,sunday_offering,tithe,other',
            'other_category' => 'nullable|string',
            'other_subtype' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'offering_date' => 'required|date|before_or_equal:today',
            'collection_method' => 'required|in:cash,mobile_money,bank_transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'elder_notes' => 'nullable|string',
            'items_json' => 'nullable|string',
        ]);

        // Verify user is elder of this community
        if (!$user->elderCommunities()->pluck('id')->contains($validated['community_id'])) {
            return back()->with('error', 'You are not authorized to create offerings for this community.');
        }

        // If items_json is present (for detailed offerings), parse it
        $offeringItems = [];
        if (!empty($validated['items_json'])) {
            $items = json_decode($validated['items_json'], true);
            if (is_array($items) && count($items) > 0) {
                $offeringItems = $items;
                // Recalculate total amount from items to ensure accuracy
                $totalAmount = 0;
                foreach ($items as $item) {
                    if (isset($item['amount'])) {
                        $totalAmount += floatval($item['amount']);
                    } else {
                        // For Sunday Offering combo
                        $totalAmount += floatval($item['amount_umoja'] ?? 0);
                        $totalAmount += floatval($item['amount_jengo'] ?? 0);
                        $totalAmount += floatval($item['amount_ahadi'] ?? 0);
                        $totalAmount += floatval($item['amount_other'] ?? 0);
                    }
                }
                $validated['amount'] = $totalAmount;
            }
        }

        // Backend Validation: For Sadaka ya Umoja, Sadaka ya Jengo, Sadaka ya Ahadi, and Sunday Offering,
        // the breakdown is MANDATORY. 
        if (in_array($validated['offering_type'], ['sadaka_umoja', 'sadaka_jengo', 'sadaka_ahadi', 'sunday_offering']) && empty($offeringItems)) {
            $typeLabel = $validated['offering_type'];
            if ($typeLabel === 'sadaka_umoja')
                $typeLabel = 'Sadaka ya Umoja';
            if ($typeLabel === 'sadaka_jengo')
                $typeLabel = 'Sadaka ya Jengo';
            if ($typeLabel === 'sadaka_ahadi')
                $typeLabel = 'Ahadi ya Bwana';
            if ($typeLabel === 'sunday_offering')
                $typeLabel = 'Sunday Offering';

            return back()->withInput()->with('error', 'ERROR: Envelope breakdown is required for ' . $typeLabel . '. Please add at least one member envelope.');
        }

        // If service_id is provided, verify it belongs to this elder and check date/time restriction
        if (!empty($validated['service_id'])) {
            $service = SundayService::findOrFail($validated['service_id']);
            // Verify service belongs to this elder OR belongs to the same campus as the elder's communities
            $elderCampusIds = $user->elderCommunities()->pluck('campus_id')->unique();
            if ($service->church_elder_id != $user->member_id && !$elderCampusIds->contains($service->campus_id)) {
                return back()->with('error', 'This service does not belong to you or your campus.');
            }

            // Check date and time restriction
            $serviceDate = $service->service_date ?? null;
            $startTime = $service->start_time ?? null;
            $now = now();

            if ($serviceDate) {
                // First check if service date has been reached
                $serviceDateOnly = \Carbon\Carbon::parse($serviceDate->format('Y-m-d'))->startOfDay();
                $today = $now->copy()->startOfDay();

                if ($today->lt($serviceDateOnly)) {
                    return back()->with('error', 'Offering cannot be recorded before the service date. Service date is ' .
                        $serviceDateOnly->format('d/m/Y') . '. Today is ' .
                        $today->format('d/m/Y') . '.');
                } elseif ($startTime) {
                    // If date is reached, check if start time has been reached
                    try {
                        $timeString = $startTime;
                        if ($startTime instanceof \Carbon\Carbon) {
                            $timeString = $startTime->format('H:i:s');
                        } elseif (is_object($startTime) && method_exists($startTime, 'format')) {
                            $timeString = $startTime->format('H:i:s');
                        } elseif (is_string($startTime)) {
                            if (strlen($startTime) === 5) {
                                $timeString = $startTime . ':00';
                            }
                        }

                        $serviceStartDateTime = \Carbon\Carbon::parse($serviceDate->format('Y-m-d') . ' ' . $timeString);

                        if ($now->lt($serviceStartDateTime)) {
                            return back()->with('error', 'Offering cannot be recorded before the service start time. Service starts at ' .
                                $serviceStartDateTime->format('d/m/Y h:i A') . '. Current time is ' .
                                $now->format('d/m/Y h:i A') . '.');
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to parse service start time for offering restriction', [
                            'service_id' => $service->id,
                            'start_time' => $startTime,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Auto-fill service_type if not provided
            if (empty($validated['service_type'])) {
                $validated['service_type'] = $service->service_type;
            }
        }

        // Map slugs to pretty labels for consistency across reports
        $offeringType = $validated['offering_type'];
        if ($offeringType === 'sadaka_umoja')
            $offeringType = 'Sadaka ya Umoja';
        elseif ($offeringType === 'sadaka_jengo')
            $offeringType = 'Sadaka ya Jengo';
        elseif ($offeringType === 'sadaka_ahadi')
            $offeringType = 'Ahadi ya Bwana';
        elseif ($offeringType === 'sunday_offering')
            $offeringType = 'Sunday Offering';
        elseif ($offeringType === 'other' && !empty($validated['other_subtype']) && $validated['other_subtype'] !== 'other') {
            // Use the specific subtype for reports to pick it up via LIKE query
            $offeringType = $validated['other_subtype'];
        }

        // Initialize session totals
        $sessionTotals = [
            'amount_umoja' => 0,
            'amount_jengo' => 0,
            'amount_ahadi' => 0,
            'amount_other' => 0,
        ];

        // If it's single type, assign it now
        if ($validated['offering_type'] === 'sadaka_umoja') {
            $sessionTotals['amount_umoja'] = $validated['amount'];
        } elseif ($validated['offering_type'] === 'sadaka_jengo') {
            $sessionTotals['amount_jengo'] = $validated['amount'];
        } elseif ($validated['offering_type'] === 'sadaka_ahadi') {
            $sessionTotals['amount_ahadi'] = $validated['amount'];
        } elseif ($validated['offering_type'] === 'general' || $validated['offering_type'] === 'other' || $validated['offering_type'] === 'tithe') {
            $sessionTotals['amount_other'] = $validated['amount'];
        } elseif ($validated['offering_type'] === 'sunday_offering') {
            // For combo, we'll calculate from items
            foreach ($offeringItems as $item) {
                $sessionTotals['amount_umoja'] += floatval($item['amount_umoja'] ?? 0);
                $sessionTotals['amount_jengo'] += floatval($item['amount_jengo'] ?? 0);
                $sessionTotals['amount_ahadi'] += floatval($item['amount_ahadi'] ?? 0);
                $sessionTotals['amount_other'] += floatval($item['amount_other'] ?? 0);
            }
            // Update total amount
            $validated['amount'] = array_sum($sessionTotals);
        }

        $offering = CommunityOffering::create([
            'community_id' => $validated['community_id'],
            'service_id' => $validated['service_id'] ?? null,
            'service_type' => $validated['service_type'] ?? null,
            'offering_type' => $offeringType,
            'amount' => $validated['amount'],
            'amount_umoja' => $sessionTotals['amount_umoja'],
            'amount_jengo' => $sessionTotals['amount_jengo'],
            'amount_ahadi' => $sessionTotals['amount_ahadi'],
            'amount_other' => $sessionTotals['amount_other'],
            'offering_date' => $validated['offering_date'],
            'collection_method' => $validated['collection_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'church_elder_id' => $user->id,
            'status' => 'pending_secretary',
            'notes' => $validated['notes'] ?? null,
            'elder_notes' => $validated['elder_notes'] ?? null,
        ]);

        // Save offering items if present
        if (!empty($offeringItems)) {
            foreach ($offeringItems as $item) {
                // Find member by envelope number (trimmed for robustness)
                $envelope = trim($item['envelope_number']);
                $member = \App\Models\Member::where('envelope_number', $envelope)->first();

                \App\Models\CommunityOfferingItem::create([
                    'community_offering_id' => $offering->id,
                    'member_id' => $member ? $member->id : null,
                    'envelope_number' => $envelope,
                    'amount' => $item['amount'] ?? array_sum([
                        $item['amount_umoja'] ?? 0,
                        $item['amount_jengo'] ?? 0,
                        $item['amount_ahadi'] ?? 0,
                        $item['amount_other'] ?? 0,
                    ]),
                    'amount_umoja' => $item['amount_umoja'] ?? (($validated['offering_type'] === 'sadaka_umoja' && isset($item['amount'])) ? $item['amount'] : 0),
                    'amount_jengo' => $item['amount_jengo'] ?? (($validated['offering_type'] === 'sadaka_jengo' && isset($item['amount'])) ? $item['amount'] : 0),
                    'amount_ahadi' => $item['amount_ahadi'] ?? (($validated['offering_type'] === 'sadaka_ahadi' && isset($item['amount'])) ? $item['amount'] : 0),
                    'amount_other' => $item['amount_other'] ?? (($validated['offering_type'] === 'general' && isset($item['amount'])) ? $item['amount'] : 0),
                ]);
            }
        }

        // Send notification to Secretary
        $this->sendNotificationToSecretary($offering);

        return redirect()->route('church-elder.community-offerings.index', $validated['community_id'])
            ->with('success', 'Offering recorded and submitted to General Secretary.');
    }

    /**
     * Evangelism Leader confirms receipt from Elder.
     */
    public function confirmByLeader(Request $request, CommunityOffering $offering)
    {
        $user = auth()->user();
        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403);
        }

        // Verify the offering belongs to a community in the evangelism leader's campus
        if ($user->isEvangelismLeader()) {
            $campus = $user->getCampus();
            if (!$campus || !$offering->community || $offering->community->campus_id !== $campus->id) {
                abort(403, 'You are not authorized to confirm offerings from communities outside your campus.');
            }
        }

        if ($offering->status !== 'pending_evangelism') {
            return back()->with('error', 'This offering is not pending your confirmation.');
        }

        $validated = $request->validate([
            'leader_notes' => 'nullable|string',
        ]);

        $offering->update([
            'status' => 'pending_secretary',
            'evangelism_leader_id' => $user->id,
            'handover_to_evangelism_at' => now(),
            'leader_notes' => $validated['leader_notes'] ?? null,
        ]);

        // Send notification to Church Elder and General Secretary
        $this->sendNotificationToElder($offering, 'confirmed');
        $this->sendNotificationToSecretary($offering);

        return back()->with('success', 'Offering confirmed and forwarded to Secretary.');
    }

    /**
     * Evangelism Leader rejects an offering
     */
    public function rejectByLeader(Request $request, CommunityOffering $offering)
    {
        $user = auth()->user();
        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403);
        }

        // Verify the offering belongs to a community in the evangelism leader's campus
        if ($user->isEvangelismLeader()) {
            $campus = $user->getCampus();
            if (!$campus || !$offering->community || $offering->community->campus_id !== $campus->id) {
                abort(403, 'You are not authorized to reject offerings from communities outside your campus.');
            }
        }

        if ($offering->status !== 'pending_evangelism') {
            return back()->with('error', 'This offering cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        try {
            $offering->update([
                'status' => 'rejected',
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Send notification to Church Elder
            $this->sendNotificationToElder($offering, 'rejected');

            Log::info('Offering rejected successfully', [
                'offering_id' => $offering->id,
                'rejected_by' => $user->id,
                'status' => $offering->status,
            ]);

            return back()->with('success', 'Offering rejected. Church Elder has been notified.');
        } catch (\Exception $e) {
            Log::error('Failed to reject offering', [
                'offering_id' => $offering->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to reject offering: ' . $e->getMessage());
        }
    }

    /**
     * Bulk confirm multiple offerings
     */
    public function bulkConfirmByLeader(Request $request)
    {
        $user = auth()->user();
        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'offering_ids' => 'required|array',
            'offering_ids.*' => 'exists:community_offerings,id',
        ]);

        // Get the evangelism leader's campus and filter offerings
        $query = CommunityOffering::whereIn('id', $validated['offering_ids'])
            ->where('status', 'pending_evangelism');

        if ($user->isEvangelismLeader()) {
            $campus = $user->getCampus();
            if (!$campus) {
                abort(404, 'Campus not found for this evangelism leader.');
            }

            // Get all community IDs for this campus
            $communityIds = Community::where('campus_id', $campus->id)
                ->pluck('id')
                ->toArray();

            $query->whereIn('community_id', $communityIds);
        }

        $offerings = $query->get();

        $confirmedCount = 0;
        foreach ($offerings as $offering) {
            $offering->update([
                'status' => 'pending_secretary',
                'evangelism_leader_id' => $user->id,
                'handover_to_evangelism_at' => now(),
            ]);
            $confirmedCount++;
            $this->sendNotificationToElder($offering, 'confirmed');
        }

        // Send consolidated notification to Secretary
        if ($confirmedCount > 0) {
            $this->sendConsolidatedNotificationToSecretary($offerings);
        }

        return back()->with('success', "Successfully confirmed {$confirmedCount} offering(s) and forwarded to Secretary.");
    }

    /**
     * Secretary confirms receipt from Evangelism Leader (Finalize).
     */
    public function confirmBySecretary(Request $request, CommunityOffering $offering)
    {
        $user = auth()->user();
        if (!$user->isSecretary() && !$user->isAdmin()) {
            abort(403);
        }

        if ($offering->status !== 'pending_secretary') {
            return back()->with('error', 'This offering is not pending secretary confirmation.');
        }

        $validated = $request->validate([
            'secretary_notes' => 'nullable|string',
        ]);

        $offering->update([
            'status' => 'completed',
            'secretary_id' => $user->id,
            'handover_to_secretary_at' => now(),
            'secretary_notes' => $validated['secretary_notes'] ?? null,
        ]);

        // Send notification to Evangelism Leader and Church Elder
        $this->sendNotificationToLeader($offering, 'finalized');
        $this->sendNotificationToElder($offering, 'finalized');

        return back()->with('success', 'Offering receipt confirmed and finalized.');
    }

    /**
     * Show consolidated view for Evangelism Leader
     */
    public function consolidated()
    {
        $user = auth()->user();
        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403);
        }

        // Get the evangelism leader's campus
        $campus = $user->getCampus();

        if (!$campus) {
            abort(404, 'Campus not found for this evangelism leader.');
        }

        // Get all community IDs for this campus
        $communityIds = Community::where('campus_id', $campus->id)
            ->pluck('id')
            ->toArray();

        $offerings = CommunityOffering::where('evangelism_leader_id', $user->id)
            ->where('status', 'pending_secretary')
            ->whereIn('community_id', $communityIds)
            ->with(['community', 'service', 'churchElder'])
            ->orderBy('offering_date', 'asc')
            ->get();

        // Group by community
        $groupedByCommunity = $offerings->groupBy('community_id');

        // Group by service type
        $groupedByServiceType = $offerings->groupBy('service_type');

        $totalAmount = $offerings->sum('amount');
        $totalCount = $offerings->count();

        return view('evangelism-leader.offerings.consolidated', compact(
            'offerings',
            'groupedByCommunity',
            'groupedByServiceType',
            'totalAmount',
            'totalCount'
        ));
    }

    /**
     * Show details of a specific offering
     */
    public function show(CommunityOffering $offering)
    {
        $user = auth()->user();

        // Check permissions based on role
        if ($user->isChurchElder()) {
            if ($offering->church_elder_id != $user->id) {
                abort(403);
            }
        } elseif ($user->isEvangelismLeader()) {
            // Verify the offering belongs to a community in the evangelism leader's campus
            $campus = $user->getCampus();
            if (!$campus || !$offering->community || $offering->community->campus_id !== $campus->id) {
                abort(403, 'You are not authorized to view offerings from communities outside your campus.');
            }
            // Allowed to view all status
        } elseif ($user->isSecretary() || $user->isAdmin() || $user->isPastor()) {
            // Secretary, Admin, Pastor can view all
        } else {
            abort(403);
        }

        $offering->load(['community', 'service', 'churchElder', 'evangelismLeader', 'secretary', 'rejectedBy', 'items.member']);

        return view('community-offerings.show', compact('offering'));
    }

    /**
     * Send notification to Evangelism Leader
     */
    private function sendNotificationToEvangelismLeader(CommunityOffering $offering)
    {
        try {
            $evangelismLeaders = User::whereHas('member', function ($query) {
                $query->whereHas('leaders', function ($q) {
                    $q->where('position', 'evangelism_leader')->where('is_active', true);
                });
            })->get();

            foreach ($evangelismLeaders as $leader) {
                // You can implement notification here (email, SMS, database notification)
                Log::info('Notification sent to Evangelism Leader', [
                    'leader_id' => $leader->id,
                    'offering_id' => $offering->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification to Evangelism Leader', [
                'error' => $e->getMessage(),
                'offering_id' => $offering->id,
            ]);
        }
    }

    /**
     * Send notification to Church Elder
     */
    private function sendNotificationToElder(CommunityOffering $offering, $action)
    {
        try {
            $elder = $offering->churchElder;
            if ($elder) {
                Log::info('Notification sent to Church Elder', [
                    'elder_id' => $elder->id,
                    'offering_id' => $offering->id,
                    'action' => $action,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification to Church Elder', [
                'error' => $e->getMessage(),
                'offering_id' => $offering->id,
            ]);
        }
    }

    /**
     * Send notification to General Secretary
     */
    private function sendNotificationToSecretary(CommunityOffering $offering)
    {
        try {
            $secretaries = User::where('role', 'secretary')->get();
            foreach ($secretaries as $secretary) {
                Log::info('Notification sent to General Secretary', [
                    'secretary_id' => $secretary->id,
                    'offering_id' => $offering->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification to General Secretary', [
                'error' => $e->getMessage(),
                'offering_id' => $offering->id,
            ]);
        }
    }

    /**
     * Send consolidated notification to Secretary
     */
    private function sendConsolidatedNotificationToSecretary($offerings)
    {
        try {
            $secretaries = User::where('role', 'secretary')->get();
            $totalAmount = $offerings->sum('amount');
            $count = $offerings->count();

            foreach ($secretaries as $secretary) {
                Log::info('Consolidated notification sent to General Secretary', [
                    'secretary_id' => $secretary->id,
                    'total_amount' => $totalAmount,
                    'count' => $count,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send consolidated notification to General Secretary', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to Evangelism Leader
     */
    private function sendNotificationToLeader(CommunityOffering $offering, $action)
    {
        try {
            $leader = $offering->evangelismLeader;
            if ($leader) {
                Log::info('Notification sent to Evangelism Leader', [
                    'leader_id' => $leader->id,
                    'offering_id' => $offering->id,
                    'action' => $action,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification to Evangelism Leader', [
                'error' => $e->getMessage(),
                'offering_id' => $offering->id,
            ]);
        }
    }
}
