<?php

namespace App\Http\Controllers;

use App\Models\BaptismApplication;
use App\Models\Campus;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BaptismApplicationController extends Controller
{
    /**
     * Display a listing of applications for Evangelism Leader
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

        $applications = BaptismApplication::where('evangelism_leader_id', $user->id)
            ->with(['pastor', 'churchBranch', 'community'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('baptism-applications.index', compact('applications', 'campus'));
    }

    /**
     * Show the form for creating a new application
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

        // Get all campuses for branch selection
        $campuses = Campus::where('is_active', true)->orderBy('name')->get();

        // Get all communities in this campus for selection
        $communities = Community::where('campus_id', $campus->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('baptism-applications.create', compact('campus', 'campuses', 'communities'));
    }

    /**
     * Store a newly created application
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isEvangelismLeader() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            // Personal Information
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'age' => 'nullable|integer|min:0|max:150', // Age is auto-calculated, but we'll validate it if provided
            'phone_number' => 'nullable|string|max:20', // Optional for children, required for adults (handled in custom validation)
            'email' => 'nullable|email|max:255',
            'residential_address' => 'required|string|max:500',
            'church_branch_id' => 'nullable|exists:campuses,id',
            'community_id' => 'required|exists:communities,id',
            
            // Spiritual Information
            'previously_baptized' => 'required|boolean',
            'previous_church_name' => 'nullable|required_if:previously_baptized,1|string|max:255',
            'previous_baptism_date' => 'nullable|required_if:previously_baptized,1|date|before:today',
            'attended_baptism_classes' => 'required|boolean',
            'church_attendance_duration' => 'nullable|string|max:100',
            'pastor_catechist_name' => 'nullable|string|max:255',
            
            // Family Information
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'parent_guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'family_religious_background' => 'nullable|string|max:1000',
            
            // Application Statement
            'reason_for_baptism' => 'required|string|min:20|max:2000',
            'declaration_agreed' => 'required|accepted',
            
            // Attachments
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'recommendation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'declaration_agreed.accepted' => 'You must agree to the declaration statement.',
            'reason_for_baptism.min' => 'Please provide a more detailed reason for requesting baptism (at least 20 characters).',
        ]);

        try {
            // Convert string boolean values to actual booleans
            // Radio buttons send "1" or "0" as strings, convert to boolean
            if (isset($validated['previously_baptized'])) {
                $validated['previously_baptized'] = (bool) $validated['previously_baptized'];
            }
            if (isset($validated['attended_baptism_classes'])) {
                $validated['attended_baptism_classes'] = (bool) $validated['attended_baptism_classes'];
            }
            
            // Handle file uploads
            $photoPath = null;
            $recommendationLetterPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('baptism_applications/photos', 'public');
            }

            if ($request->hasFile('recommendation_letter')) {
                $recommendationLetterPath = $request->file('recommendation_letter')->store('baptism_applications/recommendations', 'public');
            }

            // Get the evangelism leader's campus
            $campus = $user->getCampus();
            if (!$campus) {
                return back()->with('error', 'Campus not found. Please contact administrator.')
                    ->withInput();
            }

            // Automatically set church_branch_id to the evangelism leader's campus
            $validated['church_branch_id'] = $campus->id;

            // Calculate age from date of birth (auto-calculated)
            $dateOfBirth = Carbon::parse($validated['date_of_birth']);
            $age = $dateOfBirth->age; // Carbon's age method calculates age automatically

            // Validate based on age
            if ($age < 18) {
                // Child applicant: parent/guardian required, phone/email optional
                if (empty($validated['parent_guardian_name'])) {
                    return back()->with('error', 'Parent/Guardian name is required for applicants under 18 years old.')
                        ->withInput();
                }
                // Don't require phone_number for children
                // Don't save marital_status for children
                $validated['marital_status'] = null;
            } else {
                // Adult applicant: phone required, marital status allowed
                if (empty($validated['phone_number'])) {
                    return back()->with('error', 'Phone number is required for adult applicants.')
                        ->withInput();
                }
            }

            // Create application
            $application = BaptismApplication::create([
                'full_name' => $validated['full_name'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'age' => $age,
                'phone_number' => $validated['phone_number'] ?? null, // Nullable for children
                'email' => $validated['email'] ?? null,
                'residential_address' => $validated['residential_address'],
                'church_branch_id' => $campus->id, // Always use the evangelism leader's campus
                'community_id' => $validated['community_id'] ?? null,
                'previously_baptized' => $validated['previously_baptized'],
                'previous_church_name' => $validated['previous_church_name'] ?? null,
                'previous_baptism_date' => $validated['previous_baptism_date'] ?? null,
                'attended_baptism_classes' => $validated['attended_baptism_classes'],
                'church_attendance_duration' => $validated['church_attendance_duration'] ?? null,
                'pastor_catechist_name' => $validated['pastor_catechist_name'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'parent_guardian_name' => $validated['parent_guardian_name'] ?? null,
                'guardian_phone' => $validated['guardian_phone'] ?? null,
                'guardian_email' => $validated['guardian_email'] ?? null,
                'family_religious_background' => $validated['family_religious_background'] ?? null,
                'reason_for_baptism' => $validated['reason_for_baptism'],
                'declaration_agreed' => true,
                'photo_path' => $photoPath,
                'recommendation_letter_path' => $recommendationLetterPath,
                'evangelism_leader_id' => $user->id,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            Log::info('Baptism application created', [
                'application_id' => $application->id,
                'applicant_name' => $application->full_name,
                'evangelism_leader_id' => $user->id
            ]);

            return redirect()->route('evangelism-leader.baptism-applications.index')
                ->with('success', 'Baptism application submitted successfully. It has been sent to the Pastor for review.');
        } catch (\Exception $e) {
            Log::error('Error creating baptism application', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return back()->with('error', 'Failed to submit application: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified application
     */
    public function show(BaptismApplication $baptismApplication)
    {
        $user = auth()->user();
        
        // Check authorization
        if ($user->isEvangelismLeader()) {
            if ($baptismApplication->evangelism_leader_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->isPastor() || $user->isAdmin()) {
            // Pastors and admins can view all applications
        } else {
            abort(403, 'Unauthorized access.');
        }

        $baptismApplication->load(['evangelismLeader', 'pastor', 'churchBranch', 'community']);

        return view('baptism-applications.show', compact('baptismApplication'));
    }

    /**
     * Show applications pending pastor review
     */
    public function pending()
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Only Pastors can review applications.');
        }

        $applications = BaptismApplication::where('status', 'pending')
            ->with(['evangelismLeader', 'churchBranch', 'community'])
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);

        return view('baptism-applications.pending', compact('applications'));
    }

    /**
     * Approve an application
     */
    public function approve(Request $request, BaptismApplication $baptismApplication)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($baptismApplication->status !== 'pending') {
            return back()->with('error', 'This application has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'nullable|string|max:1000',
            'scheduled_baptism_date' => 'nullable|date|after:today',
        ]);

        $baptismApplication->update([
            'status' => $validated['scheduled_baptism_date'] ? 'scheduled' : 'approved',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'] ?? null,
            'scheduled_baptism_date' => $validated['scheduled_baptism_date'] ?? null,
            'reviewed_at' => now(),
        ]);

        Log::info('Baptism application approved', [
            'application_id' => $baptismApplication->id,
            'pastor_id' => $user->id,
            'scheduled_date' => $baptismApplication->scheduled_baptism_date
        ]);

        return back()->with('success', 'Application approved successfully' . 
            ($baptismApplication->scheduled_baptism_date ? ' and scheduled for ' . $baptismApplication->scheduled_baptism_date->format('F d, Y') : '') . '.');
    }

    /**
     * Reject an application
     */
    public function reject(Request $request, BaptismApplication $baptismApplication)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($baptismApplication->status !== 'pending') {
            return back()->with('error', 'This application has already been reviewed.');
        }

        $validated = $request->validate([
            'pastor_comments' => 'required|string|min:10|max:1000',
        ], [
            'pastor_comments.required' => 'Please provide a reason for rejection.',
            'pastor_comments.min' => 'Please provide a more detailed reason (at least 10 characters).',
        ]);

        $baptismApplication->update([
            'status' => 'rejected',
            'pastor_id' => $user->id,
            'pastor_comments' => $validated['pastor_comments'],
            'reviewed_at' => now(),
        ]);

        Log::info('Baptism application rejected', [
            'application_id' => $baptismApplication->id,
            'pastor_id' => $user->id
        ]);

        return back()->with('success', 'Application rejected. Evangelism Leader has been notified.');
    }

    /**
     * Schedule baptism date for approved application
     */
    public function schedule(Request $request, BaptismApplication $baptismApplication)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array($baptismApplication->status, ['approved', 'scheduled'])) {
            return back()->with('error', 'Only approved applications can be scheduled.');
        }

        $validated = $request->validate([
            'scheduled_baptism_date' => 'required|date|after:today',
        ]);

        $baptismApplication->update([
            'status' => 'scheduled',
            'scheduled_baptism_date' => $validated['scheduled_baptism_date'],
        ]);

        Log::info('Baptism scheduled', [
            'application_id' => $baptismApplication->id,
            'scheduled_date' => $baptismApplication->scheduled_baptism_date
        ]);

        return back()->with('success', 'Baptism scheduled for ' . $baptismApplication->scheduled_baptism_date->format('F d, Y') . '.');
    }

    /**
     * Mark baptism as completed
     */
    public function complete(BaptismApplication $baptismApplication)
    {
        $user = auth()->user();
        
        if (!$user->isPastor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($baptismApplication->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled baptisms can be marked as completed.');
        }

        $baptismApplication->update([
            'status' => 'completed',
        ]);

        Log::info('Baptism marked as completed', [
            'application_id' => $baptismApplication->id
        ]);

        return back()->with('success', 'Baptism marked as completed.');
    }
}
