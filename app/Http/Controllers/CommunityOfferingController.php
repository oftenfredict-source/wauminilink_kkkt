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
            if ($community && !$elderCommunities->contains($community->id)) {
                abort(403, 'You are not authorized to view offerings for this community.');
            }
            
            // Elder sees their own submissions
            // If community is provided, filter by that community
            // Otherwise, show offerings from all communities they manage
            $offerings = CommunityOffering::where('church_elder_id', $user->id)
                ->when($community, function($query) use ($community) {
                    return $query->where('community_id', $community->id);
                }, function($query) use ($elderCommunities) {
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
        } 
        elseif ($user->isEvangelismLeader()) {
            // Leader sees submissions from communities they oversee (or all if they oversee all)
            // Include rejected offerings so they can see what was rejected
            $offerings = CommunityOffering::whereIn('status', ['pending_evangelism', 'rejected'])
                ->with(['community', 'service', 'churchElder', 'rejectedBy'])
                ->orderByRaw("CASE WHEN status = 'rejected' THEN 1 ELSE 0 END")
                ->orderBy('offering_date', 'asc')
                ->paginate(10);
                
            $confirmedOfferings = CommunityOffering::where('evangelism_leader_id', $user->id)
                ->where('status', 'pending_secretary')
                ->with(['community', 'service'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            // Get consolidated totals
            $consolidatedTotal = CommunityOffering::where('evangelism_leader_id', $user->id)
                ->where('status', 'pending_secretary')
                ->sum('amount');
            $consolidatedCount = CommunityOffering::where('evangelism_leader_id', $user->id)
                ->where('status', 'pending_secretary')
                ->count();
                
            return view('evangelism-leader.offerings.index', compact('offerings', 'confirmedOfferings', 'consolidatedTotal', 'consolidatedCount'));
        }
        elseif ($user->isSecretary() || $user->isAdmin()) {
            // Secretary sees submissions ready for them
            $offerings = CommunityOffering::where('status', 'pending_secretary')
                ->with(['community', 'service', 'evangelismLeader'])
                ->orderBy('handover_to_evangelism_at', 'asc')
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
        if (!$user->elderCommunities()->contains($community->id)) {
            abort(403, 'You are not authorized to create offerings for this community.');
        }
        
        // Get communities where user is elder
        $communities = $user->elderCommunities();
        
        // Get mid-week services for the communities (services from last 30 days)
        $midWeekServiceTypes = ['prayer_meeting', 'bible_study', 'youth_service', 'women_fellowship', 'men_fellowship', 'evangelism'];
        $services = SundayService::whereIn('service_type', $midWeekServiceTypes)
            ->where('service_date', '>=', now()->subDays(30))
            ->whereHas('churchElder', function($query) use ($user) {
                $query->where('id', $user->member_id);
            })
            ->orderBy('service_date', 'desc')
            ->get();
        
        return view('church-elder.community-offerings.create', compact('communities', 'services', 'community'));
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
        if (!$user->elderCommunities()->contains($community)) {
            abort(403, 'You are not authorized to create offerings for this community.');
        }

        // Verify service belongs to this elder
        if ($service->church_elder_id != $user->member_id) {
            abort(403, 'This service does not belong to you.');
        }

        // Check if offering already exists for this service
        $existingOffering = CommunityOffering::where('service_id', $service->id)
            ->where('community_id', $community->id)
            ->first();

        return view('church-elder.community-offerings.create-from-service', compact('community', 'service', 'existingOffering'));
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
            'amount' => 'required|numeric|min:0',
            'offering_date' => 'required|date|before_or_equal:today',
            'collection_method' => 'required|in:cash,mobile_money,bank_transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'elder_notes' => 'nullable|string',
        ]);

        // Verify user is elder of this community
        if (!$user->elderCommunities()->contains($validated['community_id'])) {
            return back()->with('error', 'You are not authorized to create offerings for this community.');
        }

        // If service_id is provided, verify it belongs to this elder
        if (!empty($validated['service_id'])) {
            $service = SundayService::findOrFail($validated['service_id']);
            if ($service->church_elder_id != $user->member_id) {
                return back()->with('error', 'This service does not belong to you.');
            }
            // Auto-fill service_type if not provided
            if (empty($validated['service_type'])) {
                $validated['service_type'] = $service->service_type;
            }
        }

        $offering = CommunityOffering::create([
            'community_id' => $validated['community_id'],
            'service_id' => $validated['service_id'] ?? null,
            'service_type' => $validated['service_type'] ?? null,
            'amount' => $validated['amount'],
            'offering_date' => $validated['offering_date'],
            'collection_method' => $validated['collection_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'church_elder_id' => $user->id,
            'status' => 'pending_evangelism',
            'notes' => $validated['notes'] ?? null,
            'elder_notes' => $validated['elder_notes'] ?? null,
        ]);

        // Send notification to Evangelism Leader
        $this->sendNotificationToEvangelismLeader($offering);

        // Redirect based on user role
        $user = auth()->user();
        if ($user->isChurchElder()) {
            return redirect()->route('church-elder.community-offerings.index', $validated['community_id'])
                ->with('success', 'Offering recorded and submitted to Evangelism Leader.');
        }
        
        return redirect()->back()
            ->with('success', 'Offering recorded and submitted to Evangelism Leader.');
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

        $offerings = CommunityOffering::whereIn('id', $validated['offering_ids'])
            ->where('status', 'pending_evangelism')
            ->get();

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

        $offerings = CommunityOffering::where('evangelism_leader_id', $user->id)
            ->where('status', 'pending_secretary')
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
            // Can view if pending (they can confirm) or if they confirmed it
            if ($offering->status == 'pending_evangelism' || $offering->evangelism_leader_id == $user->id) {
                // Allowed
            } else {
                abort(403);
            }
        } elseif ($user->isSecretary() || $user->isAdmin()) {
            // Secretary and Admin can view all
        } else {
            abort(403);
        }

        $offering->load(['community', 'service', 'churchElder', 'evangelismLeader', 'secretary', 'rejectedBy']);

        return view('community-offerings.show', compact('offering'));
    }

    /**
     * Send notification to Evangelism Leader
     */
    private function sendNotificationToEvangelismLeader(CommunityOffering $offering)
    {
        try {
            $evangelismLeaders = User::whereHas('member', function($query) {
                $query->whereHas('leaders', function($q) {
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
