<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Member;
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
        
        $announcements = Announcement::where(function($query) use ($today) {
                // Show announcements that don't have an end_date or end_date is in the future
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>', $today);
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('announcements.create');
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
                $this->sendAnnouncementSms($announcement);
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
        return view('announcements.edit', compact('announcement'));
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
                $this->sendAnnouncementSms($announcement);
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
    public function sendSms(Announcement $announcement)
    {
        try {
            $this->sendAnnouncementSms($announcement);
            
            return redirect()->route('announcements.index')
                ->with('success', 'SMS notifications sent to all members successfully!');
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
    private function sendAnnouncementSms(Announcement $announcement)
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

            // Get all members with phone numbers
            $members = Member::whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->get();

            $smsService = app(SmsService::class);
            $successCount = 0;
            $failCount = 0;

            foreach ($members as $member) {
                try {
                    $result = $smsService->sendDebug($member->phone_number, $message);
                    
                    if ($result['ok'] ?? false) {
                        $successCount++;
                    } else {
                        $failCount++;
                        Log::warning('Failed to send announcement SMS to member', [
                            'member_id' => $member->id,
                            'phone' => $member->phone_number,
                            'reason' => $result['reason'] ?? 'unknown',
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

        // Build message without extra spaces and without date
        $message = "{$typeLabel} - {$churchName}\n";
        $message .= "{$announcement->title}\n";
        $message .= "{$content}";

        return $message;
    }
}
