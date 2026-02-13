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

// OTP verification routes
Route::get('/login/otp/verify', [AuthController::class, 'showOtpVerification'])->name('login.otp.verify');
Route::post('/login/otp/verify', [AuthController::class, 'verifyOtp'])->name('login.otp.verify.post');
Route::post('/login/otp/resend', [AuthController::class, 'resendOtp'])->name('login.otp.resend');

// Password reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

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
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ZKTecoController;
use App\Http\Controllers\UsharikaDashboardController;
use App\Http\Controllers\BranchDashboardController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\EvangelismLeaderController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ChurchElderController;
use App\Http\Controllers\CommunityOfferingController;
use App\Http\Controllers\ParishWorkerController;
use App\Http\Controllers\BaptismApplicationController;
use App\Http\Controllers\ReturnToFellowshipRequestController;
use App\Http\Controllers\MarriageBlessingRequestController;
use App\Http\Controllers\ChurchWeddingRequestController;
use App\Http\Controllers\SocialWelfareReportController;
use App\Http\Controllers\Finance\AhadiPledgeController;

// Language switching routes
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');
Route::get('/api/language/current', [LanguageController::class, 'getCurrentLocale'])->name('language.current');

// Leader password change routes - accessible to all leaders (pastor, secretary, treasurer, admin)
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/leader/change-password', [LeaderController::class, 'showChangePassword'])->name('leader.change-password');
    Route::post('/leader/change-password', [LeaderController::class, 'updatePassword'])->name('leader.password.update');

    // Member lookup route
    Route::get('/member/lookup', [MemberController::class, 'lookup'])->name('member.lookup');
});

