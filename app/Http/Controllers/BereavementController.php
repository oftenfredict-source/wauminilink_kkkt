<?php

namespace App\Http\Controllers;

use App\Models\BereavementEvent;
use App\Models\BereavementContribution;
use App\Models\Member;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BereavementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of bereavement events
     */
    public function index(Request $request)
    {
        $query = BereavementEvent::with(['contributions.member', 'creator']);

        // Search
        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function ($q) use ($s) {
                $q->where('deceased_name', 'like', "%{$s}%")
                    ->orWhere('family_details', 'like', "%{$s}%")
                    ->orWhere('related_departments', 'like', "%{$s}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('incident_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('incident_date', '<=', $request->date('to'));
        }

        $events = $query->orderBy('incident_date', 'desc')->paginate(15);
        $events->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json($events);
        }

        $totalMembers = Member::count();
        return view('bereavement.index', compact('events', 'totalMembers'));
    }

    /**
     * Show the form for creating a new bereavement event
     */
    public function create()
    {
        $members = Member::orderBy('full_name')->get();
        return view('bereavement.create', compact('members'));
    }

    /**
     * Store a newly created bereavement event
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'deceased_name' => 'required|string|max:255',
                'family_details' => 'nullable|string',
                'related_departments' => 'nullable|string',
                'incident_date' => 'required|date',
                'contribution_start_date' => 'required|date',
                'contribution_end_date' => 'required|date|after:contribution_start_date',
                'notes' => 'nullable|string',
                'member_ids' => 'nullable|array',
                'member_ids.*' => 'exists:members,id',
                'send_notifications' => 'boolean',
            ]);

            $validated['created_by'] = auth()->id();
            $validated['status'] = 'open';

            $event = BereavementEvent::create($validated);

            // Create contribution records for all members or selected members
            $memberIds = $request->input('member_ids', []);
            if (empty($memberIds)) {
                // If no members selected, create records for all members
                $members = Member::all();
            } else {
                $members = Member::whereIn('id', $memberIds)->get();
            }

            foreach ($members as $member) {
                try {
                    BereavementContribution::create([
                        'bereavement_event_id' => $event->id,
                        'member_id' => $member->id,
                        'has_contributed' => false,
                        'contribution_type' => 'individual',
                        'amount' => 0, // Set default amount for existing table structure
                        'contribution_date' => now()->toDateString(), // Required by existing table
                        'payment_method' => 'cash', // Required by existing table
                        'recorded_by' => auth()->id(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create contribution record for member: ' . $member->id, [
                        'error' => $e->getMessage()
                    ]);
                    // Continue with next member
                }
            }

            // Send notifications if requested
            if ($request->boolean('send_notifications')) {
                try {
                    $this->notificationService->sendBereavementNotifications(
                        $event,
                        $memberIds,
                        'created'
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send bereavement notifications: ' . $e->getMessage());
                    // Don't fail the entire request if notifications fail
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bereavement event created successfully',
                'event' => $event->load(['contributions.member', 'creator']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating bereavement event: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified bereavement event
     */
    public function show(BereavementEvent $bereavement)
    {
        $bereavement->load([
            'contributions.member',
            'contributions.recorder',
            'creator'
        ]);

        // Calculate statistics
        $totalContributions = $bereavement->total_contributions;
        $contributorsCount = $bereavement->contributors_count;
        $nonContributorsCount = $bereavement->non_contributors_count;
        $daysRemaining = $bereavement->days_remaining;

        // Get contributors and non-contributors
        $contributors = $bereavement->contributions()
            ->where('has_contributed', true)
            ->with('member')
            ->orderBy('contribution_date', 'desc')
            ->get();

        $nonContributors = $bereavement->contributions()
            ->where('has_contributed', false)
            ->with('member')
            ->orderBy('member_id')
            ->get();

        // Get all members for the dropdown (those who haven't contributed yet)
        // This includes members with contribution records (has_contributed = false) 
        // and members who don't have a contribution record yet
        $contributedMemberIds = $bereavement->contributions()
            ->where('has_contributed', true)
            ->pluck('member_id')
            ->filter()
            ->toArray();

        $availableMembers = Member::whereNotIn('id', $contributedMemberIds)
            ->orderBy('full_name')
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'event' => $bereavement,
                'statistics' => [
                    'total_contributions' => $totalContributions,
                    'contributors_count' => $contributorsCount,
                    'non_contributors_count' => $nonContributorsCount,
                    'days_remaining' => $daysRemaining,
                ],
                'contributors' => $contributors,
                'non_contributors' => $nonContributors,
            ]);
        }

        return view('bereavement.show', compact(
            'bereavement',
            'totalContributions',
            'contributorsCount',
            'nonContributorsCount',
            'daysRemaining',
            'contributors',
            'nonContributors',
            'availableMembers'
        ));
    }

    /**
     * Update the specified bereavement event
     */
    public function update(Request $request, BereavementEvent $bereavement)
    {
        // Don't allow updates to closed events
        if ($bereavement->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update a closed bereavement event'
            ], 403);
        }

        $validated = $request->validate([
            'deceased_name' => 'required|string|max:255',
            'family_details' => 'nullable|string',
            'related_departments' => 'nullable|string',
            'incident_date' => 'required|date',
            'contribution_start_date' => 'required|date',
            'contribution_end_date' => 'required|date|after:contribution_start_date',
            'notes' => 'nullable|string',
            'fund_usage' => 'nullable|string',
        ]);

        $bereavement->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bereavement event updated successfully',
            'event' => $bereavement->load(['contributions.member', 'creator']),
        ]);
    }

    /**
     * Remove the specified bereavement event
     */
    public function destroy(BereavementEvent $bereavement)
    {
        $bereavement->delete();
        return response()->json([
            'success' => true,
            'message' => 'Bereavement event deleted successfully'
        ]);
    }

    /**
     * Record a contribution for a member
     */
    public function recordContribution(Request $request, BereavementEvent $bereavement)
    {
        if ($bereavement->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot record contributions for a closed event'
            ], 403);
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'contribution_amount' => 'required|numeric|min:0',
            'contribution_date' => 'required|date',
            'contribution_type' => 'required|in:family_wide,individual',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money',
            'reference_number' => 'required_if:payment_method,bank_transfer,mobile_money|nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Map contribution_type from our format to table format
        $contributionType = $validated['contribution_type'] === 'family_wide' ? 'family' : 'individual';

        $contribution = BereavementContribution::updateOrCreate(
            [
                'bereavement_event_id' => $bereavement->id,
                'member_id' => $validated['member_id'],
            ],
            [
                'has_contributed' => true,
                'contribution_amount' => $validated['contribution_amount'],
                'contribution_date' => $validated['contribution_date'],
                'contribution_type' => $contributionType,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Contribution recorded successfully',
            'contribution' => $contribution->load('member'),
        ]);
    }

    /**
     * Mark a member as not contributing
     */
    public function markNonContributor(Request $request, BereavementEvent $bereavement)
    {
        if ($bereavement->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update contributions for a closed event'
            ], 403);
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $contribution = BereavementContribution::updateOrCreate(
            [
                'bereavement_event_id' => $bereavement->id,
                'member_id' => $validated['member_id'],
            ],
            [
                'has_contributed' => false,
                'amount' => 0,
                'contribution_date' => now()->toDateString(), // Required by existing table
                'payment_method' => 'cash', // Required by existing table
                'recorded_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Member marked as non-contributor',
            'contribution' => $contribution->load('member'),
        ]);
    }

    /**
     * Manually close a bereavement event
     */
    public function close(BereavementEvent $bereavement)
    {
        if ($bereavement->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Event is already closed'
            ], 400);
        }

        $bereavement->close();

        return response()->json([
            'success' => true,
            'message' => 'Bereavement event closed successfully',
            'event' => $bereavement,
        ]);
    }

    /**
     * Generate summary report
     */
    public function summaryReport(BereavementEvent $bereavement)
    {
        $bereavement->load([
            'contributions.member',
            'creator'
        ]);

        $totalContributions = $bereavement->total_contributions;
        $contributorsCount = $bereavement->contributors_count;
        $nonContributorsCount = $bereavement->non_contributors_count;

        // Group by department (if departments are tracked)
        $contributionsByType = $bereavement->contributions()
            ->where('has_contributed', true)
            ->selectRaw('contribution_type, SUM(amount) as total')
            ->groupBy('contribution_type')
            ->get();

        return response()->json([
            'event' => $bereavement,
            'summary' => [
                'total_contributions' => $totalContributions,
                'contributors_count' => $contributorsCount,
                'non_contributors_count' => $nonContributorsCount,
                'contributions_by_type' => $contributionsByType,
            ],
        ]);
    }

    /**
     * Export report to PDF or Excel
     */
    public function exportReport(BereavementEvent $bereavement, $format = 'pdf')
    {
        $bereavement->load([
            'contributions.member',
            'creator'
        ]);

        // This would typically use a PDF/Excel library like DomPDF or Maatwebsite/Excel
        // For now, return JSON or redirect to a view
        if ($format === 'pdf') {
            return view('bereavement.reports.pdf', compact('bereavement'));
        } else {
            // Excel export logic would go here
            return response()->json([
                'message' => 'Excel export not yet implemented',
                'data' => $bereavement
            ]);
        }
    }

    /**
     * Get members for notification selection
     */
    public function getMembersForNotification()
    {
        $members = Member::select('id', 'full_name', 'email', 'phone_number')
            ->orderBy('full_name')
            ->get();

        return response()->json($members);
    }
}

