<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CelebrationController;
use App\Http\Middleware\PreventBackHistory;


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\MemberController;
use App\Http\Controllers\SundayServiceController;
use App\Http\Controllers\SpecialEventController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;

// Auth routes with PreventBackHistory middleware
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/secretary/dashboard', [DashboardController::class, 'index'])->name('dashboard.secretary');
    Route::get('/members/view', [MemberController::class, 'view'])->name('members.view');
    // Sunday services UI route
    Route::get('/services/sunday', [SundayServiceController::class, 'index'])->name('services.sunday.index');
    // Special events UI route
    Route::get('/special-events', [SpecialEventController::class, 'index'])->name('special.events.index');
    // Celebrations UI route
    Route::get('/celebrations', [CelebrationController::class, 'index'])->name('celebrations.index');
});

// Member routes
Route::middleware(['auth'])->group(function () {
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    // Test route for debugging
    Route::get('/test-member', function() {
        try {
            $member = new App\Models\Member();
            return response()->json(['status' => 'success', 'message' => 'Member model works', 'fillable' => $member->getFillable()]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    });
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/add', function () { return view('members.add-members'); })->name('members.add');
    Route::get('/members/next-id', [MemberController::class, 'nextId'])->name('members.next_id');
    Route::get('/members/export/csv', [MemberController::class, 'exportCsv'])->name('members.export.csv');
    Route::get('/members/{id}', [MemberController::class, 'show'])->name('members.show')->where('id', '[0-9]+');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    // Archive delete with reason (controller to handle)
    Route::delete('/members/{member}/archive', [MemberController::class, 'archive'])->name('members.archive');

    // Sunday services routes
    Route::post('/services/sunday', [SundayServiceController::class, 'store'])->name('services.sunday.store');
    Route::get('/services/sunday/{sundayService}', [SundayServiceController::class, 'show'])->name('services.sunday.show');
    Route::put('/services/sunday/{sundayService}', [SundayServiceController::class, 'update'])->name('services.sunday.update');
    Route::delete('/services/sunday/{sundayService}', [SundayServiceController::class, 'destroy'])->name('services.sunday.destroy');
    Route::get('/services/sunday-export/csv', [SundayServiceController::class, 'exportCsv'])->name('services.sunday.export.csv');

    // Special events routes
    Route::post('/special-events', [SpecialEventController::class, 'store'])->name('special.events.store');
    Route::get('/special-events/{specialEvent}', [SpecialEventController::class, 'show'])->name('special.events.show');
    Route::put('/special-events/{specialEvent}', [SpecialEventController::class, 'update'])->name('special.events.update');
    Route::delete('/special-events/{specialEvent}', [SpecialEventController::class, 'destroy'])->name('special.events.destroy');

    // Celebrations routes
    Route::post('/celebrations', [CelebrationController::class, 'store'])->name('celebrations.store');
    Route::get('/celebrations/{celebration}', [CelebrationController::class, 'show'])->name('celebrations.show');
    Route::put('/celebrations/{celebration}', [CelebrationController::class, 'update'])->name('celebrations.update');
    Route::delete('/celebrations/{celebration}', [CelebrationController::class, 'destroy'])->name('celebrations.destroy');
    Route::get('/celebrations-export/csv', [CelebrationController::class, 'exportCsv'])->name('celebrations.export.csv');
});

// Settings routes
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});



Route::get('/', function () {
    return view('welcome');
})->name('landing_page');



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
