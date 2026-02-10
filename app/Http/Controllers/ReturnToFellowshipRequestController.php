<?php

namespace App\Http\Controllers;

use App\Models\ReturnToFellowshipRequest;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReturnToFellowshipRequestController extends Controller
{
    /**
     * Display a listing of requests for Evangelism Leader
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $campus = $user->getCampus();
        if (!$campus) {
            abort(404, 'Campus not found.');
        }

        $requests = ReturnToFellowshipRequest::where('evangelism_leader_id', $user->id)
            ->with(['pastor', 'churchBranch'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('return-to-fellowship-requests.index', compact('requests', 'campus'));
    }

    /**
     * Show the form for creating a new request
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $campus = $user->getCampus();
        if (!$campus) {
            abort(404, 'Campus not found.');
        }

        return view('return-to-fellowship-requests.create', compact('campus'));
    }

    /**
     * Store a newly created request
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'previously_member' => 'required|boolean',
            'previous_church_branch' => 'nullable|string|max:255',
            'period_away' => 'nullable|string|max:100',
            'reason_for_leaving' => 'nullable|string|max:1000',
            'reason_for_returning' => 'required|string|min:20|max:2000',
            'declaration_agreed' => 'required|accepted',
        ], [
            'declaration_agreed.accepted' => 'You must agree to the declaration statement.',
            'reason_for_returning.min' => 'Please provide a more detailed reason for returning (at least 20 characters).',
        ]);

        try {
            // Get the evangelism leader's campus
            $campus = $user->getCampus();
            if (!$campus) {
                return back()->with('error', 'Campus not found. Please contact administrator.')
                    ->withInput();
            }

            // Convert boolean
            $validated['previously_member'] = (bool) $validated['previously_member'];

            // Create request
            $fellowshipRequest = ReturnToFellowshipRequest::create([
                'full_name' => $validated['full_name'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'email' => $validated['email'] ?? null,
                'church_branch_id' => $campus->id,
                'previously_member' => $validated['previously_member'],
                'previous_church_branch' => $validated['previous_church_branch'] ?? null,
                'period_away' => $validated['period_away'] ?? null,
                'reason_for_leaving' => $validated['reason_for_leaving'] ?? null,
                'reason_for_returning' => $validated['reason_for_returning'],
                'declaration_agreed' => true,
                'evangelism_leader_id' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            Log::info('Return to fellowship request created', [
                'request_id' => $fellowshipRequest->id,
                'applicant_name' => $fellowshipRequest->full_name,
                'evangelism_leader_id' => $user->id
            ]);

            return redirect()->route('evangelism-leader.return-to-fellowship-requests.index')
                ->with('success', 'Return to fellowship request submitted successfully. It has been sent to the Pastor for review.');
        } catch (\Exception $e) {
            Log::error('Error creating return to fellowship request', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return back()->with('error', 'Failed to submit request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified request
     */
    public function show(ReturnToFellowshipRequest $returnToFellowshipRequest)
    {
        $user = auth()->user();

        // Check authorization
        if ($user->isEvangelismLeader()) {
            if ($returnToFellowshipRequest->evangelism_leader_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->isPastor() || $user->isAdmin()) {
            // Pastors and admins can view all requests
        } else {
            abort(403, 'Unauthorized access.');
        }

        $returnToFellowshipRequest->load(['evangelismLeader', 'pastor', 'churchBranch']);

        return view('return-to-fellowship-requests.show', compact('returnToFellowshipRequest'));
    }

    /**
     * Show requests pending pastor review
     */
    public function pending()
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Only Pastors can review requests.');
        }

        $requests = ReturnToFellowshipRequest::where('status', 'pending')
            ->with(['evangelismLeader', 'churchBranch'])
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);

        return view('return-to-fellowship-requests.pending', compact('requests'));
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, ReturnToFellowshipRequest $returnToFellowshipRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($returnToFellowshipRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'nullable|string|max:1000',
        ]);

        $returnToFellowshipRequest->update([
            'status' => 'approved',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request approved successfully.');
    }

    /**
     * Reject a request
     */
    public function reject(Request $request, ReturnToFellowshipRequest $returnToFellowshipRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($returnToFellowshipRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|max:1000',
        ]);

        $returnToFellowshipRequest->update([
            'status' => 'rejected',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request rejected.');
    }

    public function requireCounseling(Request $request, ReturnToFellowshipRequest $returnToFellowshipRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|max:1000',
            'scheduled_counselling_date' => 'required|date|after:now',
        ]);

        $returnToFellowshipRequest->update([
            'status' => 'counseling_required',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'scheduled_counselling_date' => $validated['scheduled_counselling_date'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request marked as requiring counseling and scheduled for ' . Carbon::parse($validated['scheduled_counselling_date'])->format('M d, Y h:i A') . '.');
    }

    /**
     * Approve a request after counseling is complete
     */
    public function approveAfterCounseling(Request $request, ReturnToFellowshipRequest $returnToFellowshipRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($returnToFellowshipRequest->status !== 'counseling_required') {
            return back()->with('error', 'Counselling has not been required for this request.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'nullable|string|max:1000',
        ]);

        $returnToFellowshipRequest->update([
            'status' => 'approved',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'] ?? $returnToFellowshipRequest->pastor_comments,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Counselling complete. Request approved successfully.');
    }
}
