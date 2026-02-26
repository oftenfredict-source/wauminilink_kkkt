<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Member;
use App\Models\Campus;
use App\Models\Community;
use App\Services\SmsService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = \Carbon\Carbon::now()->toDateString();

        $announcements = Announcement::where(function ($query) use ($today) {
            // Show announcements that don't have an end_date or end_date is in the future
            $query->whereNull('end_date')
                ->orWhere('end_date', '>', $today);
        })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $campuses = Campus::active()->get();
        $communities = Community::active()->get();
        // Fetch members with phone numbers for individual selection
        $members = Member::whereNotNull('phone_number')->where('phone_number', '!=', '')->orderBy('full_name')->get();

        return view('announcements.index', compact('announcements', 'campuses', 'communities', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campuses = Campus::active()->get();
        $communities = Community::active()->get();
        // Fetch members with phone numbers for individual selection
        $members = Member::whereNotNull('phone_number')->where('phone_number', '!=', '')->orderBy('full_name')->get();

        return view('announcements.create', compact('campuses', 'communities', 'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:general,urgent,event,reminder',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'sms_campus_id' => 'nullable|exists:campuses,id',
                'sms_community_id' => 'nullable|exists:communities,id',
                'sms_gender' => 'nullable|in:male,female',
                'sms_age_group' => 'nullable|in:adult,child',
                'sms_residence' => 'nullable|in:main_area,outside',
                'sms_member_ids' => 'nullable|array',
                'sms_member_ids.*' => 'exists:members,id'
            ];

            // Only validate end_date after start_date if start_date is provided
            if ($request->filled('start_date')) {
                $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
            }

            $validated = $request->validate($rules);

            $validated['created_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active') ? true : false;
            $validated['is_pinned'] = $request->has('is_pinned') ? true : false;

            // Handle empty dates
            if (empty($validated['start_date'])) {
                $validated['start_date'] = null;
            }
            if (empty($validated['end_date'])) {
                $validated['end_date'] = null;
            }

            $announcement = Announcement::create($validated);

            \Log::info('Announcement created successfully', [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'created_by' => Auth::id(),
            ]);

            // Send SMS to members if requested
            if ($request->has('send_sms') && $request->boolean('send_sms')) {
                $this->sendAnnouncementSms($announcement, $request->only([
                    'sms_campus_id',
                    'sms_community_id',
                    'sms_gender',
                    'sms_age_group',
                    'sms_residence',
                    'sms_member_ids'
                ]));
            }

            $successMessage = 'Announcement created successfully!';
            if ($request->has('send_sms') && $request->boolean('send_sms')) {
                $successMessage .= ' SMS notifications sent to members.';
            }

            return redirect()->route('announcements.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Failed to create announcement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create announcement: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $campuses = Campus::active()->get();
        $communities = Community::active()->get();
        // Fetch members with phone numbers for individual selection
        $members = Member::whereNotNull('phone_number')->where('phone_number', '!=', '')->orderBy('full_name')->get();

        return view('announcements.edit', compact('announcement', 'campuses', 'communities', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        try {
            $rules = [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:general,urgent,event,reminder',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'sms_campus_id' => 'nullable|exists:campuses,id',
                'sms_community_id' => 'nullable|exists:communities,id',
                'sms_gender' => 'nullable|in:male,female',
                'sms_age_group' => 'nullable|in:adult,child',
                'sms_residence' => 'nullable|in:main_area,outside',
                'sms_member_ids' => 'nullable|array',
                'sms_member_ids.*' => 'exists:members,id'
            ];

            // Only validate end_date after start_date if start_date is provided
            if ($request->filled('start_date')) {
                $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
            }

            $validated = $request->validate($rules);

            $validated['is_active'] = $request->has('is_active') ? true : false;
            $validated['is_pinned'] = $request->has('is_pinned') ? true : false;

            // Handle empty dates
            if (empty($validated['start_date'])) {
                $validated['start_date'] = null;
            }
            if (empty($validated['end_date'])) {
                $validated['end_date'] = null;
            }

            $announcement->update($validated);

            \Log::info('Announcement updated successfully', [
                'id' => $announcement->id,
                'title' => $announcement->title,
            ]);

            // Send SMS to members if requested
            if ($request->has('send_sms') && $request->boolean('send_sms')) {
                $this->sendAnnouncementSms($announcement, $request->only([
                    'sms_campus_id',
                    'sms_community_id',
                    'sms_gender',
                    'sms_age_group',
                    'sms_residence',
                    'sms_member_ids'
                ]));
            }

            $successMessage = 'Announcement updated successfully!';
            if ($request->has('send_sms') && $request->boolean('send_sms')) {
                $successMessage .= ' SMS notifications sent to members.';
            }

            return redirect()->route('announcements.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Failed to update announcement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update announcement: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Send SMS notification for an existing announcement
     */
    public function sendSms(Request $request, Announcement $announcement)
    {
        try {
            $this->sendAnnouncementSms($announcement, $request->only([
                'sms_campus_id',
                'sms_community_id',
                'sms_gender',
                'sms_age_group',
                'sms_residence',
                'sms_member_ids'
            ]));

            return redirect()->route('announcements.index')
                ->with('success', 'SMS notifications sent to selected members successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to send announcement SMS', [
                'announcement_id' => $announcement->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('announcements.index')
                ->with('error', 'Failed to send SMS notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS notification to all members about the announcement
     */
    private function sendAnnouncementSms(Announcement $announcement, array $filters = [])
    {
        try {
            // Check if SMS notifications are enabled
            if (!SettingsService::get('enable_sms_notifications', false)) {
                Log::info('SMS notifications disabled, skipping announcement SMS');
                return false;
            }

            // Get church name from settings
            $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');

            // Build SMS message
            $message = $this->buildAnnouncementMessage($announcement, $churchName);

            // Get filtered members with phone numbers
            $query = Member::whereNotNull('phone_number')
                ->where('phone_number', '!=', '');

            // Prioritize specific members if selected
            if (!empty($filters['sms_member_ids'])) {
                $query->whereIn('id', $filters['sms_member_ids']);
            } else {
                // Apply general filters only if specific members are not selected
                if (!empty($filters['sms_campus_id'])) {
                    $query->where('campus_id', $filters['sms_campus_id']);
                }
                if (!empty($filters['sms_community_id'])) {
                    $query->where('community_id', $filters['sms_community_id']);
                }
                if (!empty($filters['sms_gender'])) {
                    $query->where('gender', $filters['sms_gender']);
                }
                if (!empty($filters['sms_age_group'])) {
                    $today = \Carbon\Carbon::today();
                    $adultDate = $today->copy()->subYears(18);

                    if ($filters['sms_age_group'] === 'adult') {
                        $query->where('date_of_birth', '<=', $adultDate);
                    } else {
                        $query->where('date_of_birth', '>', $adultDate);
                    }
                }
                if (!empty($filters['sms_residence'])) {
                    if ($filters['sms_residence'] === 'main_area') {
                        $query->where('lives_outside_main_area', false);
                    } else {
                        $query->where('lives_outside_main_area', true);
                    }
                }
            }

            $members = $query->get();

            $smsService = app(SmsService::class);
            $successCount = 0;
            $failCount = 0;

            foreach ($members as $member) {
                try {
                    if ($smsService->send($member->phone_number, $message)) {
                        $successCount++;
                    } else {
                        $failCount++;
                        Log::warning('Failed to send announcement SMS to member', [
                            'member_id' => $member->id,
                            'phone' => $member->phone_number,
                        ]);
                    }
                } catch (\Exception $e) {
                    $failCount++;
                    Log::error('Exception sending announcement SMS to member', [
                        'member_id' => $member->id,
                        'phone' => $member->phone_number,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Announcement SMS sending completed', [
                'announcement_id' => $announcement->id,
                'total_members' => $members->count(),
                'success_count' => $successCount,
                'fail_count' => $failCount,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send announcement SMS', [
                'announcement_id' => $announcement->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build SMS message for announcement
     */
    private function buildAnnouncementMessage(Announcement $announcement, string $churchName): string
    {
        $typeLabels = [
            'urgent' => 'TAARIFA MUHIMU',
            'event' => 'TUKIO',
            'reminder' => 'UKUMBUSHO',
            'general' => 'TAARIFA',
        ];

        $typeLabel = $typeLabels[$announcement->type] ?? 'TAARIFA';

        // Truncate content to fit SMS (SMS typically 160 characters, but we'll use 120 to be safe)
        $content = mb_substr($announcement->content, 0, 100);
        if (mb_strlen($announcement->content) > 100) {
            $content .= '...';
        }

        // Build message without extra spaces
        $date = $announcement->created_at->format('d/m/Y');
        $message = "{$typeLabel} ({$date}) - {$churchName}\n";
        $message .= "{$announcement->title}\n";
        $message .= "{$content}";

        return $message;
    }
}
