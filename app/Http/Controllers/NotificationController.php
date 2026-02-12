<?php

namespace App\Http\Controllers;

use App\Models\SpecialEvent;
use App\Models\Celebration;
use App\Models\SundayService;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\PledgePayment;
use App\Models\FundingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function getNotificationData()
    {
        try {
            $now = Carbon::now();
            $startDate = $now->toDateString(); // start from today
            $next30Days = $now->copy()->addDays(30);

            \Log::info('NotificationController: Fetching data for date range', [
                'startDate' => $startDate,
                'endDate' => $next30Days->toDateString()
            ]);

            // Get upcoming events (next 30 days)
            $events = SpecialEvent::whereDate('event_date', '>=', $startDate)
                ->whereDate('event_date', '<=', $next30Days->toDateString())
                ->orderBy('event_date')
                ->get()
                ->map(function ($event) use ($now) {
                    $eventDate = Carbon::parse($event->event_date)->startOfDay();
                    $eventTime = ($event->start_time && trim($event->start_time) !== '') ? $event->start_time : '23:59:59';
                    $eventDateTime = $eventDate->copy()->setTimeFromTimeString($eventTime);

                    // Calculate days remaining (only for future dates)
                    $daysRemaining = max(0, (int) $now->startOfDay()->diffInDays($eventDate, false));

                    // Only calculate hours if the event is today
                    $hoursRemaining = null;
                    if ($daysRemaining === 0) {
                        $hoursRemaining = max(0, (int) $now->diffInHours($eventDateTime, false));
                    }

                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'date' => $event->event_date,
                        'time' => $event->start_time,
                        'venue' => $event->venue,
                        'speaker' => $event->speaker,
                        'days_remaining' => $daysRemaining,
                        'hours_remaining' => $hoursRemaining,
                        'type' => 'event'
                    ];
                });

            // Get upcoming celebrations (next 30 days)
            $celebrations = Celebration::whereDate('celebration_date', '>=', $startDate)
                ->whereDate('celebration_date', '<=', $next30Days->toDateString())
                ->orderBy('celebration_date')
                ->get()
                ->map(function ($celebration) use ($now) {
                    $celebrationDate = Carbon::parse($celebration->celebration_date)->startOfDay();
                    $celebrationTime = ($celebration->start_time && trim($celebration->start_time) !== '') ? $celebration->start_time : '23:59:59';
                    $celebrationDateTime = $celebrationDate->copy()->setTimeFromTimeString($celebrationTime);

                    // Calculate days remaining (only for future dates)
                    $daysRemaining = max(0, (int) $now->startOfDay()->diffInDays($celebrationDate, false));

                    // Only calculate hours if the celebration is today
                    $hoursRemaining = null;
                    if ($daysRemaining === 0) {
                        $hoursRemaining = max(0, (int) $now->diffInHours($celebrationDateTime, false));
                    }

                    return [
                        'id' => $celebration->id,
                        'title' => $celebration->title,
                        'date' => $celebration->celebration_date,
                        'time' => $celebration->start_time,
                        'venue' => $celebration->venue,
                        'celebrant' => $celebration->celebrant_name,
                        'celebration_type' => $celebration->type,
                        'days_remaining' => $daysRemaining,
                        'hours_remaining' => $hoursRemaining,
                        'type' => 'celebration'
                    ];
                });

            // Get upcoming Sunday services (next 30 days)
            $services = SundayService::whereDate('service_date', '>=', $startDate)
                ->whereDate('service_date', '<=', $next30Days->toDateString())
                ->orderBy('service_date')
                ->get()
                ->map(function ($service) use ($now) {
                    $serviceDate = Carbon::parse($service->service_date)->startOfDay();
                    $serviceTime = ($service->start_time && trim($service->start_time) !== '') ? $service->start_time : '23:59:59';
                    $serviceDateTime = $serviceDate->copy()->setTimeFromTimeString($serviceTime);

                    // Calculate days remaining (only for future dates)
                    $daysRemaining = max(0, (int) $now->startOfDay()->diffInDays($serviceDate, false));

                    // Only calculate hours if the service is today
                    $hoursRemaining = null;
                    if ($daysRemaining === 0) {
                        $hoursRemaining = max(0, (int) $now->diffInHours($serviceDateTime, false));
                    }

                    return [
                        'id' => $service->id,
                        'title' => 'Service',
                        'date' => $service->service_date,
                        'time' => $service->start_time,
                        'venue' => $service->venue,
                        // Use correct field name from model; keep 'speaker' for compatibility
                        'preacher' => $service->preacher,
                        'speaker' => $service->preacher,
                        'theme' => $service->theme,
                        'days_remaining' => $daysRemaining,
                        'hours_remaining' => $hoursRemaining,
                        'type' => 'service'
                    ];
                });

            // Get pending financial approvals (for secretary, pastor, admin)
            $user = auth()->user();
            $pendingApprovals = [];
            $pendingApprovalsCount = 0;

            if ($user && ($user->isSecretary() || $user->isPastor() || $user->isAdmin() || $user->canApproveFinances())) {

                $pendingTithes = Tithe::where('approval_status', 'pending')
                    ->count();

                $pendingOfferings = Offering::where('approval_status', 'pending')
                    ->count();

                $pendingDonations = Donation::where('approval_status', 'pending')
                    ->count();

                $pendingExpenses = Expense::where('approval_status', 'pending')
                    ->count();

                $pendingBudgets = Budget::where('approval_status', 'pending')
                    ->count();

                $pendingPledgePayments = PledgePayment::where('approval_status', 'pending')
                    ->count();

                $pendingFundingRequests = FundingRequest::where('status', 'pending')
                    ->count();

                $pendingApprovalsCount = $pendingTithes + $pendingOfferings + $pendingDonations +
                    $pendingExpenses + $pendingBudgets + $pendingPledgePayments +
                    $pendingFundingRequests;

                $pendingApprovals = [
                    'tithes' => $pendingTithes,
                    'offerings' => $pendingOfferings,
                    'donations' => $pendingDonations,
                    'expenses' => $pendingExpenses,
                    'budgets' => $pendingBudgets,
                    'pledge_payments' => $pendingPledgePayments,
                    'funding_requests' => $pendingFundingRequests,
                    'total' => $pendingApprovalsCount
                ];
            }

            // Get payments needing verification (for treasurer) - expenses approved by pastor but not yet marked as paid
            $paymentsNeedingVerification = [];
            $paymentsNeedingVerificationCount = 0;

            if ($user && ($user->isTreasurer() || $user->isAdmin())) {
                // Get expenses that are approved by pastor but not yet marked as paid
                $approvedExpensesNeedingPayment = Expense::where('approval_status', 'approved')
                    ->where('status', '!=', 'paid')
                    ->with('budget')
                    ->orderBy('expense_date', 'desc')
                    ->get()
                    ->map(function ($expense) {
                        return [
                            'id' => $expense->id,
                            'expense_name' => $expense->expense_name,
                            'amount' => $expense->amount,
                            'expense_date' => $expense->expense_date,
                            'budget_name' => $expense->budget->budget_name ?? 'No Budget',
                            'category' => $expense->expense_category,
                            'type' => 'expense_payment'
                        ];
                    });

                $paymentsNeedingVerificationCount = $approvedExpensesNeedingPayment->count();
                $paymentsNeedingVerification = $approvedExpensesNeedingPayment;
            }

            // Calculate total notifications (including financial approvals and payments needing verification)
            $totalNotifications = $events->count() + $celebrations->count() + $services->count() + $pendingApprovalsCount + $paymentsNeedingVerificationCount;

            \Log::info('NotificationController: Data counts', [
                'events_count' => $events->count(),
                'celebrations_count' => $celebrations->count(),
                'services_count' => $services->count(),
                'pending_approvals_count' => $pendingApprovalsCount,
                'payments_needing_verification_count' => $paymentsNeedingVerificationCount,
                'total' => $totalNotifications
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'events' => $events,
                    'celebrations' => $celebrations,
                    'services' => $services,
                    'pending_approvals' => $pendingApprovals,
                    'payments_needing_verification' => $paymentsNeedingVerification,
                    'counts' => [
                        'events' => $events->count(),
                        'celebrations' => $celebrations->count(),
                        'services' => $services->count(),
                        'pending_approvals' => $pendingApprovalsCount,
                        'payments_needing_verification' => $paymentsNeedingVerificationCount,
                        'total' => $totalNotifications
                    ],
                    'last_updated' => $now->format('M j, Y g:i A')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch notification data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notification data'
            ], 500);
        }
    }
}
