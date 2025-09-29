<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\PreventBackHistory;


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\MemberController;
use App\Http\Controllers\SundayServiceController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;

// Auth routes with PreventBackHistory middleware
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/secretary/dashboard', [DashboardController::class, 'index'])->name('dashboard.secretary');
    Route::get('/members/view', [MemberController::class, 'view'])->name('members.view');
    // Sunday services UI route
    Route::get('/services/sunday', [SundayServiceController::class, 'index'])->name('services.sunday.index');
});

// Member routes
Route::middleware(['auth'])->group(function () {
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/add', function () { return view('members.add-members'); })->name('members.add');
    Route::get('/members/next-id', [MemberController::class, 'nextId'])->name('members.next_id');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    // Archive delete with reason (controller to handle)
    Route::delete('/members/{member}/archive', [MemberController::class, 'archive'])->name('members.archive');
    Route::get('/members-export/csv', [MemberController::class, 'exportCsv'])->name('members.export.csv');

    // Sunday services routes
    Route::post('/services/sunday', [SundayServiceController::class, 'store'])->name('services.sunday.store');
    Route::get('/services/sunday/{sundayService}', [SundayServiceController::class, 'show'])->name('services.sunday.show');
    Route::put('/services/sunday/{sundayService}', [SundayServiceController::class, 'update'])->name('services.sunday.update');
    Route::delete('/services/sunday/{sundayService}', [SundayServiceController::class, 'destroy'])->name('services.sunday.destroy');
    Route::get('/services/sunday-export/csv', [SundayServiceController::class, 'exportCsv'])->name('services.sunday.export.csv');
});

// Settings routes
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});



Route::get('/', function () {
    return view('welcome');
})->name('landing_page');
