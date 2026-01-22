<?php

namespace App\Http\Controllers;

use App\Models\MarriageBlessingRequest;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MarriageBlessingRequestController extends Controller
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

        $requests = MarriageBlessingRequest::where('evangelism_leader_id', $user->id)
            ->with(['pastor', 'churchBranch'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('marriage-blessing-requests.index', compact('requests', 'campus'));
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

        return view('marriage-blessing-requests.create', compact('campus'));
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
            'husband_full_name' => 'required|string|max:255',
            'wife_full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'marriage_type' => 'nullable|in:customary,civil,traditional,other',
            'marriage_date' => 'required|date|before_or_equal:today',
            'place_of_marriage' => 'nullable|string|max:255',
            'marriage_certificate_number' => 'nullable|string|max:100',
            'both_spouses_members' => 'required|boolean',
            'membership_duration' => 'nullable|string|max:100',
            'attended_marriage_counseling' => 'required|boolean',
            'reason_for_blessing' => 'required|string|min:20|max:2000',
            'declaration_agreed' => 'required|accepted',
        ], [
            'declaration_agreed.accepted' => 'You must agree to the declaration statement.',
            'reason_for_blessing.min' => 'Please provide a more detailed reason for requesting blessing (at least 20 characters).',
        ]);

        try {
            // Get the evangelism leader's campus
            $campus = $user->getCampus();
            if (!$campus) {
                return back()->with('error', 'Campus not found. Please contact administrator.')
                    ->withInput();
            }

            // Convert booleans
            $validated['both_spouses_members'] = (bool) $validated['both_spouses_members'];
            $validated['attended_marriage_counseling'] = (bool) $validated['attended_marriage_counseling'];

            // Create request
            $blessingRequest = MarriageBlessingRequest::create([
                'husband_full_name' => $validated['husband_full_name'],
                'wife_full_name' => $validated['wife_full_name'],
                'phone_number' => $validated['phone_number'] ?? null,
                'email' => $validated['email'] ?? null,
                'church_branch_id' => $campus->id,
                'marriage_type' => $validated['marriage_type'] ?? null,
                'marriage_date' => $validated['marriage_date'],
                'place_of_marriage' => $validated['place_of_marriage'] ?? null,
                'marriage_certificate_number' => $validated['marriage_certificate_number'] ?? null,
                'both_spouses_members' => $validated['both_spouses_members'],
                'membership_duration' => $validated['membership_duration'] ?? null,
                'attended_marriage_counseling' => $validated['attended_marriage_counseling'],
                'reason_for_blessing' => $validated['reason_for_blessing'],
                'declaration_agreed' => true,
                'evangelism_leader_id' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            Log::info('Marriage blessing request created', [
                'request_id' => $blessingRequest->id,
                'husband' => $blessingRequest->husband_full_name,
                'wife' => $blessingRequest->wife_full_name,
                'evangelism_leader_id' => $user->id
            ]);

            return redirect()->route('evangelism-leader.marriage-blessing-requests.index')
                ->with('success', 'Marriage blessing request submitted successfully. It has been sent to the Pastor for review.');
        } catch (\Exception $e) {
            Log::error('Error creating marriage blessing request', [
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
    public function show(MarriageBlessingRequest $marriageBlessingRequest)
    {
        $user = auth()->user();
        
        if ($user->isEvangelismLeader()) {
            if ($marriageBlessingRequest->evangelism_leader_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->isPastor() || $user->isAdmin()) {
            // Pastors and admins can view all requests
        } else {
            abort(403, 'Unauthorized access.');
        }

        $marriageBlessingRequest->load(['evangelismLeader', 'pastor', 'churchBranch']);

        return view('marriage-blessing-requests.show', compact('marriageBlessingRequest'));
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

        $requests = MarriageBlessingRequest::where('status', 'pending')
            ->with(['evangelismLeader', 'churchBranch'])
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);

        return view('marriage-blessing-requests.pending', compact('requests'));
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, MarriageBlessingRequest $marriageBlessingRequest)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($marriageBlessingRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'nullable|string|max:1000',
            'scheduled_blessing_date' => 'nullable|date|after:today',
        ]);

        $marriageBlessingRequest->update([
            'status' => $validated['scheduled_blessing_date'] ? 'scheduled' : 'approved',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'] ?? null,
            'scheduled_blessing_date' => $validated['scheduled_blessing_date'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request approved successfully' . 
            ($marriageBlessingRequest->scheduled_blessing_date ? ' and scheduled for ' . $marriageBlessingRequest->scheduled_blessing_date->format('F d, Y') : '') . '.');
    }

    /**
     * Reject a request
     */
    public function reject(Request $request, MarriageBlessingRequest $marriageBlessingRequest)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($marriageBlessingRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|max:1000',
        ]);

        $marriageBlessingRequest->update([
            'status' => 'rejected',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request rejected.');
    }

    /**
     * Mark as requiring counseling
     */
    public function requireCounseling(Request $request, MarriageBlessingRequest $marriageBlessingRequest)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|max:1000',
        ]);

        $marriageBlessingRequest->update([
            'status' => 'counseling_required',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request marked as requiring counseling.');
    }
}
