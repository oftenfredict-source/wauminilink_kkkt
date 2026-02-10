<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Campus;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommunityController extends Controller
{
    /**
     * Display a listing of communities for a campus
     */
    public function index(Request $request, Campus $campus)
    {
        $communities = Community::where('campus_id', $campus->id)
            ->orderBy('name')
            ->get();

        return view('communities.index', compact('communities', 'campus'));
    }

    /**
     * Show the form for creating a new community
     */
    public function create(Campus $campus)
    {
        return view('communities.create', compact('campus'));
    }

    /**
     * Store a newly created community
     */
    public function store(Request $request, Campus $campus)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $community = Community::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'campus_id' => $campus->id,
                'is_active' => true,
            ]);

            Log::info('Community created', [
                'community_id' => $community->id,
                'name' => $community->name,
                'campus_id' => $campus->id,
            ]);

            return redirect()->route('campuses.show', $campus)
                ->with('success', 'Community created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating community', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create community: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified community
     */
    public function show(Campus $campus, Community $community)
    {
        // Ensure the community belongs to the campus
        if ($community->campus_id !== $campus->id) {
            return redirect()->route('campuses.show', $campus)
                ->with('error', 'Community not found in this campus.');
        }

        $community->load(['campus', 'members', 'memberChildren.member', 'churchElder.member']);
        
        // Filter children to only include those under 18
        $childMembers = $community->memberChildren()
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')
            ->get();
        
        // Get children 18+ who are church members (should be in transition or treated as adults)
        $adultChildren = $community->memberChildren()
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')
            ->with('pendingTransition')
            ->get();
        
        $memberCount = $community->members()->count();
        $childMemberCount = $childMembers->count();
        $totalMemberCount = $memberCount + $childMemberCount;
        
        // Get members from the same campus who are not assigned to any community or assigned to this community
        $availableMembers = \App\Models\Member::where('campus_id', $campus->id)
            ->where(function($query) use ($community) {
                $query->whereNull('community_id')
                      ->orWhere('community_id', $community->id);
            })
            ->orderBy('full_name')
            ->get();

        // Get available church elders for this campus
        $availableChurchElders = \App\Models\Leader::where('campus_id', $campus->id)
            ->where('position', 'elder')
            ->where('is_active', true)
            ->with('member')
            ->get();

        return view('communities.show', compact('community', 'campus', 'memberCount', 'childMemberCount', 'totalMemberCount', 'childMembers', 'adultChildren', 'availableMembers', 'availableChurchElders'));
    }

    /**
     * Show the form for editing the specified community
     */
    public function edit(Campus $campus, Community $community)
    {
        // Ensure the community belongs to the campus
        if ($community->campus_id !== $campus->id) {
            return redirect()->route('campuses.show', $campus)
                ->with('error', 'Community not found in this campus.');
        }

        // Get available church elders for this campus
        $availableChurchElders = \App\Models\Leader::where('campus_id', $campus->id)
            ->where('position', 'elder')
            ->where('is_active', true)
            ->with('member')
            ->get();

        return view('communities.edit', compact('community', 'campus', 'availableChurchElders'));
    }

    /**
     * Update the specified community
     */
    public function update(Request $request, Campus $campus, Community $community)
    {
        // Ensure the community belongs to the campus
        if ($community->campus_id !== $campus->id) {
            return redirect()->route('campuses.show', $campus)
                ->with('error', 'Community not found in this campus.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $community->update([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : $community->is_active,
            ]);

            Log::info('Community updated', [
                'community_id' => $community->id,
                'name' => $community->name,
            ]);

            return redirect()->route('campuses.show', $campus)
                ->with('success', 'Community updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating community', [
                'community_id' => $community->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update community: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified community (soft delete)
     */
    public function destroy(Campus $campus, Community $community)
    {
        // Ensure the community belongs to the campus
        if ($community->campus_id !== $campus->id) {
            return redirect()->route('campuses.show', $campus)
                ->with('error', 'Community not found in this campus.');
        }

        try {
            // Get member count before deletion
            $memberCount = $community->members()->count();
            
            // Remove community assignment from members (set community_id to null)
            if ($memberCount > 0) {
                Member::where('community_id', $community->id)
                    ->update(['community_id' => null]);
                
                Log::info('Members unassigned from community before deletion', [
                    'community_id' => $community->id,
                    'member_count' => $memberCount,
                ]);
            }

            $community->delete();

            Log::info('Community deleted', [
                'community_id' => $community->id,
                'name' => $community->name,
                'members_unassigned' => $memberCount,
            ]);

            $message = 'Community deleted successfully!';
            if ($memberCount > 0) {
                $message .= " {$memberCount} member(s) have been unassigned from this community.";
            }

            return redirect()->route('campuses.show', $campus)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error deleting community', [
                'community_id' => $community->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete community: ' . $e->getMessage());
        }
    }

    /**
     * Get communities for a campus (JSON endpoint for AJAX)
     */
    public function getCommunitiesJson(Campus $campus)
    {
        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['communities' => $communities]);
    }

    /**
     * Assign church elder to community
     */
    public function assignChurchElder(Request $request, Campus $campus, Community $community)
    {
        // Ensure the community belongs to the campus
        if ($community->campus_id !== $campus->id) {
            return redirect()->route('campuses.show', $campus)
                ->with('error', 'Community not found in this campus.');
        }

        $validator = Validator::make($request->all(), [
            'church_elder_id' => 'nullable|exists:leaders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Validate that the leader belongs to this campus and is an elder
            if ($request->church_elder_id) {
                $leader = \App\Models\Leader::find($request->church_elder_id);
                if (!$leader || $leader->campus_id !== $campus->id || $leader->position !== 'elder') {
                    return redirect()->back()
                        ->with('error', 'Invalid church elder selected.');
                }
            }

            $community->update([
                'church_elder_id' => $request->church_elder_id ?: null,
            ]);

            Log::info('Church elder assigned to community', [
                'community_id' => $community->id,
                'leader_id' => $request->church_elder_id,
            ]);

            return redirect()->route('campuses.communities.show', [$campus, $community])
                ->with('success', $request->church_elder_id ? 'Church elder assigned successfully!' : 'Church elder removed successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning church elder', [
                'community_id' => $community->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to assign church elder: ' . $e->getMessage());
        }
    }

    /**
     * Assign members to this community
     */
    public function assignMembers(Request $request, Campus $campus, Community $community)
    {
        // Ensure the community belongs to the campus
        if ($community->campus_id !== $campus->id) {
            return redirect()->route('campuses.show', $campus)
                ->with('error', 'Community not found in this campus.');
        }

        $validator = Validator::make($request->all(), [
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update members to assign them to this community
            Member::whereIn('id', $request->member_ids)
                ->where('campus_id', $campus->id) // Ensure members belong to the same campus
                ->update(['community_id' => $community->id]);

            Log::info('Members assigned to community', [
                'community_id' => $community->id,
                'member_count' => count($request->member_ids),
            ]);

            return redirect()->route('campuses.communities.show', [$campus, $community])
                ->with('success', count($request->member_ids) . ' member(s) assigned to community successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning members to community', [
                'community_id' => $community->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to assign members: ' . $e->getMessage());
        }
    }
}
