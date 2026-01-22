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
            'groom_date_of_birth' => 'required|date|before:today',
            'groom_phone_number' => 'required|string|max:20',
            'groom_email' => 'nullable|email|max:255',
            'bride_full_name' => 'required|string|max:255',
            'bride_date_of_birth' => 'required|date|before:today',
            'bride_phone_number' => 'required|string|max:20',
            'bride_email' => 'nullable|email|max:255',
            'both_baptized' => 'required|boolean',
            'both_confirmed' => 'required|boolean',
            'membership_duration' => 'nullable|string|max:100',
            'pastor_catechist_name' => 'nullable|string|max:255',
            'preferred_wedding_date' => 'required|date|after:today',
            'preferred_church' => 'nullable|string|max:255',
            'expected_guests' => 'nullable|integer|min:1',
            'attended_premarital_counseling' => 'required|boolean',
            'groom_baptism_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'bride_baptism_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'groom_confirmation_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'bride_confirmation_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'groom_birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'bride_birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'marriage_notice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'declaration_agreed' => 'required|accepted',
        ], [
            'declaration_agreed.accepted' => 'You must agree to the declaration statement.',
        ]);

        try {
            // Get the evangelism leader's campus
            $campus = $user->getCampus();
            if (!$campus) {
                return back()->with('error', 'Campus not found. Please contact administrator.')
                    ->withInput();
            }

            // Handle file uploads
            $groomBaptismPath = null;
            $brideBaptismPath = null;
            $groomConfirmationPath = null;
            $brideConfirmationPath = null;
            $groomBirthPath = null;
            $brideBirthPath = null;
            $marriageNoticePath = null;

            if ($request->hasFile('groom_baptism_certificate')) {
                $groomBaptismPath = $request->file('groom_baptism_certificate')->store('wedding_requests/documents', 'public');
            }
            if ($request->hasFile('bride_baptism_certificate')) {
                $brideBaptismPath = $request->file('bride_baptism_certificate')->store('wedding_requests/documents', 'public');
            }
            if ($request->hasFile('groom_confirmation_certificate')) {
                $groomConfirmationPath = $request->file('groom_confirmation_certificate')->store('wedding_requests/documents', 'public');
            }
            if ($request->hasFile('bride_confirmation_certificate')) {
                $brideConfirmationPath = $request->file('bride_confirmation_certificate')->store('wedding_requests/documents', 'public');
            }
            if ($request->hasFile('groom_birth_certificate')) {
                $groomBirthPath = $request->file('groom_birth_certificate')->store('wedding_requests/documents', 'public');
            }
            if ($request->hasFile('bride_birth_certificate')) {
                $brideBirthPath = $request->file('bride_birth_certificate')->store('wedding_requests/documents', 'public');
            }
            if ($request->hasFile('marriage_notice')) {
                $marriageNoticePath = $request->file('marriage_notice')->store('wedding_requests/documents', 'public');
            }

            // Convert booleans
            $validated['both_baptized'] = (bool) $validated['both_baptized'];
            $validated['both_confirmed'] = (bool) $validated['both_confirmed'];
            $validated['attended_premarital_counseling'] = (bool) $validated['attended_premarital_counseling'];

            // Create request
            $weddingRequest = ChurchWeddingRequest::create([
                'groom_full_name' => $validated['groom_full_name'],
                'groom_date_of_birth' => $validated['groom_date_of_birth'],
                'groom_phone_number' => $validated['groom_phone_number'],
                'groom_email' => $validated['groom_email'] ?? null,
                'bride_full_name' => $validated['bride_full_name'],
                'bride_date_of_birth' => $validated['bride_date_of_birth'],
                'bride_phone_number' => $validated['bride_phone_number'],
                'bride_email' => $validated['bride_email'] ?? null,
                'church_branch_id' => $campus->id,
                'both_baptized' => $validated['both_baptized'],
                'both_confirmed' => $validated['both_confirmed'],
                'membership_duration' => $validated['membership_duration'] ?? null,
                'pastor_catechist_name' => $validated['pastor_catechist_name'] ?? null,
                'preferred_wedding_date' => $validated['preferred_wedding_date'],
                'preferred_church' => $validated['preferred_church'] ?? null,
                'expected_guests' => $validated['expected_guests'] ?? null,
                'attended_premarital_counseling' => $validated['attended_premarital_counseling'],
                'groom_baptism_certificate_path' => $groomBaptismPath,
                'bride_baptism_certificate_path' => $brideBaptismPath,
                'groom_confirmation_certificate_path' => $groomConfirmationPath,
                'bride_confirmation_certificate_path' => $brideConfirmationPath,
                'groom_birth_certificate_path' => $groomBirthPath,
                'bride_birth_certificate_path' => $brideBirthPath,
                'marriage_notice_path' => $marriageNoticePath,
                'declaration_agreed' => true,
                'evangelism_leader_id' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            Log::info('Church wedding request created', [
                'request_id' => $weddingRequest->id,
                'groom' => $weddingRequest->groom_full_name,
                'bride' => $weddingRequest->bride_full_name,
                'evangelism_leader_id' => $user->id
            ]);

            return redirect()->route('evangelism-leader.church-wedding-requests.index')
                ->with('success', 'Church wedding request submitted successfully. It has been sent to the Pastor for review.');
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

        $requests = ChurchWeddingRequest::where('status', 'pending')
            ->with(['evangelismLeader', 'churchBranch'])
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
            ($churchWeddingRequest->confirmed_wedding_date ? ' and scheduled for ' . $churchWeddingRequest->confirmed_wedding_date->format('F d, Y') : '') . '.');
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
}