// Secretary routes for community offerings
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::prefix('secretary')->name('secretary.')->group(function () {
        Route::get('/offerings', [CommunityOfferingController::class, 'index'])->name('offerings.index');
        Route::post('/offerings/{offering}/confirm', [CommunityOfferingController::class, 'confirmBySecretary'])->name('offerings.confirm');
        Route::get('/offerings/{offering}', [CommunityOfferingController::class, 'show'])->name('offerings.show');
    });

    // Parish Worker routes
    Route::prefix('parish-worker')->name('parish-worker.')->group(function () {
        Route::get('/dashboard', [ParishWorkerController::class, 'index'])->name('dashboard');
        Route::get('/activities', [ParishWorkerController::class, 'activitiesIndex'])->name('activities.index');
        Route::get('/activities/create', [ParishWorkerController::class, 'createActivity'])->name('activities.create');
        Route::post('/activities', [ParishWorkerController::class, 'storeActivity'])->name('activities.store');

        Route::get('/reports', [ParishWorkerController::class, 'reportsIndex'])->name('reports.index');
        Route::get('/reports/create', [ParishWorkerController::class, 'createReport'])->name('reports.create');
        Route::post('/reports', [ParishWorkerController::class, 'storeReport'])->name('reports.store');
        Route::get('/reports/{report}', [ParishWorkerController::class, 'showReport'])->name('reports.show');

        // Candle Management
        Route::get('/candles', [ParishWorkerController::class, 'candlesIndex'])->name('candles.index');
        Route::get('/candles/create', [ParishWorkerController::class, 'createCandleAction'])->name('candles.create');
        Route::post('/candles', [ParishWorkerController::class, 'storeCandleAction'])->name('candles.store');
    });

    // Pastor routes for baptism applications
    Route::prefix('pastor')->name('pastor.')->group(function () {
        Route::get('/baptism-applications/pending', [BaptismApplicationController::class, 'pending'])->name('baptism-applications.pending');
        Route::post('/baptism-applications/{baptismApplication}/approve', [BaptismApplicationController::class, 'approve'])->name('baptism-applications.approve');
        Route::post('/baptism-applications/{baptismApplication}/reject', [BaptismApplicationController::class, 'reject'])->name('baptism-applications.reject');
        Route::post('/baptism-applications/{baptismApplication}/schedule', [BaptismApplicationController::class, 'schedule'])->name('baptism-applications.schedule');
        Route::post('/baptism-applications/{baptismApplication}/complete', [BaptismApplicationController::class, 'complete'])->name('baptism-applications.complete');
        Route::get('/baptism-applications/{baptismApplication}', [BaptismApplicationController::class, 'show'])->name('baptism-applications.show');

        // Return to Fellowship Requests routes
        Route::get('/return-to-fellowship-requests/pending', [ReturnToFellowshipRequestController::class, 'pending'])->name('return-to-fellowship-requests.pending');
        Route::post('/return-to-fellowship-requests/{returnToFellowshipRequest}/approve', [ReturnToFellowshipRequestController::class, 'approve'])->name('return-to-fellowship-requests.approve');
        Route::post('/return-to-fellowship-requests/{returnToFellowshipRequest}/approve-after-counseling', [ReturnToFellowshipRequestController::class, 'approveAfterCounseling'])->name('return-to-fellowship-requests.approve-after-counseling');
        Route::post('/return-to-fellowship-requests/{returnToFellowshipRequest}/reject', [ReturnToFellowshipRequestController::class, 'reject'])->name('return-to-fellowship-requests.reject');
        Route::post('/return-to-fellowship-requests/{returnToFellowshipRequest}/require-counseling', [ReturnToFellowshipRequestController::class, 'requireCounseling'])->name('return-to-fellowship-requests.require-counseling');
        Route::get('/return-to-fellowship-requests/{returnToFellowshipRequest}', [ReturnToFellowshipRequestController::class, 'show'])->name('return-to-fellowship-requests.show');

        // Marriage Blessing Requests routes
        Route::get('/marriage-blessing-requests/pending', [MarriageBlessingRequestController::class, 'pending'])->name('marriage-blessing-requests.pending');
        Route::post('/marriage-blessing-requests/{marriageBlessingRequest}/approve', [MarriageBlessingRequestController::class, 'approve'])->name('marriage-blessing-requests.approve');
        Route::post('/marriage-blessing-requests/{marriageBlessingRequest}/reject', [MarriageBlessingRequestController::class, 'reject'])->name('marriage-blessing-requests.reject');
        Route::post('/marriage-blessing-requests/{marriageBlessingRequest}/require-counseling', [MarriageBlessingRequestController::class, 'requireCounseling'])->name('marriage-blessing-requests.require-counseling');
        Route::post('/marriage-blessing-requests/{marriageBlessingRequest}/schedule-meeting', [MarriageBlessingRequestController::class, 'scheduleMeeting'])->name('marriage-blessing-requests.schedule-meeting');
        Route::get('/marriage-blessing-requests/{marriageBlessingRequest}/edit', [MarriageBlessingRequestController::class, 'edit'])->name('marriage-blessing-requests.edit');
        Route::patch('/marriage-blessing-requests/{marriageBlessingRequest}', [MarriageBlessingRequestController::class, 'update'])->name('marriage-blessing-requests.update');
        Route::get('/marriage-blessing-requests/{marriageBlessingRequest}', [MarriageBlessingRequestController::class, 'show'])->name('marriage-blessing-requests.show');

        // Church Wedding Requests routes
        Route::get('/church-wedding-requests/pending', [ChurchWeddingRequestController::class, 'pending'])->name('church-wedding-requests.pending');
        Route::post('/church-wedding-requests/{churchWeddingRequest}/approve', [ChurchWeddingRequestController::class, 'approve'])->name('church-wedding-requests.approve');
        Route::post('/church-wedding-requests/{churchWeddingRequest}/reject', [ChurchWeddingRequestController::class, 'reject'])->name('church-wedding-requests.reject');
        Route::post('/church-wedding-requests/{churchWeddingRequest}/require-documents', [ChurchWeddingRequestController::class, 'requireDocuments'])->name('church-wedding-requests.require-documents');
        Route::post('/church-wedding-requests/{churchWeddingRequest}/schedule-meeting', [ChurchWeddingRequestController::class, 'scheduleMeeting'])->name('church-wedding-requests.schedule-meeting');
        Route::get('/church-wedding-requests/{churchWeddingRequest}/edit', [ChurchWeddingRequestController::class, 'edit'])->name('church-wedding-requests.edit');
        Route::patch('/church-wedding-requests/{churchWeddingRequest}', [ChurchWeddingRequestController::class, 'update'])->name('church-wedding-requests.update');
        Route::get('/church-wedding-requests/{churchWeddingRequest}', [ChurchWeddingRequestController::class, 'show'])->name('church-wedding-requests.show');

        // Temporary Membership Management routes
        Route::get('/temporary-memberships', [App\Http\Controllers\TemporaryMembershipController::class, 'index'])->name('temporary-memberships.index');
        Route::get('/temporary-memberships/{member}', [App\Http\Controllers\TemporaryMembershipController::class, 'show'])->name('temporary-memberships.show');
        Route::post('/temporary-memberships/{member}/extend', [App\Http\Controllers\TemporaryMembershipController::class, 'extend'])->name('temporary-memberships.extend');
        Route::post('/temporary-memberships/{member}/convert', [App\Http\Controllers\TemporaryMembershipController::class, 'convertToPermanent'])->name('temporary-memberships.convert');
        Route::post('/temporary-memberships/{member}/complete', [App\Http\Controllers\TemporaryMembershipController::class, 'markCompleted'])->name('temporary-memberships.complete');

        // Tasks, Issues, and Reports routes
        Route::get('/tasks', [PastorDashboardController::class, 'allTasks'])->name('tasks.index');
        Route::get('/tasks/evangelism/{task}', [PastorDashboardController::class, 'showEvangelismTask'])->name('tasks.show-evangelism');
        Route::get('/tasks/church-elder/{task}', [PastorDashboardController::class, 'showChurchElderTask'])->name('tasks.show-elder');
        Route::post('/tasks/evangelism/{task}/comment', [PastorDashboardController::class, 'commentEvangelismTask'])->name('tasks.comment-evangelism');
        Route::post('/tasks/church-elder/{task}/comment', [PastorDashboardController::class, 'commentChurchElderTask'])->name('tasks.comment-elder');

        Route::get('/issues', [PastorDashboardController::class, 'allIssues'])->name('issues.index');
        Route::get('/issues/evangelism/{issue}', [PastorDashboardController::class, 'showEvangelismIssue'])->name('issues.show-evangelism');
        Route::get('/issues/church-elder/{issue}', [PastorDashboardController::class, 'showChurchElderIssue'])->name('issues.show-elder');
        Route::post('/issues/evangelism/{issue}/comment', [PastorDashboardController::class, 'commentEvangelismIssue'])->name('issues.comment-evangelism');
        Route::post('/issues/church-elder/{issue}/comment', [PastorDashboardController::class, 'commentChurchElderIssue'])->name('issues.comment-elder');
        Route::post('/issues/evangelism/{issue}/status', [PastorDashboardController::class, 'updateEvangelismIssueStatus'])->name('issues.update-status-evangelism');
        Route::post('/issues/church-elder/{issue}/status', [PastorDashboardController::class, 'updateChurchElderIssueStatus'])->name('issues.update-status-elder');

        Route::get('/reports', [PastorDashboardController::class, 'allReports'])->name('reports.index');
        Route::get('/parish-worker/reports', [PastorDashboardController::class, 'allParishWorkerReports'])->name('parish-worker.reports.index');
        Route::get('/parish-worker/reports/{report}', [PastorDashboardController::class, 'showParishWorkerReport'])->name('parish-worker.reports.show');
        Route::post('/parish-worker/reports/{report}/comment', [PastorDashboardController::class, 'commentParishWorkerReport'])->name('parish-worker.reports.comment');
        Route::get('/parish-worker/activities', [PastorDashboardController::class, 'allParishWorkerActivities'])->name('parish-worker.activities.index');
        Route::get('/parish-worker/candles', [PastorDashboardController::class, 'allCandleActions'])->name('parish-worker.candles.index');

        // Members outside main area
        Route::get('/members-outside-main-area', [MemberController::class, 'outsideMainArea'])->name('members.outside-main-area');
        Route::post('/members-outside-main-area/send-sms', [MemberController::class, 'sendSmsToOutsideMembers'])->name('members.outside-main-area.send-sms');

        // Child to Member Transitions
        Route::get('/transitions', [App\Http\Controllers\TransitionController::class, 'index'])->name('transitions.index');
        Route::get('/transitions/{transition}', [App\Http\Controllers\TransitionController::class, 'show'])->name('transitions.show');
        Route::post('/transitions/{transition}/approve', [App\Http\Controllers\TransitionController::class, 'approve'])->name('transitions.approve');
        Route::post('/transitions/{transition}/reject', [App\Http\Controllers\TransitionController::class, 'reject'])->name('transitions.reject');
    });

    // Real-time bereavement notifications endpoint (for polling)
    Route::get('/pastor/bereavement-notifications', [PastorDashboardController::class, 'getBereavementNotifications'])->name('bereavement.notifications');
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
    Route::get('/leaders-members-ajax', [LeaderController::class, 'getMembersByCampus'])->name('leaders.members.ajax');
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
    Route::get('/test-leader-sms', function (Request $request) {
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
        Route::get('/test', function () {
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

        // Ahadi kwa Bwana (In-Kind Pledges)
        Route::get('ahadi-pledges/reports', [AhadiPledgeController::class, 'reports'])->name('ahadi-pledges.reports');
        Route::resource('ahadi-pledges', AhadiPledgeController::class);

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
        Route::get('/general-secretary', [App\Http\Controllers\ReportController::class, 'generalSecretaryReport'])->name('general-secretary');
        Route::get('/overview', [App\Http\Controllers\ReportController::class, 'overview'])->name('overview');
        Route::get('/member-giving', [App\Http\Controllers\ReportController::class, 'memberGiving'])->name('member-giving');
        Route::get('/department-giving', [App\Http\Controllers\ReportController::class, 'departmentGiving'])->name('department-giving');
        Route::get('/income-vs-expenditure', [App\Http\Controllers\ReportController::class, 'incomeVsExpenditure'])->name('income-vs-expenditure');
        Route::get('/budget-performance', [App\Http\Controllers\ReportController::class, 'budgetPerformance'])->name('budget-performance');
        Route::get('/offering-fund-breakdown', [App\Http\Controllers\ReportController::class, 'offeringFundBreakdown'])->name('offering-fund-breakdown');
        Route::get('/monthly-financial', [App\Http\Controllers\ReportController::class, 'monthlyFinancialReport'])->name('monthly-financial');
        Route::get('/special-offerings', [App\Http\Controllers\ReportController::class, 'specialOfferings'])->name('special-offerings');
        Route::get('/special-offerings/member/{member}', [App\Http\Controllers\ReportController::class, 'specialOfferingMemberCard'])->name('special-offerings.card');
        Route::get('/member-grid-report/{member}', [App\Http\Controllers\ReportController::class, 'memberGridReport'])->name('member-grid-report');
        Route::get('/weekly-financial', [App\Http\Controllers\ReportController::class, 'weeklyFinancialReport'])->name('weekly-financial');
        // Specific routes first (for backward compatibility)
        Route::get('/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('export.excel');
        // Dynamic route (handles /reports/export/pdf and /reports/export/excel)
        Route::get('/export/{format}', [App\Http\Controllers\ReportController::class, 'exportReport'])->name('export')->where('format', 'pdf|excel');
        Route::get('/member-receipt/{memberId}', [App\Http\Controllers\ReportController::class, 'generateMemberReceipt'])->name('member-receipt');
    });
});

// Social Welfare Reports (Pastor & Admin only)
Route::middleware(['auth', PreventBackHistory::class])->prefix('reports/welfare')->name('reports.welfare.')->group(function () {
    Route::get('/', [SocialWelfareReportController::class, 'index'])->name('index');
    Route::get('/orphans', [SocialWelfareReportController::class, 'orphanReport'])->name('orphans');
    Route::get('/disabilities', [SocialWelfareReportController::class, 'disabilityReport'])->name('disabilities');
    Route::get('/vulnerable', [SocialWelfareReportController::class, 'vulnerableReport'])->name('vulnerable');
    Route::get('/children-orphans', [SocialWelfareReportController::class, 'childrenOrphanReport'])->name('children.orphans');
    Route::get('/children-disabilities', [SocialWelfareReportController::class, 'childrenDisabilityReport'])->name('children.disabilities');
    Route::get('/children-vulnerable', [SocialWelfareReportController::class, 'childrenVulnerableReport'])->name('children.vulnerable');
    Route::get('/children-social', [SocialWelfareReportController::class, 'childrenSocialReport'])->name('children.social');
    Route::get('/unified', [SocialWelfareReportController::class, 'unifiedSocialReport'])->name('unified');
    Route::get('/orphans/export', [SocialWelfareReportController::class, 'exportOrphanReport'])->name('orphans.export');
    Route::get('/disabilities/export', [SocialWelfareReportController::class, 'exportDisabilityReport'])->name('disabilities.export');
    Route::get('/vulnerable/export', [SocialWelfareReportController::class, 'exportVulnerableReport'])->name('vulnerable.export');
    Route::get('/children-orphans/export', [SocialWelfareReportController::class, 'exportChildrenOrphanReport'])->name('children.orphans.export');
    Route::get('/children-disabilities/export', [SocialWelfareReportController::class, 'exportChildrenDisabilityReport'])->name('children.disabilities.export');
    Route::get('/children-vulnerable/export', [SocialWelfareReportController::class, 'exportChildrenVulnerableReport'])->name('children.vulnerable.export');
    Route::get('/children-social/export', [SocialWelfareReportController::class, 'exportChildrenSocialReport'])->name('children.social.export');
});

// Department Management - Accessible to Admin, Evangelist, etc.
Route::middleware(['auth'])->group(function () {
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::post('departments/{department}/assign', [App\Http\Controllers\DepartmentController::class, 'assignMember'])->name('departments.assign');
    Route::delete('departments/{department}/members/{member}', [App\Http\Controllers\DepartmentController::class, 'removeMember'])->name('departments.remove-member');
    Route::delete('departments/{department}/children/{child}', [App\Http\Controllers\DepartmentController::class, 'removeChild'])->name('departments.remove-child');
    Route::get('departments/{department}/check-eligibility/{member}', [App\Http\Controllers\DepartmentController::class, 'checkEligibility'])->name('departments.check-eligibility');
    Route::get('departments/{department}/suggest', [App\Http\Controllers\DepartmentController::class, 'suggest'])->name('departments.suggest');
});

// Member routes - Permission-based access control
Route::middleware(['auth', 'treasurer'])->group(function () {
    // Member creation - requires members.create permission
    Route::get('/members/add', [MemberController::class, 'create'])
        ->middleware('permission:members.create')
        ->name('members.add');

    Route::post('/members', [MemberController::class, 'store'])
        ->middleware('permission:members.create')
        ->name('members.store');

    // Test route for debugging
    Route::get('/test-member', function () {
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
    Route::get('/members/check-envelope', [MemberController::class, 'checkEnvelopeAvailability'])->name('members.check-envelope');

    // PUT and DELETE routes must come before GET routes with parameters
    Route::put('/members/{member}', [MemberController::class, 'update'])
        ->middleware('permission:members.edit')
        ->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])
        ->middleware('permission:members.delete')
        ->name('members.destroy');

    // Password reset - Admin only (must come before GET /members/{id} route)
    Route::post('/members/{id}/reset-password', [MemberController::class, 'resetPassword'])
        ->middleware('permission:members.edit')
        ->where('id', '[0-9]+')
        ->name('members.reset-password');

    // GET route with parameter should come last
    Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->middleware('permission:members.edit')->name('members.edit')->where('id', '[0-9]+');
    Route::post('/members/{member}/extend', [MemberController::class, 'extendMembership'])
        ->middleware('permission:members.edit')
        ->name('members.extend');
    Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show')->where('id', '[0-9]+');

    // Children routes
    Route::get('/children/{child}', [MemberController::class, 'showChild'])->name('children.show');
    Route::post('/children', [MemberController::class, 'storeChild'])->name('children.store');
    Route::get('/children/{child}/edit', [MemberController::class, 'editChild'])->name('children.edit');
    Route::put('/children/{child}', [MemberController::class, 'updateChild'])->middleware('permission:members.edit')->name('children.update');
    Route::delete('/children/{child}', [MemberController::class, 'destroyChild'])->middleware('permission:members.delete')->name('children.destroy');

    // Test route to check if member exists
    Route::get('/test-member/{id}', function ($id) {
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
    Route::get('/list-members', function () {
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
                'members' => $archivedMembers->map(function ($archived) {
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
    Route::get('/test-sms', function (Request $request) {
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
    Route::match(['get', 'post'], '/setup-sms', function (Request $request) {
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
    Route::get('/test-payment-sms', function (Request $request) {
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
    Route::get('/services/sunday/coordinators', [SundayServiceController::class, 'getCoordinators'])->name('services.sunday.coordinators');
    Route::get('/services/sunday/church-elders', [SundayServiceController::class, 'getChurchElders'])->name('services.sunday.church.elders');
    Route::get('/services/sunday/preachers', [SundayServiceController::class, 'getPreachers'])->name('services.sunday.preachers');
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
    // IMPORTANT: More specific routes must come BEFORE less specific routes
    // Biometric sync route - must be first to avoid conflicts
    Route::post('/attendance/biometric-sync', [AttendanceController::class, 'syncBiometricAttendance'])
        ->name('attendance.biometric.sync')
        ->middleware('auth'); // Explicitly add auth middleware

    Route::post('/attendance/trigger-notifications', [AttendanceController::class, 'triggerNotifications'])->name('attendance.trigger.notifications');
    Route::get('/attendance/member/{memberId}/history', [AttendanceController::class, 'memberHistory'])->name('attendance.member.history');
    Route::get('/attendance/service/{serviceType}/{serviceId}/report', [AttendanceController::class, 'serviceReport'])->name('attendance.service.report');
    // Primary route that works with php artisan serve (avoids conflict with public/attendance directory)
    Route::get('/stats/attendance', [AttendanceController::class, 'statistics'])->name('attendance.statistics');
    // Alternative routes for compatibility
    Route::get('/attendance-stats', [AttendanceController::class, 'statistics'])->name('attendance.statistics.alt');
    Route::get('/attendance/statistics', [AttendanceController::class, 'statistics'])->name('attendance.statistics.legacy');
    // Primary route that works with php artisan serve (avoids conflict with public/attendance directory)
    Route::get('/stats/missed-members', [AttendanceController::class, 'getMembersWithMissedAttendance'])->name('attendance.missed.members');
    // Alternative route for compatibility
    Route::get('/attendance/missed-members', [AttendanceController::class, 'getMembersWithMissedAttendance'])->name('attendance.missed.members.legacy');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Biometric Device Testing Routes
    Route::get('/biometric/test', [ZKTecoController::class, 'index'])->name('biometric.test');
    Route::post('/biometric/test-connection', [ZKTecoController::class, 'testConnection'])->name('biometric.test-connection');
    Route::post('/biometric/device-info', [ZKTecoController::class, 'getDeviceInfo'])->name('biometric.device-info');
    Route::post('/biometric/attendance', [ZKTecoController::class, 'getAttendance'])->name('biometric.attendance');
    Route::post('/biometric/users', [ZKTecoController::class, 'getUsers'])->name('biometric.users');

    // Biometric Member Registration Routes
    Route::post('/biometric/register-member', [ZKTecoController::class, 'registerMember'])->name('biometric.register-member');
    Route::post('/biometric/register-members-bulk', [ZKTecoController::class, 'registerMembersBulk'])->name('biometric.register-members-bulk');
    Route::get('/biometric/search-members', [ZKTecoController::class, 'searchMembers'])->name('biometric.search-members');

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
    Route::get('/settings/help', function () {
        return view('settings.help');
    })
        ->middleware('permission:settings.view')
        ->name('settings.help');
    Route::get('/settings/analytics', [SettingsController::class, 'analytics'])
        ->middleware('permission:settings.view')
        ->name('settings.analytics');
});



// Test route for biometric sync (temporary - for debugging)
Route::post('/test-biometric-sync', [AttendanceController::class, 'syncBiometricAttendance'])
    ->middleware('auth')
    ->name('test.biometric.sync');

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isEvangelismLeader()) {
            return redirect()->route('evangelism-leader.dashboard');
        } elseif ($user->isParishWorker()) {
            return redirect()->route('parish-worker.dashboard');
        } elseif ($user->isChurchElder()) {
            // Redirect to the first community they are assigned to, or a general dashboard
            $community = $user->elderCommunities()->first();
            if ($community) {
                return redirect()->route('church-elder.community.show', $community->id);
            }
            return redirect()->route('church-elder.dashboard');
        } elseif ($user->isPastor()) {
            return redirect()->route('dashboard.pastor');
        } elseif ($user->isTreasurer()) {
            return redirect()->route('finance.dashboard');
        } elseif ($user->isSecretary()) {
            return redirect()->route('dashboard.secretary');
        } elseif ($user->isMember()) {
            return redirect()->route('member.dashboard');
        }
    }
    return view('welcome');
})->name('landing_page');


// Redirect /dashboard based on user role
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    // 1. Super Administrator prioritized check
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    // If user has member_id, check for active leadership positions first
    if ($user->member_id) {
        // Check if user has any active leadership positions
        $hasActivePositions = false;
        if ($user->member) {
            $activePositions = $user->member->activeLeadershipPositions()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->get();
            $hasActivePositions = $activePositions->isNotEmpty();
        }

        // If no active positions, ALWAYS redirect to member dashboard (member portal)
        if (!$hasActivePositions) {
            return redirect()->route('member.dashboard');
        }

        // If they have active positions, check role-based dashboards
        if ($user->isEvangelismLeader()) {
            return redirect()->route('evangelism-leader.dashboard');
        } elseif ($user->isParishWorker()) {
            return redirect()->route('parish-worker.dashboard');
        } elseif ($user->isChurchElder()) {
            return redirect()->route('church-elder.dashboard');
        } elseif ($user->isPastor()) {
            return redirect()->route('dashboard.pastor');
        } elseif ($user->isTreasurer()) {
            return redirect()->route('finance.dashboard');
        } else {
            // If they have member_id but role doesn't match any leadership, go to member portal
            return redirect()->route('member.dashboard');
        }
    }

    // For users without member_id, check role-based dashboards
    if ($user->isPastor()) {
        return redirect()->route('dashboard.pastor');
    } elseif ($user->isTreasurer()) {
        return redirect()->route('finance.dashboard');
    } elseif ($user->isParishWorker()) {
        return redirect()->route('parish-worker.dashboard');
    } elseif ($user->isSecretary()) {
        return redirect()->route('dashboard.secretary');
    } else {
        // Default fallback
        return redirect()->route('member.dashboard');
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
        \Mail::raw('Test email from Waumini Link notification system!', function ($message) {
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
Route::get('/test-notifications', function () {
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
Route::get('/debug-notifications', function () {
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
Route::get('/test-approval-system', function () {
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
Route::get('/create-simple-test-data', function () {
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
Route::get('/create-all-tab-test-data', function () {
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
Route::get('/test-offering-approval', function () {
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
Route::get('/create-approval-test-data', function () {
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
    // Child to Member Transitions (Admin)
    Route::get('/transitions', [App\Http\Controllers\TransitionController::class, 'index'])->name('transitions.index');
    Route::get('/transitions/{transition}', [App\Http\Controllers\TransitionController::class, 'show'])->name('transitions.show');
    Route::post('/transitions/{transition}/approve', [App\Http\Controllers\TransitionController::class, 'approve'])->name('transitions.approve');
    Route::post('/transitions/{transition}/reject', [App\Http\Controllers\TransitionController::class, 'reject'])->name('transitions.reject');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Members outside main area
    Route::get('/members-outside-main-area', [MemberController::class, 'outsideMainArea'])->name('members.outside-main-area');
    Route::post('/members-outside-main-area/send-sms', [MemberController::class, 'sendSmsToOutsideMembers'])->name('members.outside-main-area.send-sms');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    Route::post('/logs/block-ip', [AdminController::class, 'blockIp'])->name('logs.block-ip');
    Route::post('/logs/unblock-ip', [AdminController::class, 'unblockIp'])->name('logs.unblock-ip');
    Route::get('/system-logs/{logId}/device-details', [AdminController::class, 'getDeviceDetails'])->name('logs.device-details');
    Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
    Route::post('/sessions/{sessionId}/revoke', [AdminController::class, 'revokeSession'])->name('sessions.revoke');
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
    Route::get('/system-monitor', [AdminController::class, 'systemMonitor'])->name('system-monitor');
    Route::get('/system-info', [AdminController::class, 'getSystemInfo'])->name('system-info');
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
});

// Announcements routes (for secretary/admin)
Route::middleware(['auth', PreventBackHistory::class, 'treasurer'])->group(function () {
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/send-sms', [AnnouncementController::class, 'sendSms'])->name('announcements.send-sms');
});

// Usharika Dashboard route (for main campus/Usharika admins)
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/usharika/dashboard', [UsharikaDashboardController::class, 'index'])->name('usharika.dashboard');
});

// Branch Dashboard route (for branch users)
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/branch/dashboard', [BranchDashboardController::class, 'index'])->name('branch.dashboard');
});

// Campus routes
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::resource('campuses', CampusController::class);
    Route::get('/campuses-ajax/get', [CampusController::class, 'getCampuses'])->name('campuses.ajax.get');
    Route::post('/campuses/{campus}/assign-evangelism-leader', [CampusController::class, 'assignEvangelismLeader'])->name('campuses.assign-evangelism-leader');

    // Nested community routes under campuses
    Route::prefix('campuses/{campus}')->name('campuses.')->group(function () {
        // JSON route must come BEFORE resource route to avoid route conflict
        Route::get('/communities/json', [CommunityController::class, 'getCommunitiesJson'])->name('communities.json');
        Route::get('/communities/ajax', [CommunityController::class, 'getCommunitiesJson'])->name('campuses.communities.ajax');
        Route::resource('communities', CommunityController::class);
        Route::post('/communities/{community}/assign-church-elder', [CommunityController::class, 'assignChurchElder'])->name('communities.assign-church-elder');
        Route::post('/communities/{community}/assign-members', [CommunityController::class, 'assignMembers'])->name('communities.assign-members');
    });
});

// Evangelism Leader routes
Route::middleware(['auth', PreventBackHistory::class])->prefix('evangelism-leader')->name('evangelism-leader.')->group(function () {
    Route::get('/dashboard', [EvangelismLeaderController::class, 'index'])->name('dashboard');
    Route::get('/register-member', [EvangelismLeaderController::class, 'showRegisterMember'])->name('register-member');

    // Reports routes
    Route::get('/reports', [EvangelismLeaderController::class, 'reportsIndex'])->name('reports.index');
    Route::get('/reports/create', [EvangelismLeaderController::class, 'createReport'])->name('reports.create');
    Route::post('/reports', [EvangelismLeaderController::class, 'storeReport'])->name('reports.store');
    Route::get('/reports/{report}', [EvangelismLeaderController::class, 'showReport'])->name('reports.show');

    // Tasks routes
    Route::get('/tasks', [EvangelismLeaderController::class, 'tasksIndex'])->name('tasks.index');
    Route::get('/tasks/create', [EvangelismLeaderController::class, 'createTask'])->name('tasks.create');
    Route::post('/tasks', [EvangelismLeaderController::class, 'storeTask'])->name('tasks.store');
    Route::get('/tasks/{task}', [EvangelismLeaderController::class, 'showTask'])->name('tasks.show');
    Route::put('/tasks/{task}/status', [EvangelismLeaderController::class, 'updateTaskStatus'])->name('tasks.update-status');

    // Issues routes
    Route::get('/issues', [EvangelismLeaderController::class, 'issuesIndex'])->name('issues.index');
    Route::get('/issues/create', [EvangelismLeaderController::class, 'createIssue'])->name('issues.create');
    Route::post('/issues', [EvangelismLeaderController::class, 'storeIssue'])->name('issues.store');
    Route::get('/issues/{issue}', [EvangelismLeaderController::class, 'showIssue'])->name('issues.show');

    // Church Elder Tasks & Issues routes (for Evangelism Leaders to view)
    Route::get('/church-elder-tasks', [EvangelismLeaderController::class, 'churchElderTasksIndex'])->name('church-elder-tasks.index');

    // Finance Management routes
    Route::get('/finance', [EvangelismLeaderController::class, 'financeIndex'])->name('finance.index');
    Route::post('/finance/offerings', [EvangelismLeaderController::class, 'storeOffering'])->name('finance.offerings.store');
    Route::get('/finance/offerings/{offering}', [EvangelismLeaderController::class, 'showOffering'])->name('finance.offerings.show');
    Route::get('/finance/offerings/{offering}/edit', [EvangelismLeaderController::class, 'editOffering'])->name('finance.offerings.edit');
    Route::put('/finance/offerings/{offering}', [EvangelismLeaderController::class, 'updateOffering'])->name('finance.offerings.update');
    Route::post('/finance/tithes', [EvangelismLeaderController::class, 'storeTithe'])->name('finance.tithes.store');
    Route::get('/finance/tithes/{tithe}', [EvangelismLeaderController::class, 'showTithe'])->name('finance.tithes.show');
    Route::get('/finance/tithes/{tithe}/edit', [EvangelismLeaderController::class, 'editTithe'])->name('finance.tithes.edit');
    Route::put('/finance/tithes/{tithe}', [EvangelismLeaderController::class, 'updateTithe'])->name('finance.tithes.update');
    Route::post('/finance/offerings/submit', [EvangelismLeaderController::class, 'submitOfferings'])->name('finance.offerings.submit');
    Route::post('/finance/tithes/submit', [EvangelismLeaderController::class, 'submitTithes'])->name('finance.tithes.submit');
    Route::get('/church-elder-tasks/{task}', [EvangelismLeaderController::class, 'showChurchElderTask'])->name('church-elder-tasks.show');
    Route::get('/church-elder-issues', [EvangelismLeaderController::class, 'churchElderIssuesIndex'])->name('church-elder-issues.index');
    Route::get('/church-elder-issues/{issue}', [EvangelismLeaderController::class, 'showChurchElderIssue'])->name('church-elder-issues.show');

    // Community Offerings routes
    Route::get('/offerings', [CommunityOfferingController::class, 'index'])->name('offerings.index');
    Route::get('/offerings/consolidated', [CommunityOfferingController::class, 'consolidated'])->name('offerings.consolidated');
    Route::post('/offerings/{offering}/confirm', [CommunityOfferingController::class, 'confirmByLeader'])->name('offerings.confirm');
    Route::post('/offerings/{offering}/reject', [CommunityOfferingController::class, 'rejectByLeader'])->name('offerings.reject');
    Route::post('/offerings/bulk-confirm', [CommunityOfferingController::class, 'bulkConfirmByLeader'])->name('offerings.bulk-confirm');
    Route::get('/offerings/{offering}', [CommunityOfferingController::class, 'show'])->name('offerings.show');

    // Branch Sunday Services routes
    Route::get('/branch-services', [EvangelismLeaderController::class, 'branchServicesIndex'])->name('branch-services.index');
    Route::get('/branch-services/create', [EvangelismLeaderController::class, 'branchServicesCreate'])->name('branch-services.create');
    Route::post('/branch-services', [EvangelismLeaderController::class, 'branchServicesStore'])->name('branch-services.store');
    Route::get('/branch-services/{service}', [EvangelismLeaderController::class, 'branchServicesShow'])->name('branch-services.show');
    Route::get('/branch-services/{service}/attendance', [EvangelismLeaderController::class, 'branchServiceAttendance'])->name('branch-services.attendance');
    Route::post('/branch-services/{service}/attendance', [EvangelismLeaderController::class, 'branchServiceRecordAttendance'])->name('branch-services.attendance.record');

    // Branch Offerings routes
    Route::get('/branch-offerings', [EvangelismLeaderController::class, 'branchOfferingsIndex'])->name('branch-offerings.index');
    Route::get('/branch-offerings/create', [EvangelismLeaderController::class, 'branchOfferingsCreate'])->name('branch-offerings.create');
    Route::post('/branch-offerings', [EvangelismLeaderController::class, 'branchOfferingsStore'])->name('branch-offerings.store');
    Route::get('/branch-offerings/{offering}', [EvangelismLeaderController::class, 'branchOfferingsShow'])->name('branch-offerings.show');

    // Bereavement Management routes
    Route::get('/bereavement', [EvangelismLeaderController::class, 'bereavementIndex'])->name('bereavement.index');
    Route::get('/bereavement/create', [EvangelismLeaderController::class, 'bereavementCreate'])->name('bereavement.create');
    Route::post('/bereavement', [EvangelismLeaderController::class, 'bereavementStore'])->name('bereavement.store');
    Route::get('/bereavement/{bereavement}', [EvangelismLeaderController::class, 'bereavementShow'])->name('bereavement.show');
    Route::get('/bereavement/{bereavement}/edit', [EvangelismLeaderController::class, 'bereavementEdit'])->name('bereavement.edit');
    Route::put('/bereavement/{bereavement}', [EvangelismLeaderController::class, 'bereavementUpdate'])->name('bereavement.update');
    Route::post('/bereavement/{bereavement}/close', [EvangelismLeaderController::class, 'bereavementClose'])->name('bereavement.close');
    Route::post('/bereavement/{bereavement}/contribution', [EvangelismLeaderController::class, 'bereavementRecordContribution'])->name('bereavement.record-contribution');

    // Baptism Applications routes
    Route::get('/baptism-applications', [BaptismApplicationController::class, 'index'])->name('baptism-applications.index');
    Route::get('/baptism-applications/create', [BaptismApplicationController::class, 'create'])->name('baptism-applications.create');
    Route::post('/baptism-applications', [BaptismApplicationController::class, 'store'])->name('baptism-applications.store');
    Route::get('/baptism-applications/{baptismApplication}', [BaptismApplicationController::class, 'show'])->name('baptism-applications.show');

    // Return to Fellowship Requests routes
    Route::get('/return-to-fellowship-requests', [ReturnToFellowshipRequestController::class, 'index'])->name('return-to-fellowship-requests.index');
    Route::get('/return-to-fellowship-requests/create', [ReturnToFellowshipRequestController::class, 'create'])->name('return-to-fellowship-requests.create');
    Route::post('/return-to-fellowship-requests', [ReturnToFellowshipRequestController::class, 'store'])->name('return-to-fellowship-requests.store');
    Route::get('/return-to-fellowship-requests/{returnToFellowshipRequest}', [ReturnToFellowshipRequestController::class, 'show'])->name('return-to-fellowship-requests.show');

    // Marriage Blessing Requests routes
    Route::get('/marriage-blessing-requests', [MarriageBlessingRequestController::class, 'index'])->name('marriage-blessing-requests.index');
    Route::get('/marriage-blessing-requests/create', [MarriageBlessingRequestController::class, 'create'])->name('marriage-blessing-requests.create');
    Route::post('/marriage-blessing-requests', [MarriageBlessingRequestController::class, 'store'])->name('marriage-blessing-requests.store');
    Route::get('/marriage-blessing-requests/{marriageBlessingRequest}', [MarriageBlessingRequestController::class, 'show'])->name('marriage-blessing-requests.show');

    // Church Wedding Requests routes
    Route::get('/church-wedding-requests', [ChurchWeddingRequestController::class, 'index'])->name('church-wedding-requests.index');
    Route::get('/church-wedding-requests/create', [ChurchWeddingRequestController::class, 'create'])->name('church-wedding-requests.create');
    Route::post('/church-wedding-requests', [ChurchWeddingRequestController::class, 'store'])->name('church-wedding-requests.store');
    Route::get('/church-wedding-requests/{churchWeddingRequest}', [ChurchWeddingRequestController::class, 'show'])->name('church-wedding-requests.show');
});

// Church Elder routes
Route::middleware(['auth', PreventBackHistory::class])->prefix('church-elder')->name('church-elder.')->group(function () {
    Route::get('/dashboard', [ChurchElderController::class, 'dashboard'])->name('dashboard');
    Route::get('/community/{community}', [ChurchElderController::class, 'showCommunity'])->name('community.show');

    // Services routes
    Route::get('/community/{community}/services', [ChurchElderController::class, 'services'])->name('services');
    Route::get('/community/{community}/services/create', [ChurchElderController::class, 'createService'])->name('services.create');
    Route::post('/community/{community}/services', [ChurchElderController::class, 'storeService'])->name('services.store');
    Route::get('/community/{community}/services/{service}/edit', [ChurchElderController::class, 'editService'])->name('services.edit');
    Route::put('/community/{community}/services/{service}', [ChurchElderController::class, 'updateService'])->name('services.update');
    Route::delete('/community/{community}/services/{service}', [ChurchElderController::class, 'deleteService'])->name('services.delete');

    // Attendance routes
    Route::get('/community/{community}/attendance', [ChurchElderController::class, 'attendance'])->name('attendance');
    Route::post('/community/{community}/attendance', [ChurchElderController::class, 'recordAttendance'])->name('attendance.record');
    Route::get('/community/{community}/attendance/view', [ChurchElderController::class, 'viewAttendance'])->name('attendance.view');

    // Offerings routes
    Route::get('/community/{community}/offerings', [ChurchElderController::class, 'offerings'])->name('offerings');
    Route::get('/community/{community}/offerings/all', [ChurchElderController::class, 'allOfferings'])->name('offerings.all');
    Route::post('/community/{community}/offerings', [ChurchElderController::class, 'storeOffering'])->name('offerings.store');

    // Community Offerings (Mid-week Service Offerings) routes
    Route::get('/community/{community}/community-offerings', [CommunityOfferingController::class, 'index'])->name('community-offerings.index');
    Route::get('/community/{community}/community-offerings/create', [CommunityOfferingController::class, 'create'])->name('community-offerings.create');
    Route::get('/community/{community}/services/{service}/create-offering', [CommunityOfferingController::class, 'createFromService'])->name('community-offerings.create-from-service');
    Route::post('/community-offerings', [CommunityOfferingController::class, 'store'])->name('community-offerings.store');
    Route::get('/community-offerings/{offering}', [CommunityOfferingController::class, 'show'])->name('community-offerings.show');

    // Tasks routes
    Route::get('/community/{community}/tasks', [ChurchElderController::class, 'tasksIndex'])->name('tasks.index');
    Route::get('/community/{community}/tasks/create', [ChurchElderController::class, 'createTask'])->name('tasks.create');
    Route::post('/community/{community}/tasks', [ChurchElderController::class, 'storeTask'])->name('tasks.store');
    Route::get('/community/{community}/tasks/{task}', [ChurchElderController::class, 'showTask'])->name('tasks.show');
    Route::put('/community/{community}/tasks/{task}/status', [ChurchElderController::class, 'updateTaskStatus'])->name('tasks.update-status');

    // Issues routes
    Route::get('/community/{community}/issues', [ChurchElderController::class, 'issuesIndex'])->name('issues.index');
    Route::get('/community/{community}/issues/create', [ChurchElderController::class, 'createIssue'])->name('issues.create');
    Route::post('/community/{community}/issues', [ChurchElderController::class, 'storeIssue'])->name('issues.store');
    Route::get('/community/{community}/issues/{issue}', [ChurchElderController::class, 'showIssue'])->name('issues.show');

    // Finance routes
    Route::get('/community/{community}/finance', [ChurchElderController::class, 'finance'])->name('finance.community');

    // Reports route
    Route::get('/community/{community}/reports', [ChurchElderController::class, 'reports'])->name('reports');
});

// Member routes
Route::middleware(['auth', PreventBackHistory::class])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/information', [MemberDashboardController::class, 'information'])->name('information');
    Route::get('/finance', [MemberDashboardController::class, 'finance'])->name('finance');
    Route::get('/announcements', [MemberDashboardController::class, 'announcements'])->name('announcements');
    Route::get('/leaders', [MemberDashboardController::class, 'leaders'])->name('leaders');
    Route::get('/settings', [MemberDashboardController::class, 'settings'])->name('settings');
    Route::post('/profile/update', [MemberDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [MemberDashboardController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [MemberDashboardController::class, 'updatePassword'])->name('password.update');
    Route::post('/notifications/{notification}/read', [MemberDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/pledges', [MemberDashboardController::class, 'storePledge'])->name('pledges.store');
});

// Serve storage files directly (bypasses symlink issues)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);

    // Security: Only allow files in public storage
    $realPath = realpath($filePath);
    $storagePath = realpath(storage_path('app/public'));

    // Check if file exists and is within storage/app/public directory
    if (!$realPath || strpos($realPath, $storagePath) !== 0) {
        abort(404);
    }

    // Check if file exists
    if (!file_exists($realPath) || !is_file($realPath)) {
        abort(404);
    }

    // Get MIME type
    $mimeType = mime_content_type($realPath);
    if (!$mimeType) {
        // Fallback MIME types for common image formats
        $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    // Set headers and return file
    return response()->file($realPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
    ]);
})->where('path', '.*')->name('storage.serve');

// Church Elder Offering Routes
Route::middleware(['auth', 'role:church_elder'])->prefix('church-elder')->name('church-elder.')->group(function () {
    Route::get('/offerings', [App\Http\Controllers\ChurchElderOfferingController::class, 'index'])->name('offerings.index');
    Route::get('/offerings/create', [App\Http\Controllers\ChurchElderOfferingController::class, 'create'])->name('offerings.create');
    Route::post('/offerings', [App\Http\Controllers\ChurchElderOfferingController::class, 'store'])->name('offerings.store');
});

// Sunday Offering Collection Routes (Elder + Treasurer)
Route::middleware(['auth'])->prefix('sunday-offering')->name('sunday-offering.')->group(function () {
    Route::get('/', [App\Http\Controllers\SundayOfferingController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\SundayOfferingController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\SundayOfferingController::class, 'store'])->name('store');
    Route::get('/{session}/entry', [App\Http\Controllers\SundayOfferingController::class, 'entry'])->name('entry');
    Route::post('/{session}/entry', [App\Http\Controllers\SundayOfferingController::class, 'updateEntry'])->name('update-entry');
    Route::get('/{session}', [App\Http\Controllers\SundayOfferingController::class, 'show'])->name('show');
    Route::post('/{session}/receive', [App\Http\Controllers\SundayOfferingController::class, 'receive'])->name('receive');
});

// Weekly Campus Summary Route (Secretary, Treasurer, Admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/finance/weekly-campus-summary', [App\Http\Controllers\WeeklyCampusSummaryController::class, 'index'])->name('finance.weekly-campus-summary');
});


// Finance: Mark expense as paid route
Route::put('/finance/expenses/{id}/pay', [App\Http\Controllers\FinanceController::class, 'payExpense'])->name('finance.expenses.pay');
