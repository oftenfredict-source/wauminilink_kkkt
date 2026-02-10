<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TemporaryMembershipController extends Controller
{
    /**
     * Display list of temporary memberships expiring soon or expired
     */
    public function index()
    {
        $this->authorize('viewAny', Member::class);
        
        $now = Carbon::now();
        $thirtyDaysFromNow = $now->copy()->addDays(30);
        
        // Get expiring memberships (within 30 days)
        $expiringMembers = Member::where('membership_type', 'temporary')
            ->where('membership_status', 'active')
            ->whereNotNull('membership_end_date')
            ->whereBetween('membership_end_date', [$now, $thirtyDaysFromNow])
            ->orderBy('membership_end_date', 'asc')
            ->get();
        
        // Get expired memberships
        $expiredMembers = Member::where('membership_type', 'temporary')
            ->where('membership_status', 'active')
            ->whereNotNull('membership_end_date')
            ->where('membership_end_date', '<', $now)
            ->orderBy('membership_end_date', 'desc')
            ->get();
        
        return view('temporary-memberships.index', compact('expiringMembers', 'expiredMembers'));
    }

    /**
     * Show details of a temporary membership
     */
    public function show(Member $member)
    {
        $this->authorize('view', $member);
        
        if ($member->membership_type !== 'temporary') {
            return redirect()->route('temporary-memberships.index')
                ->with('error', 'This member does not have a temporary membership.');
        }
        
        return view('temporary-memberships.show', compact('member'));
    }

    /**
     * Extend temporary membership
     */
    public function extend(Request $request, Member $member)
    {
        $this->authorize('update', $member);
        
        $request->validate([
            'duration_value' => 'required|integer|min:1|max:120',
            'duration_unit' => 'required|in:months,years',
        ]);
        
        if ($member->membership_type !== 'temporary') {
            return response()->json([
                'success' => false,
                'message' => 'This member does not have a temporary membership.'
            ], 422);
        }
        
        $durationValue = $request->duration_value;
        $durationUnit = $request->duration_unit;
        
        // Calculate new duration in months
        if ($durationUnit === 'years') {
            $durationMonths = $durationValue * 12;
        } else {
            $durationMonths = $durationValue;
        }
        
        // Calculate new end date from current end date (or today if expired)
        $startDate = $member->membership_end_date && $member->membership_end_date->isFuture() 
            ? $member->membership_end_date 
            : now();
        $endDate = $startDate->copy()->addMonths($durationMonths);
        
        // Update member
        $member->membership_duration_months = ($member->membership_duration_months ?? 0) + $durationMonths;
        $member->membership_end_date = $endDate;
        $member->membership_status = 'extended';
        $member->save();
        
        // Log the action
        Log::info('Temporary membership extended', [
            'member_id' => $member->id,
            'extended_by' => Auth::id(),
            'new_end_date' => $endDate->format('Y-m-d'),
            'duration_added' => $durationMonths . ' months'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Membership extended successfully.',
            'member' => $member->fresh()
        ]);
    }

    /**
     * Convert temporary membership to permanent
     */
    public function convertToPermanent(Member $member)
    {
        $this->authorize('update', $member);
        
        if ($member->membership_type !== 'temporary') {
            return response()->json([
                'success' => false,
                'message' => 'This member does not have a temporary membership.'
            ], 422);
        }
        
        // Update member
        $member->membership_type = 'permanent';
        $member->membership_status = 'converted';
        $member->membership_duration_months = null;
        $member->membership_start_date = null;
        $member->membership_end_date = null;
        $member->save();
        
        // Log the action
        Log::info('Temporary membership converted to permanent', [
            'member_id' => $member->id,
            'converted_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Membership converted to permanent successfully.',
            'member' => $member->fresh()
        ]);
    }

    /**
     * Mark temporary membership as completed/left
     */
    public function markCompleted(Member $member)
    {
        $this->authorize('update', $member);
        
        if ($member->membership_type !== 'temporary') {
            return response()->json([
                'success' => false,
                'message' => 'This member does not have a temporary membership.'
            ], 422);
        }
        
        // Update member
        $member->membership_status = 'completed';
        $member->save();
        
        // Log the action
        Log::info('Temporary membership marked as completed', [
            'member_id' => $member->id,
            'marked_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Membership marked as completed successfully.',
            'member' => $member->fresh()
        ]);
    }
}
