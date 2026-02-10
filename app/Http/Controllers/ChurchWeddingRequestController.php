<?php

namespace App\Http\Controllers;

use App\Models\ChurchWeddingRequest;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChurchWeddingRequestController extends Controller
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

        $requests = ChurchWeddingRequest::where('evangelism_leader_id', $user->id)
            ->with(['pastor', 'churchBranch'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('church-wedding-requests.index', compact('requests', 'campus'));
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

        return view('church-wedding-requests.create', compact('campus'));
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
            'groom_full_name' => 'required|string|max:255',
            'groom_phone_number' => 'required|string|max:20',
            'bride_full_name' => 'required|string|max:255',
            'bride_phone_number' => 'required|string|max:20',
            'declaration_agreed' => 'required|accepted',
        ], [
            'declaration_agreed.accepted' => 'You must agree to the declaration statement.',
            'groom_phone_number.required' => 'Groom phone number is required for referral.',
            'bride_phone_number.required' => 'Bride phone number is required for referral.',
        ]);

        try {
            // Get the evangelism leader's campus
            $campus = $user->getCampus();
            if (!$campus) {
                return back()->with('error', 'Campus not found. Please contact administrator.')
                    ->withInput();
            }

            // Create request with minimal referral data
            $weddingRequest = ChurchWeddingRequest::create([
                'groom_full_name' => $validated['groom_full_name'],
                'groom_phone_number' => $validated['groom_phone_number'],
                'bride_full_name' => $validated['bride_full_name'],
                'bride_phone_number' => $validated['bride_phone_number'],
                'church_branch_id' => $campus->id,
                'declaration_agreed' => true,
                'evangelism_leader_id' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(),
                // The following fields will be filled by the Pastor later
                'groom_date_of_birth' => null,
                'groom_email' => null,
                'bride_date_of_birth' => null,
                'bride_email' => null,
                'both_baptized' => false,
                'both_confirmed' => false,
                'membership_duration' => null,
                'pastor_catechist_name' => null,
                'preferred_wedding_date' => null,
                'preferred_church' => null,
                'expected_guests' => null,
                'attended_premarital_counseling' => false,
                'groom_baptism_certificate_path' => null,
                'bride_baptism_certificate_path' => null,
                'groom_confirmation_certificate_path' => null,
                'bride_confirmation_certificate_path' => null,
                'groom_birth_certificate_path' => null,
                'bride_birth_certificate_path' => null,
                'marriage_notice_path' => null,
            ]);

            Log::info('Church wedding request created', [
                'request_id' => $weddingRequest->id,
                'groom' => $weddingRequest->groom_full_name,
                'bride' => $weddingRequest->bride_full_name,
                'evangelism_leader_id' => $user->id
            ]);

            return redirect()->route('evangelism-leader.church-wedding-requests.index')
                ->with('success', 'Church wedding referral submitted successfully. It has been sent to the Pastor for review.');
        } catch (\Exception $e) {
            Log::error('Error creating church wedding request', [
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
    public function show(ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if ($user->isEvangelismLeader()) {
            if ($churchWeddingRequest->evangelism_leader_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->isPastor() || $user->isAdmin()) {
            // Pastors and admins can view all requests
        } else {
            abort(403, 'Unauthorized access.');
        }

        $churchWeddingRequest->load(['evangelismLeader', 'pastor', 'churchBranch']);

        return view('church-wedding-requests.show', compact('churchWeddingRequest'));
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

        $requests = ChurchWeddingRequest::where(function ($query) {
            $query->whereIn('status', ['pending', 'documents_required'])
                ->orWhere(function ($q) {
                    $q->whereIn('status', ['approved', 'scheduled'])
                        ->where(function ($dateQuery) {
                            $dateQuery->whereNull('confirmed_wedding_date')
                                ->orWhere('confirmed_wedding_date', '>=', now()->startOfDay());
                        });
                });
        })
            ->with(['evangelismLeader', 'churchBranch'])
            ->orderByRaw("CASE 
            WHEN status = 'pending' THEN 1 
            WHEN status = 'documents_required' THEN 2 
            WHEN status = 'scheduled' THEN 3 
            WHEN status = 'approved' THEN 4 
            ELSE 5 END")
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);

        return view('church-wedding-requests.pending', compact('requests'));
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($churchWeddingRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'nullable|string|max:1000',
            'confirmed_wedding_date' => 'nullable|date|after:today',
        ]);

        $churchWeddingRequest->update([
            'status' => $validated['confirmed_wedding_date'] ? 'scheduled' : 'approved',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'] ?? null,
            'wedding_approval_date' => now(),
            'confirmed_wedding_date' => $validated['confirmed_wedding_date'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request approved successfully' .
            ($churchWeddingRequest->confirmed_wedding_date ? ' and scheduled for ' . Carbon::parse($churchWeddingRequest->confirmed_wedding_date)->format('F d, Y') : '') . '.');
    }

    /**
     * Reject a request
     */
    public function reject(Request $request, ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($churchWeddingRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|max:1000',
        ]);

        $churchWeddingRequest->update([
            'status' => 'rejected',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request rejected.');
    }

    /**
     * Mark as requiring documents
     */
    public function requireDocuments(Request $request, ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|max:1000',
        ]);

        $churchWeddingRequest->update([
            'status' => 'documents_required',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Request marked as requiring documents.');
    }

    /**
     * Schedule a meeting with the couple
     */
    public function scheduleMeeting(Request $request, ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'scheduled_meeting_date' => 'required|date|after:now',
            'pastor_comments' => 'nullable|string|max:1000',
        ]);

        $churchWeddingRequest->update([
            'status' => 'scheduled',
            'scheduled_meeting_date' => $validated['scheduled_meeting_date'],
            'pastor_comments' => $validated['pastor_comments'] ?? $churchWeddingRequest->pastor_comments,
            'pastor_id' => $user->id,
        ]);

        return back()->with('success', 'Meeting scheduled for ' . Carbon::parse($validated['scheduled_meeting_date'])->format('M d, Y h:i A') . '.');
    }

    /**
     * Show edit form for pastor to complete details
     */
    public function edit(ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return view('church-wedding-requests.edit', compact('churchWeddingRequest'));
    }

    /**
     * Update request details (Pastor completing the form)
     */
    public function update(Request $request, ChurchWeddingRequest $churchWeddingRequest)
    {
        $user = auth()->user();

        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'groom_date_of_birth' => 'required|date|before:today',
            'bride_date_of_birth' => 'required|date|before:today',
            'both_baptized' => 'required|boolean',
            'both_confirmed' => 'required|boolean',
            'membership_duration' => 'nullable|string|max:100',
            'pastor_catechist_name' => 'nullable|string|max:255',
            'preferred_wedding_date' => 'required|date|after:today',
            'preferred_church' => 'nullable|string|max:255',
            'expected_guests' => 'nullable|integer|min:1',
            'attended_premarital_counseling' => 'required|boolean',
            'pastor_comments' => 'nullable|string|max:1000',
        ]);

        $churchWeddingRequest->update($validated);

        return redirect()->route('pastor.church-wedding-requests.show', $churchWeddingRequest->id)
            ->with('success', 'Request details updated successfully.');
    }
}
