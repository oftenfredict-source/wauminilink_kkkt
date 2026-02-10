<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildToMemberTransition;
use App\Models\Child;
use App\Models\Member;
use App\Models\Campus;
use App\Models\Community;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransitionController extends Controller
{
    /**
     * Display a listing of pending transitions
     */
    public function index()
    {
        $transitions = ChildToMemberTransition::with(['child.member', 'child.campus', 'child.community', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transitions.index', compact('transitions'));
    }

    /**
     * Show transition details
     */
    public function show(ChildToMemberTransition $transition)
    {
        $transition->load(['child.member', 'child.campus', 'child.community', 'reviewer', 'newMember']);
        
        // Get available campuses and communities for selection
        $campuses = Campus::where('is_active', true)
            ->orderBy('is_main_campus', 'desc')
            ->orderBy('name')
            ->get();

        $communities = collect();
        if ($transition->child->campus_id) {
            $communities = Community::where('campus_id', $transition->child->campus_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('transitions.show', compact('transition', 'campuses', 'communities'));
    }

    /**
     * Approve transition and convert child to member
     */
    public function approve(Request $request, ChildToMemberTransition $transition)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'community_id' => 'nullable|exists:communities,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $child = $transition->child;
            
            // Generate member ID
            $memberId = Member::generateMemberId();
            
            // Create new member record from child data
            $memberData = [
                'member_id' => $memberId,
                'campus_id' => $request->campus_id,
                'community_id' => $request->community_id,
                'member_type' => 'independent',
                'membership_type' => 'permanent', // Default to permanent
                'full_name' => $child->full_name,
                'gender' => $child->gender,
                'date_of_birth' => $child->date_of_birth,
                'phone_number' => $child->phone_number ?? ($child->member->phone_number ?? null),
                'email' => $child->member->email ?? null,
                'nida_number' => null, // Can be updated later
                'tribe' => $child->member->tribe ?? null,
                'region' => $child->region ?? ($child->member->region ?? null),
                'district' => $child->district ?? ($child->member->district ?? null),
                'ward' => $child->member->ward ?? null,
                'street' => $child->member->street ?? null,
                'address' => $child->member->address ?? null,
                'residence_region' => $child->region ?? null,
                'residence_district' => $child->district ?? null,
                'residence_ward' => null,
                'residence_street' => null,
                'education_level' => null, // Can be updated later
                'profession' => null, // Can be updated later
                // Preserve parent/guardian relationship as optional reference
                'guardian_name' => $child->getParentName(),
                'guardian_phone' => $child->parent_phone ?? ($child->member->phone_number ?? null),
                'guardian_relationship' => $child->parent_relationship ?? ($child->member ? 'Parent' : null),
            ];

            $newMember = Member::create($memberData);

            // Update transition record
            $transition->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'new_member_id' => $newMember->id,
                'notes' => $request->notes,
            ]);

            // Mark transition as completed
            $transition->update(['status' => 'completed']);

            // Note: We don't delete the child record - it remains for historical reference
            // But we can mark it as transitioned
            $child->update([
                'is_church_member' => false, // No longer a child member
            ]);

            DB::commit();

            Log::info('Child to member transition completed', [
                'child_id' => $child->id,
                'child_name' => $child->full_name,
                'new_member_id' => $newMember->id,
                'new_member_name' => $newMember->full_name,
                'reviewed_by' => auth()->user()->name,
            ]);

            $indexRoute = auth()->user()->isAdmin() ? 'admin.transitions.index' : 'pastor.transitions.index';
            return redirect()->route($indexRoute)
                ->with('success', "Successfully converted {$child->full_name} to independent member (ID: {$memberId}).");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving child to member transition', [
                'transition_id' => $transition->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to approve transition: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Reject transition
     */
    public function reject(Request $request, ChildToMemberTransition $transition)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $transition->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        Log::info('Child to member transition rejected', [
            'transition_id' => $transition->id,
            'child_id' => $transition->child_id,
            'reason' => $request->rejection_reason,
            'reviewed_by' => auth()->user()->name,
        ]);

        $indexRoute = auth()->user()->isAdmin() ? 'admin.transitions.index' : 'pastor.transitions.index';
        return redirect()->route($indexRoute)
            ->with('success', 'Transition request rejected.');
    }
}
