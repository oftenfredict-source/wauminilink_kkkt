<?php
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Member;
use App\Models\Campus;
use App\Models\User;
use App\Models\ServiceAttendance; // Added 
use App\Models\SundayService; // Added
use App\Models\SpecialEvent;
use App\Models\Celebration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

file_put_contents('seed_debug.log', "--- STARTING SEED DEBUG ---\n");

function tryCreate($name, $callback) {
    file_put_contents('seed_debug.log', "Attempting to create $name... ", FILE_APPEND);
    try {
        DB::beginTransaction();
        $callback();
        DB::rollBack();
        file_put_contents('seed_debug.log', "SUCCESS\n", FILE_APPEND);
    } catch (\Exception $e) {
        file_put_contents('seed_debug.log', "FAILED\nError: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

$memberId = Member::first()->id ?? null;
$campusId = Campus::first()->id ?? 1;
$userId = User::first()->id ?? 1;

if (!$memberId) {
    file_put_contents('seed_debug.log', "NO MEMBERS FOUND. ABORTING.\n", FILE_APPEND);
    exit;
}

// 8. Sunday Service (Seed FIRST so ID is available)
$serviceId = 1; // Default
tryCreate('SundayService', function() use ($memberId, $campusId, $userId, &$serviceId) {
    $service = SundayService::create([
        'service_date' => Carbon::now(),
        'service_type' => 'Sunday Service',
        'start_time' => '09:00',
        'end_time' => '11:00',
        'venue' => 'Main Sanctuary',
        'attendance_count' => 0,
        'status' => 'completed',
        'campus_id' => $campusId,
        'evangelism_leader_id' => $userId,
        'coordinator_id' => $memberId,
        'church_elder_id' => $memberId
    ]);
    $serviceId = $service->id;
});

// 5. Service Attendance
tryCreate('ServiceAttendance', function() use ($memberId, $userId, $serviceId) {
    ServiceAttendance::create([
        'service_type' => 'sunday_service',
        'service_id' => $serviceId, // Use valid ID
        'attended_at' => Carbon::now(),
        'member_id' => $memberId,
        'recorded_by' => $userId,
        // Removed temperature, has_mask
    ]);
});

// 6. Special Event
tryCreate('SpecialEvent', function() {
    SpecialEvent::create([
        'title' => 'Debug Event',
        'event_date' => Carbon::now(),
        'description' => 'Debug Description'
    ]);
});

// 7. Celebration
tryCreate('Celebration', function() {
    Celebration::create([
        'title' => 'Debug Celebration',
        'celebration_date' => Carbon::now(),
        'type' => 'Anniversary'
    ]);
});

file_put_contents('seed_debug.log', "--- END SEED DEBUG ---\n", FILE_APPEND);
