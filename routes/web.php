<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CelebrationController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\PreventBackHistory;


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\WeeklyAssignmentController;
use App\Http\Controllers\SundayServiceController;
use App\Http\Controllers\SpecialEventController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FinancialApprovalController;
use App\Http\Controllers\PastorDashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

// Leader password change routes - accessible to all leaders (pastor, secretary, treasurer, admin)
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/leader/change-password', [DashboardController::class, 'showChangePassword'])->name('leader.change-password');
    Route::post('/leader/change-password', [DashboardController::class, 'updatePassword'])->name('leader.password.update');
});

// Auth routes with PreventBackHistory middleware
// Treasurer middleware is applied to restrict treasurer access to finance-only routes
Route::middleware(['auth', PreventBackHistory::class, 'treasurer'])->group(function () {
    Route::get('/secretary/dashboard', [DashboardController::class, 'index'])->name('dashboard.secretary');
    Route::get('/pastor/dashboard', [PastorDashboardController::class, 'index'])->name('dashboard.pastor');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/members/view', [MemberController::class, 'view'])
        ->middleware('permission:members.view')
        ->name('members.view');
    // Leaders management routes
    Route::resource('leaders', LeaderController::class);
    Route::post('/leaders/{leader}/deactivate', [LeaderController::class, 'deactivate'])->name('leaders.deactivate');
    Route::post('/leaders/{leader}/reactivate', [LeaderController::class, 'reactivate'])->name('leaders.reactivate');
    // Leadership reports routes
    Route::get('/leaders-reports', [LeaderController::class, 'reports'])->name('leaders.reports');
    Route::get('/leaders-export/csv', [LeaderController::class, 'exportCsv'])->name('leaders.export.csv');
    Route::get('/leaders-export/pdf', [LeaderController::class, 'exportPdf'])->name('leaders.export.pdf');
    // Weekly Assignments routes
    Route::resource('weekly-assignments', WeeklyAssignmentController::class);
    // Member identity cards routes
    Route::get('/members/{member}/identity-card', [MemberController::class, 'identityCard'])->name('members.identity-card');
    
    // Leadership identity cards routes
    Route::get('/leaders/{leader}/identity-card', [LeaderController::class, 'identityCard'])->name('leaders.identity-card');
    Route::get('/leaders-identity-cards/bulk', [LeaderController::class, 'bulkIdentityCards'])->name('leaders.identity-cards.bulk');
    Route::get('/leaders-identity-cards/position/{position}', [LeaderController::class, 'positionIdentityCards'])->name('leaders.identity-cards.position');
    // Test leader appointment SMS
    Route::get('/test-leader-sms', function(Request $request) {
        $to = $request->query('to');
        $name = $request->query('name', 'John Doe');
        $position = $request->query('position', 'Pastor');
        $church = $request->query('church', 'Waumini Church');
        
        if (empty($to)) {
            return response()->json([
                'success' => false,
                'message' => 'Provide query params: to=+255XXXXXXXXX and optional name, position, church'
            ], 422);
        }
        
        try {
            $smsService = app(\App\Services\SmsService::class);
            $result = $smsService->sendLeaderAppointmentNotificationDebug($to, $name, $position, $church);
            
            return response()->json([
                'success' => $result['ok'] ?? false,
                'to' => $to,
                'name' => $name,
                'position' => $position,
                'church' => $church,
                'provider_status' => $result['status'] ?? null,
                'provider_body' => $result['body'] ?? null,
                'reason' => $result['reason'] ?? null,
                'error' => $result['error'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('test.leader.sms');
    // Sunday services UI route
    Route::get('/services/sunday', [SundayServiceController::class, 'index'])->name('services.sunday.index');
    // Special events UI route
    Route::get('/special-events', [SpecialEventController::class, 'index'])->name('special.events.index');
    // Celebrations UI route
    Route::get('/celebrations', [CelebrationController::class, 'index'])->name('celebrations.index');
    
    // Financial Management Routes
    Route::prefix('finance')->name('finance.')->group(function () {
        // Test route
        Route::get('/test', function() {
            return 'Finance test route works!';
        });
        
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\FinanceController::class, 'dashboard'])->name('dashboard');
        
        // Tithes
        Route::get('/tithes', [App\Http\Controllers\FinanceController::class, 'tithes'])->name('tithes');
        Route::post('/tithes', [App\Http\Controllers\FinanceController::class, 'storeTithe'])->name('tithes.store');
        Route::post('/tithes/{tithe}/paid', [App\Http\Controllers\FinanceController::class, 'markTithePaid'])->name('tithes.paid');
        
        // Offerings
        Route::get('/offerings', [App\Http\Controllers\FinanceController::class, 'offerings'])->name('offerings');
        Route::post('/offerings', [App\Http\Controllers\FinanceController::class, 'storeOffering'])->name('offerings.store');
        
        // Donations
        Route::get('/donations', [App\Http\Controllers\FinanceController::class, 'donations'])->name('donations');
        Route::post('/donations', [App\Http\Controllers\FinanceController::class, 'storeDonation'])->name('donations.store');
        
        // Pledges
        Route::get('/pledges', [App\Http\Controllers\FinanceController::class, 'pledges'])->name('pledges');
        Route::post('/pledges', [App\Http\Controllers\FinanceController::class, 'storePledge'])->name('pledges.store');
        Route::post('/pledges/{pledge}/payment', [App\Http\Controllers\FinanceController::class, 'updatePledgePayment'])->name('pledges.payment');
        
        // Budgets
        Route::get('/budgets', [App\Http\Controllers\FinanceController::class, 'budgets'])->name('budgets');
        Route::post('/budgets', [App\Http\Controllers\FinanceController::class, 'storeBudget'])->name('budgets.store');
        Route::post('/budgets/{budget}', [App\Http\Controllers\FinanceController::class, 'updateBudget'])->name('budgets.update');
        Route::delete('/budgets/{budget}', [App\Http\Controllers\FinanceController::class, 'destroyBudget'])->name('budgets.destroy');
        
        // Budget Funding
        Route::post('/budgets/{budget}/allocate-funds', [App\Http\Controllers\FinanceController::class, 'allocateBudgetFunds'])->name('budgets.allocate-funds');
        Route::get('/budgets/{budget}/funding-suggestions', [App\Http\Controllers\FinanceController::class, 'getFundingSuggestions'])->name('budgets.funding-suggestions');
        Route::post('/budgets/new/funding-suggestions', [App\Http\Controllers\FinanceController::class, 'getNewBudgetFundingSuggestions'])->name('budgets.new.funding-suggestions');
        Route::get('/budgets/available-offerings', [App\Http\Controllers\FinanceController::class, 'getAvailableOfferings'])->name('budgets.available-offerings');
        Route::get('/budgets/{budgetId}/info', [App\Http\Controllers\FinanceController::class, 'getBudgetInfo'])->name('budgets.info');
        Route::get('/budgets/{budgetId}/fund-breakdown', [App\Http\Controllers\FinanceController::class, 'getFundBreakdown'])->name('budgets.fund-breakdown');
        Route::get('/budgets/{budgetId}/fund-summary', [App\Http\Controllers\FinanceController::class, 'getFundSummary'])->name('budgets.fund-summary');
        Route::get('/budgets/{budgetId}/line-items', [App\Http\Controllers\FinanceController::class, 'getBudgetLineItems'])->name('budgets.line-items');
        Route::get('/budgets/offering-type-fund-summary', [App\Http\Controllers\FinanceController::class, 'getOfferingTypeFundSummary'])->name('budgets.offering-type-fund-summary');
        
        // Expenses
        Route::get('/expenses', [App\Http\Controllers\FinanceController::class, 'expenses'])->name('expenses');
        Route::post('/expenses', [App\Http\Controllers\FinanceController::class, 'storeExpense'])->name('expenses.store');
        Route::post('/expenses/{expense}', [App\Http\Controllers\FinanceController::class, 'updateExpense'])->name('expenses.update');
        Route::post('/expenses/{expense}/paid', [App\Http\Controllers\FinanceController::class, 'markExpensePaid'])->name('expenses.paid');
        Route::delete('/expenses/{expense}', [App\Http\Controllers\FinanceController::class, 'destroyExpense'])->name('expenses.destroy');
    });

    // Financial Approval Routes (Pastor only)
    Route::prefix('finance/approval')->name('finance.approval.')->group(function () {
        Route::get('/dashboard', [FinancialApprovalController::class, 'dashboard'])->name('dashboard');
        Route::post('/approve', [FinancialApprovalController::class, 'approve'])->name('approve');
        Route::post('/reject', [FinancialApprovalController::class, 'reject'])->name('reject');
        
        // Funding Requests
        Route::get('/funding-requests', [FinancialApprovalController::class, 'fundingRequests'])->name('funding-requests');
        Route::post('/funding-requests/{fundingRequest}/approve', [FinancialApprovalController::class, 'approveFundingRequest'])->name('funding-requests.approve');
        Route::post('/funding-requests/{fundingRequest}/reject', [FinancialApprovalController::class, 'rejectFundingRequest'])->name('funding-requests.reject');
        Route::get('/funding-requests/{fundingRequest}/details', [FinancialApprovalController::class, 'getFundingRequestDetails'])->name('funding-requests.details');
        Route::post('/bulk-approve', [FinancialApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::get('/pending/{type}', [FinancialApprovalController::class, 'pendingByType'])->name('pending');
        Route::get('/approved/{type}', [FinancialApprovalController::class, 'approvedByType'])->name('approved');
        Route::get('/rejected/{type}', [FinancialApprovalController::class, 'rejectedByType'])->name('rejected');
        Route::get('/daily-summary', [FinancialApprovalController::class, 'dailySummary'])->name('daily-summary');
        Route::get('/export-pending', [FinancialApprovalController::class, 'exportPending'])->name('export-pending');
        Route::get('/view-details/{type}/{id}', [FinancialApprovalController::class, 'viewDetails'])->name('view-details');
    });
    
    // Financial Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/overview', [App\Http\Controllers\ReportController::class, 'overview'])->name('overview');
        Route::get('/member-giving', [App\Http\Controllers\ReportController::class, 'memberGiving'])->name('member-giving');
        Route::get('/department-giving', [App\Http\Controllers\ReportController::class, 'departmentGiving'])->name('department-giving');
        Route::get('/income-vs-expenditure', [App\Http\Controllers\ReportController::class, 'incomeVsExpenditure'])->name('income-vs-expenditure');
        Route::get('/budget-performance', [App\Http\Controllers\ReportController::class, 'budgetPerformance'])->name('budget-performance');
        Route::get('/offering-fund-breakdown', [App\Http\Controllers\ReportController::class, 'offeringFundBreakdown'])->name('offering-fund-breakdown');
        Route::get('/monthly-financial', [App\Http\Controllers\ReportController::class, 'monthlyFinancialReport'])->name('monthly-financial');
        Route::get('/weekly-financial', [App\Http\Controllers\ReportController::class, 'weeklyFinancialReport'])->name('weekly-financial');
        Route::get('/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/member-receipt/{memberId}', [App\Http\Controllers\ReportController::class, 'generateMemberReceipt'])->name('member-receipt');
    });
});

// Member routes - Permission-based access control
Route::middleware(['auth', 'treasurer'])->group(function () {
    // Member creation - requires members.create permission
    Route::get('/members/add', function () {
        if (!auth()->user()->hasPermission('members.create') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to add members.');
        }
        return view('members.add-members');
    })->name('members.add');
    
    Route::post('/members', [MemberController::class, 'store'])
        ->middleware('permission:members.create')
        ->name('members.store');
    
    // Test route for debugging
    Route::get('/test-member', function() {
        try {
            $member = new App\Models\Member();
            return response()->json(['status' => 'success', 'message' => 'Member model works', 'fillable' => $member->getFillable()]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    });
    
    // Member viewing - requires members.view permission
    Route::get('/members', [MemberController::class, 'index'])
        ->middleware('permission:members.view')
        ->name('members.index');
    Route::get('/members/next-id', [MemberController::class, 'nextId'])->name('members.next_id');
    Route::get('/members/export/csv', [MemberController::class, 'exportCsv'])->name('members.export.csv');
    
    // PUT and DELETE routes must come before GET routes with parameters
    Route::put('/members/{member}', [MemberController::class, 'update'])
        ->middleware('permission:members.edit')
        ->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])
        ->middleware('permission:members.delete')
        ->name('members.destroy');
    Route::delete('/members/archived/{memberId}', [MemberController::class, 'destroyArchived'])
        ->middleware('permission:members.delete')
        ->name('members.destroy.archived');
    Route::delete('/members/{member}/archive', [MemberController::class, 'archive'])
        ->middleware('permission:members.delete')
        ->name('members.archive');
    Route::post('/members/archived/{memberId}/restore', [MemberController::class, 'restore'])
        ->middleware('permission:members.edit')
        ->name('members.restore');
    
    // Password reset - Admin only
    Route::post('/members/{member}/reset-password', [MemberController::class, 'resetPassword'])
        ->middleware('permission:members.edit')
        ->name('members.reset-password');
    
    // GET route with parameter should come last
    Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show')->where('id', '[0-9]+');
    
    // Children routes
    Route::post('/children', [MemberController::class, 'storeChild'])->name('children.store');
    
    // Test route to check if member exists
    Route::get('/test-member/{id}', function($id) {
        $member = \App\Models\Member::find($id);
        if ($member) {
            return response()->json([
                'success' => true,
                'member' => [
                    'id' => $member->id,
                    'member_id' => $member->member_id,
                    'full_name' => $member->full_name
                ]
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Member not found']);
    });
    
    // List all members for debugging
    Route::get('/list-members', function() {
        $members = \App\Models\Member::select('id', 'member_id', 'full_name')->get();
        $archivedMembers = \App\Models\DeletedMember::select('id', 'member_id', 'member_snapshot')->get();
        
        return response()->json([
            'success' => true,
            'active_members' => [
                'count' => $members->count(),
                'members' => $members
            ],
            'archived_members' => [
                'count' => $archivedMembers->count(),
                'members' => $archivedMembers->map(function($archived) {
                    $snapshot = $archived->member_snapshot;
                    return [
                        'id' => $archived->id,
                        'member_id' => $archived->member_id,
                        'full_name' => $snapshot['full_name'] ?? 'Unknown',
                        'archived_at' => $archived->deleted_at_actual
                    ];
                })
            ]
        ]);
    });
    
    // SMS test route (authenticated)
    Route::get('/test-sms', function(Request $request) {
        $to = $request->query('to');
        $text = $request->query('text', 'Karibu Waumini Church!');
        if (empty($to)) {
            return response()->json([
                'success' => false,
                'message' => 'Provide query params: to=+255XXXXXXXXX and optional text=...'
            ], 422);
        }
        try {
            $resp = app(\App\Services\SmsService::class)->sendDebug($to, $text);
            $conf = [
                'enabled' => \App\Services\SettingsService::get('enable_sms_notifications', false),
                'has_api_url' => !empty(\App\Services\SettingsService::get('sms_api_url')),
                'has_username' => !empty(\App\Services\SettingsService::get('sms_username')),
                'has_password' => !empty(\App\Services\SettingsService::get('sms_password')),
                'has_sender_id' => !empty(\App\Services\SettingsService::get('sms_sender_id')),
                'has_api_key' => !empty(\App\Services\SettingsService::get('sms_api_key')),
            ];
            return response()->json([
                'success' => $resp['ok'] ?? false,
                'to' => $to,
                'text' => $text,
                'provider_status' => $resp['status'] ?? null,
                'provider_body' => $resp['body'] ?? null,
                'reason' => $resp['reason'] ?? null,
                'request' => $resp['request'] ?? null,
                'error' => $resp['error'] ?? null,
                'config' => $conf,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('test.sms');

    // One-time setup route to configure SMS settings (authenticated)
    Route::match(['get','post'], '/setup-sms', function(Request $request) {
        try {
            \App\Models\SystemSetting::setValue('enable_sms_notifications', '1', 'boolean');
            \App\Models\SystemSetting::setValue('sms_api_url', $request->input('sms_api_url', 'https://messaging-service.co.tz/link/sms/v1/text/single'), 'string');
            \App\Models\SystemSetting::setValue('sms_username', $request->input('sms_username', 'emcatechn'), 'string');
            \App\Models\SystemSetting::setValue('sms_password', $request->input('sms_password', 'Emca@#12'), 'string');
            \App\Models\SystemSetting::setValue('sms_sender_id', $request->input('sms_sender_id', 'WauminiLnk'), 'string');

            return response()->json([
                'success' => true,
                'message' => 'SMS settings saved',
                'applied' => [
                    'enable_sms_notifications' => true,
                    'sms_api_url' => $request->input('sms_api_url', 'https://messaging-service.co.tz/link/sms/v1/text/single'),
                    'sms_username' => $request->input('sms_username', 'emcatechn'),
                    'sms_password_set' => true,
                    'sms_sender_id' => $request->input('sms_sender_id', 'MkulimaLink'),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    })->name('setup.sms');

    // Test payment approval SMS
    Route::get('/test-payment-sms', function(Request $request) {
        $to = $request->query('to');
        $memberName = $request->query('name', 'John Doe');
        $paymentType = $request->query('type', 'Tithe');
        $amount = $request->query('amount', 50000);
        $date = $request->query('date', date('Y-m-d'));
        
        if (empty($to)) {
            return response()->json([
                'success' => false,
                'message' => 'Provide query params: to=+255XXXXXXXXX and optional name, type, amount, date'
            ], 422);
        }
        
        try {
            $smsService = app(\App\Services\SmsService::class);
            $resp = $smsService->sendPaymentApprovalNotificationDebug($to, $memberName, $paymentType, $amount, $date);
            
            $conf = [
                'enabled' => \App\Services\SettingsService::get('enable_sms_notifications', false),
                'has_api_url' => !empty(\App\Services\SettingsService::get('sms_api_url')),
                'has_username' => !empty(\App\Services\SettingsService::get('sms_username')),
                'has_password' => !empty(\App\Services\SettingsService::get('sms_password')),
                'has_sender_id' => !empty(\App\Services\SettingsService::get('sms_sender_id')),
                'has_api_key' => !empty(\App\Services\SettingsService::get('sms_api_key')),
            ];
            
            return response()->json([
                'success' => $resp['ok'] ?? false,
                'to' => $to,
                'member_name' => $memberName,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'date' => $date,
                'provider_status' => $resp['status'] ?? null,
                'provider_body' => $resp['body'] ?? null,
                'reason' => $resp['reason'] ?? null,
                'request' => $resp['request'] ?? null,
                'error' => $resp['error'] ?? null,
                'config' => $conf,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('test.payment.sms');
    

    // Sunday services routes
    Route::post('/services/sunday', [SundayServiceController::class, 'store'])->name('services.sunday.store');
    // Specific routes must come before parameterized routes
    Route::get('/services/sunday/church-elders', [SundayServiceController::class, 'getChurchElders'])->name('services.sunday.church.elders');
    Route::get('/services/sunday/weekly-assignment', [SundayServiceController::class, 'getWeeklyAssignmentForDate'])->name('services.sunday.weekly.assignment');
    Route::get('/services/sunday-export/csv', [SundayServiceController::class, 'exportCsv'])->name('services.sunday.export.csv');
    // Parameterized routes come last
    Route::get('/services/sunday/{sundayService}', [SundayServiceController::class, 'show'])->name('services.sunday.show');
    Route::put('/services/sunday/{sundayService}', [SundayServiceController::class, 'update'])->name('services.sunday.update');
    Route::delete('/services/sunday/{sundayService}', [SundayServiceController::class, 'destroy'])->name('services.sunday.destroy');

    // Special events routes
    Route::post('/special-events', [SpecialEventController::class, 'store'])->name('special.events.store');
    Route::get('/special-events/{specialEvent}', [SpecialEventController::class, 'show'])->name('special.events.show');
    Route::put('/special-events/{specialEvent}', [SpecialEventController::class, 'update'])->name('special.events.update');
    Route::delete('/special-events/{specialEvent}', [SpecialEventController::class, 'destroy'])->name('special.events.destroy');
    Route::get('/special-events-members/notification', [SpecialEventController::class, 'getMembersForNotification'])->name('special.events.members.notification');

    // Attendance routes
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/member/{memberId}/history', [App\Http\Controllers\AttendanceController::class, 'memberHistory'])->name('attendance.member.history');
    Route::get('/attendance/service/{serviceType}/{serviceId}/report', [App\Http\Controllers\AttendanceController::class, 'serviceReport'])->name('attendance.service.report');
    Route::get('/attendance/statistics', [App\Http\Controllers\AttendanceController::class, 'statistics'])->name('attendance.statistics');
    Route::post('/attendance/trigger-notifications', [App\Http\Controllers\AttendanceController::class, 'triggerNotifications'])->name('attendance.trigger.notifications');
    Route::get('/attendance/missed-members', [App\Http\Controllers\AttendanceController::class, 'getMembersWithMissedAttendance'])->name('attendance.missed.members');

    // Promise Guests routes
    Route::resource('promise-guests', App\Http\Controllers\PromiseGuestController::class);
    Route::post('/promise-guests/{promiseGuest}/send-notification', [App\Http\Controllers\PromiseGuestController::class, 'sendNotification'])->name('promise-guests.send-notification');
    Route::post('/promise-guests/{promiseGuest}/mark-attended', [App\Http\Controllers\PromiseGuestController::class, 'markAttended'])->name('promise-guests.mark-attended');

    // Celebrations routes
    Route::post('/celebrations', [CelebrationController::class, 'store'])->name('celebrations.store');
    Route::get('/celebrations/{celebration}', [CelebrationController::class, 'show'])->name('celebrations.show');
    Route::put('/celebrations/{celebration}', [CelebrationController::class, 'update'])->name('celebrations.update');
    Route::delete('/celebrations/{celebration}', [CelebrationController::class, 'destroy'])->name('celebrations.destroy');
    Route::get('/celebrations-export/csv', [CelebrationController::class, 'exportCsv'])->name('celebrations.export.csv');

    // Bereavement routes
    Route::get('/bereavement', [App\Http\Controllers\BereavementController::class, 'index'])->name('bereavement.index');
    Route::get('/bereavement/create', [App\Http\Controllers\BereavementController::class, 'create'])->name('bereavement.create');
    Route::post('/bereavement', [App\Http\Controllers\BereavementController::class, 'store'])->name('bereavement.store');
    Route::get('/bereavement/{bereavement}', [App\Http\Controllers\BereavementController::class, 'show'])->name('bereavement.show');
    Route::put('/bereavement/{bereavement}', [App\Http\Controllers\BereavementController::class, 'update'])->name('bereavement.update');
    Route::delete('/bereavement/{bereavement}', [App\Http\Controllers\BereavementController::class, 'destroy'])->name('bereavement.destroy');
    Route::post('/bereavement/{bereavement}/contribution', [App\Http\Controllers\BereavementController::class, 'recordContribution'])->name('bereavement.record-contribution');
    Route::post('/bereavement/{bereavement}/non-contributor', [App\Http\Controllers\BereavementController::class, 'markNonContributor'])->name('bereavement.mark-non-contributor');
    Route::post('/bereavement/{bereavement}/close', [App\Http\Controllers\BereavementController::class, 'close'])->name('bereavement.close');
    Route::get('/bereavement/{bereavement}/summary', [App\Http\Controllers\BereavementController::class, 'summaryReport'])->name('bereavement.summary');
    Route::get('/bereavement/{bereavement}/export/{format}', [App\Http\Controllers\BereavementController::class, 'exportReport'])->name('bereavement.export');
    Route::get('/bereavement-members/notification', [App\Http\Controllers\BereavementController::class, 'getMembersForNotification'])->name('bereavement.members.notification');
});

// Settings routes - Permission-based access control
Route::middleware(['auth', PreventBackHistory::class, 'treasurer'])->group(function () {
    // View settings - requires settings.view permission
    Route::get('/settings', [SettingsController::class, 'index'])
        ->middleware('permission:settings.view')
        ->name('settings.index');
    
    // Update settings - requires settings.edit permission
    Route::post('/settings', [SettingsController::class, 'update'])
        ->middleware('permission:settings.edit')
        ->name('settings.update');
    Route::post('/settings/{category}', [SettingsController::class, 'updateCategory'])
        ->middleware('permission:settings.edit')
        ->name('settings.update.category');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])
        ->middleware('permission:settings.edit')
        ->name('settings.reset');
    Route::post('/settings/import', [SettingsController::class, 'import'])
        ->middleware('permission:settings.edit')
        ->name('settings.import');
    Route::post('/settings/set/{key}', [SettingsController::class, 'setValue'])
        ->middleware('permission:settings.edit')
        ->name('settings.set');
    
    // View-only routes - require settings.view permission
    Route::get('/settings/export', [SettingsController::class, 'export'])
        ->middleware('permission:settings.view')
        ->name('settings.export');
    Route::get('/settings/get/{key}', [SettingsController::class, 'getValue'])
        ->middleware('permission:settings.view')
        ->name('settings.get');
    Route::get('/settings/audit-logs', [SettingsController::class, 'auditLogs'])
        ->middleware('permission:settings.view')
        ->name('settings.audit-logs');
    Route::get('/settings/backup', [SettingsController::class, 'backup'])
        ->middleware('permission:settings.view')
        ->name('settings.backup');
    Route::post('/settings/restore', [SettingsController::class, 'restore'])
        ->middleware('permission:settings.edit')
        ->name('settings.restore');
    Route::get('/settings/help', function() { return view('settings.help'); })
        ->middleware('permission:settings.view')
        ->name('settings.help');
    Route::get('/settings/analytics', [SettingsController::class, 'analytics'])
        ->middleware('permission:settings.view')
        ->name('settings.analytics');
});



Route::get('/', function () {
    return view('welcome');
})->name('landing_page');


// Redirect /dashboard based on user role
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isPastor()) {
        return redirect()->route('dashboard.pastor');
    } elseif ($user->isTreasurer()) {
        return redirect()->route('finance.dashboard');
    } elseif ($user->isMember()) {
        return redirect()->route('member.dashboard');
    } else {
        return redirect()->route('dashboard.secretary');
    }
})->middleware('auth')->name('dashboard');



// Test route for debugging member creation
Route::get('/test-member-creation', function () {
    try {
        $member = \App\Models\Member::create([
            'member_id' => \App\Models\Member::generateMemberId(),
            'member_type' => 'independent',
            'membership_type' => 'permanent',
            'full_name' => 'Test User',
            'phone_number' => '+255712345678',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'profession' => 'Developer',
            'region' => 'Dar es Salaam',
            'district' => 'Kinondoni',
            'ward' => 'Test Ward',
            'street' => 'Test Street',
            'address' => 'Test Address',
            'tribe' => 'Test Tribe',
        ]);
        
        return response()->json(['success' => true, 'member' => $member]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Test route for debugging special event creation
Route::get('/test-special-event-creation', function () {
    try {
        $event = \App\Models\SpecialEvent::create([
            'event_date' => '2024-01-01',
            'title' => 'Test Event',
            'speaker' => 'Test Speaker',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'venue' => 'Test Venue',
            'attendance_count' => 50,
            'budget_amount' => 1000.00,
            'category' => 'Test Category',
            'description' => 'Test Description',
            'notes' => 'Test Notes',
        ]);
        
        return response()->json(['success' => true, 'event' => $event]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Test route to get CSRF token
Route::get('/test-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'message' => 'CSRF token generated'
    ]);
});

// Public SMS diagnostic route (temporary): returns provider response without auth
Route::get('/public-test-sms', function (Request $request) {
    $to = $request->query('to');
    $text = $request->query('text', 'Karibu Waumini Church!');
    if (empty($to)) {
        return response()->json([
            'success' => false,
            'message' => 'Provide query params: to=+255XXXXXXXXX and optional text=...'
        ], 422);
    }
    try {
        $resp = app(\App\Services\SmsService::class)->sendDebug($to, $text);
        $conf = [
            'enabled' => \App\Services\SettingsService::get('enable_sms_notifications', false),
            'api_url' => \App\Services\SettingsService::get('sms_api_url'),
            'username' => \App\Services\SettingsService::get('sms_username'),
            'sender_id' => \App\Services\SettingsService::get('sms_sender_id'),
        ];
        return response()->json([
            'success' => $resp['ok'] ?? false,
            'to' => $to,
            'text' => $text,
            'provider_status' => $resp['status'] ?? null,
            'provider_body' => $resp['body'] ?? null,
            'reason' => $resp['reason'] ?? null,
            'request' => $resp['request'] ?? null,
            'error' => $resp['error'] ?? null,
            'config' => $conf,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// Debug route for testing authentication
Route::get('/debug-auth', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    $user = auth()->user();
    return response()->json([
        'user' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'can_approve_finances' => $user->can_approve_finances,
        'isPastor' => $user->isPastor(),
        'isAdmin' => $user->isAdmin(),
        'isTreasurer' => $user->isTreasurer(),
        'canApproveFinances' => $user->canApproveFinances(),
        'dashboard_redirect' => $user->isPastor() ? 'pastor' : ($user->isTreasurer() ? 'treasurer' : 'secretary')
    ]);
})->middleware('auth');

// Simple test route for approval dashboard
Route::get('/test-approval', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    $user = auth()->user();
    if (!$user->canApproveFinances()) {
        return 'Unauthorized - cannot approve finances';
    }
    
    return 'Authorized - can approve finances';
})->middleware('auth');

// Very simple test route
Route::get('/simple-test', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    $user = auth()->user();
    return 'Logged in as: ' . $user->name . ' (Role: ' . $user->role . ')';
})->middleware('auth');

// Debug route to test special event creation
Route::post('/debug-special-events', function (Request $request) {
    \Log::info('Debug special event route called', ['request_data' => $request->all()]);
    
    try {
        $event = \App\Models\SpecialEvent::create([
            'event_date' => $request->event_date,
            'title' => $request->title,
            'speaker' => $request->speaker,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'venue' => $request->venue,
            'attendance_count' => $request->attendance_count,
            'budget_amount' => $request->budget_amount,
            'category' => $request->category,
            'description' => $request->description,
            'notes' => $request->notes,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'event' => $event
        ], 200);
    } catch (Exception $e) {
        \Log::error('Debug special event error', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Test email configuration
Route::get('/test-email', function () {
    try {
        \Mail::raw('Test email from Waumini Link notification system!', function($message) {
            $message->to('oftenfred.ict@gmail.com')
                    ->subject('Waumini Link - Email Test');
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully to oftenfred.ict@gmail.com'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Email error: ' . $e->getMessage()
        ]);
    }
});

// Notification data route
Route::get('/notifications/data', [NotificationController::class, 'getNotificationData'])->name('notifications.data');

// Test route to check database records
Route::get('/test-notifications', function() {
    try {
        $events = \App\Models\SpecialEvent::count();
        $celebrations = \App\Models\Celebration::count();
        $services = \App\Models\SundayService::count();
        
        return response()->json([
            'success' => true,
            'events' => $events,
            'celebrations' => $celebrations,
            'services' => $services,
            'total' => $events + $celebrations + $services
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug route to test notification controller
Route::get('/debug-notifications', function() {
    try {
        $controller = new \App\Http\Controllers\NotificationController();
        $response = $controller->getNotificationData();
        return $response;
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Test route to check approval system
Route::get('/test-approval-system', function() {
    try {
        // Check if we have any pending records
        $pendingTithes = \App\Models\Tithe::where('approval_status', 'pending')->count();
        $pendingOfferings = \App\Models\Offering::where('approval_status', 'pending')->count();
        $pendingDonations = \App\Models\Donation::where('approval_status', 'pending')->count();
        $pendingExpenses = \App\Models\Expense::where('approval_status', 'pending')->count();
        $pendingBudgets = \App\Models\Budget::where('approval_status', 'pending')->count();
        
        $totalPending = $pendingTithes + $pendingOfferings + $pendingDonations + $pendingExpenses + $pendingBudgets;
        
        return response()->json([
            'success' => true,
            'message' => 'Approval system check',
            'pending_records' => [
                'tithes' => $pendingTithes,
                'offerings' => $pendingOfferings,
                'donations' => $pendingDonations,
                'expenses' => $pendingExpenses,
                'budgets' => $pendingBudgets,
                'total' => $totalPending
            ],
            'approval_url' => '/finance/approval/dashboard'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Simple test route to create one pending record
Route::get('/create-simple-test-data', function() {
    try {
        // Create a simple test member
        $member = \App\Models\Member::firstOrCreate(
            ['member_id' => 'TEST001'],
            [
                'member_type' => 'independent',
                'membership_type' => 'permanent',
                'full_name' => 'Test Member for Approval',
                'phone_number' => '+255712345678',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'profession' => 'Developer',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Test Ward',
                'street' => 'Test Street',
                'address' => 'Test Address',
                'tribe' => 'Test Tribe',
            ]
        );

        $today = \Carbon\Carbon::today();

        // Create one pending tithe
        $tithe = \App\Models\Tithe::create([
            'member_id' => $member->id,
            'amount' => 50.00,
            'tithe_date' => $today,
            'payment_method' => 'cash',
            'reference_number' => 'TEST' . time(),
            'notes' => 'Test tithe for approval testing',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test data created successfully!',
            'tithe_id' => $tithe->id,
            'approval_url' => '/finance/approval/dashboard'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Test route to create data for all tabs
Route::get('/create-all-tab-test-data', function() {
    try {
        // Create a simple test member
        $member = \App\Models\Member::firstOrCreate(
            ['member_id' => 'TABTEST001'],
            [
                'member_type' => 'independent',
                'membership_type' => 'permanent',
                'full_name' => 'Tab Test Member',
                'phone_number' => '+255712345679',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'profession' => 'Developer',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Test Ward',
                'street' => 'Test Street',
                'address' => 'Test Address',
                'tribe' => 'Test Tribe',
            ]
        );

        $today = \Carbon\Carbon::today();
        $time = time();

        // Create pending tithe
        $tithe = \App\Models\Tithe::create([
            'member_id' => $member->id,
            'amount' => 100.00,
            'tithe_date' => $today,
            'payment_method' => 'cash',
            'reference_number' => 'TITHE' . $time,
            'notes' => 'Test tithe for tab testing',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        // Create pending offering
        $offering = \App\Models\Offering::create([
            'member_id' => $member->id,
            'amount' => 50.00,
            'offering_date' => $today,
            'payment_method' => 'cash',
            'reference_number' => 'OFFER' . $time,
            'notes' => 'Test offering for tab testing',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        // Create pending donation
        $donation = \App\Models\Donation::create([
            'donor_name' => 'Tab Test Donor',
            'amount' => 200.00,
            'donation_date' => $today,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'DONATE' . $time,
            'notes' => 'Test donation for tab testing',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        // Create pending expense
        $expense = \App\Models\Expense::create([
            'description' => 'Test expense for tab testing',
            'amount' => 75.00,
            'expense_date' => $today,
            'category' => 'office_supplies',
            'payment_method' => 'cash',
            'reference_number' => 'EXPENSE' . $time,
            'notes' => 'Test expense for tab testing',
            'recorded_by' => 'Test User',
            'status' => 'pending',
            'approval_status' => 'pending'
        ]);

        // Create pending budget
        $budget = \App\Models\Budget::create([
            'name' => 'Test Budget for Tab Testing',
            'amount' => 1000.00,
            'start_date' => $today,
            'end_date' => $today->copy()->addMonth(),
            'category' => 'general',
            'description' => 'Test budget for tab testing',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All tab test data created successfully!',
            'records' => [
                'tithe_id' => $tithe->id,
                'offering_id' => $offering->id,
                'donation_id' => $donation->id,
                'expense_id' => $expense->id,
                'budget_id' => $budget->id
            ],
            'approval_url' => '/finance/approval/dashboard'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Test route to create a test offering for approval
Route::get('/test-offering-approval', function() {
    try {
        // Create a test member if none exists
        $member = \App\Models\Member::first();
        if (!$member) {
            $member = \App\Models\Member::create([
                'member_id' => 'TEST001',
                'member_type' => 'independent',
                'membership_type' => 'permanent',
                'full_name' => 'Test Member for Offering',
                'phone_number' => '+255712345678',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'profession' => 'Developer',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Test Ward',
                'street' => 'Test Street',
                'address' => 'Test Address',
                'tribe' => 'Test Tribe',
            ]);
        }

        $today = \Carbon\Carbon::today();

        // Create a test offering
        $offering = \App\Models\Offering::create([
            'member_id' => $member->id,
            'amount' => 100.00,
            'offering_date' => $today,
            'offering_type' => 'general',
            'service_type' => 'sunday_service',
            'payment_method' => 'cash',
            'reference_number' => 'TEST' . time(),
            'notes' => 'Test offering for approval system',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test offering created successfully!',
            'offering' => [
                'id' => $offering->id,
                'amount' => $offering->amount,
                'approval_status' => $offering->approval_status,
                'member_name' => $offering->member->full_name
            ],
            'pastor_dashboard_url' => '/pastor/dashboard',
            'approval_dashboard_url' => '/finance/approval/dashboard'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Test route to create approval test data
Route::get('/create-approval-test-data', function() {
    try {
        $member = \App\Models\Member::first();
        if (!$member) {
            $member = \App\Models\Member::create([
                'member_id' => 'M001',
                'member_type' => 'independent',
                'membership_type' => 'permanent',
                'full_name' => 'Test Member',
                'phone_number' => '+255712345678',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'profession' => 'Developer',
                'region' => 'Dar es Salaam',
                'district' => 'Kinondoni',
                'ward' => 'Test Ward',
                'street' => 'Test Street',
                'address' => 'Test Address',
                'tribe' => 'Test Tribe',
            ]);
        }

        $today = \Carbon\Carbon::today();

        // Create pending tithe
        \App\Models\Tithe::create([
            'member_id' => $member->id,
            'amount' => 100.00,
            'tithe_date' => $today,
            'payment_method' => 'cash',
            'reference_number' => 'T' . time(),
            'notes' => 'Test tithe for approval',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        // Create pending offering
        \App\Models\Offering::create([
            'member_id' => $member->id,
            'amount' => 50.00,
            'offering_date' => $today,
            'payment_method' => 'cash',
            'reference_number' => 'O' . time(),
            'notes' => 'Test offering for approval',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        // Create pending donation
        \App\Models\Donation::create([
            'donor_name' => 'General Member',
            'amount' => 200.00,
            'donation_date' => $today,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'D' . time(),
            'notes' => 'Test donation for approval',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        // Create pending expense
        \App\Models\Expense::create([
            'description' => 'Test expense for approval',
            'amount' => 75.00,
            'expense_date' => $today,
            'category' => 'office_supplies',
            'payment_method' => 'cash',
            'reference_number' => 'E' . time(),
            'notes' => 'Test expense for approval',
            'recorded_by' => 'Test User',
            'status' => 'pending',
            'approval_status' => 'pending'
        ]);

        // Create pending budget
        \App\Models\Budget::create([
            'name' => 'Test Budget for Approval',
            'amount' => 1000.00,
            'start_date' => $today,
            'end_date' => $today->copy()->addMonth(),
            'category' => 'general',
            'description' => 'Test budget for approval',
            'recorded_by' => 'Test User',
            'approval_status' => 'pending'
        ]);

        $totalPending = \App\Models\Tithe::where('approval_status', 'pending')->count() + 
            \App\Models\Offering::where('approval_status', 'pending')->count() + 
            \App\Models\Donation::where('approval_status', 'pending')->count() + 
            \App\Models\Expense::where('approval_status', 'pending')->count() + 
            \App\Models\Budget::where('approval_status', 'pending')->count();

        return response()->json([
            'success' => true,
            'message' => 'Test approval data created successfully!',
            'total_pending' => $totalPending,
            'redirect_url' => '/finance/approval/dashboard'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Admin routes - Only accessible by administrators
Route::middleware(['auth', PreventBackHistory::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
    Route::delete('/sessions/{sessionId}/revoke', [AdminController::class, 'revokeSession'])->name('sessions.revoke');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::post('/users/{userId}/unblock', [AdminController::class, 'unblockUser'])->name('users.unblock');
    Route::get('/users/{userId}/activity', [AdminController::class, 'userActivity'])->name('user-activity');
    Route::get('/users/{userId}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{userId}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{userId}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{userId}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/roles-permissions', [AdminController::class, 'rolesPermissions'])->name('roles-permissions');
    Route::post('/roles-permissions/update', [AdminController::class, 'updateRolePermissions'])->name('roles-permissions.update');
});

// Announcements routes (for secretary/admin)
Route::middleware(['auth', PreventBackHistory::class, 'treasurer'])->group(function () {
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/send-sms', [AnnouncementController::class, 'sendSms'])->name('announcements.send-sms');
});

// Member routes
Route::middleware(['auth', PreventBackHistory::class])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/information', [MemberDashboardController::class, 'information'])->name('information');
    Route::get('/finance', [MemberDashboardController::class, 'finance'])->name('finance');
    Route::get('/announcements', [MemberDashboardController::class, 'announcements'])->name('announcements');
    Route::get('/leaders', [MemberDashboardController::class, 'leaders'])->name('leaders');
    Route::get('/change-password', [MemberDashboardController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [MemberDashboardController::class, 'updatePassword'])->name('password.update');
    Route::post('/notifications/{notification}/read', [MemberDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
});


