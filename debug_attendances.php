<?php

use App\Models\ServiceAttendance;
use App\Models\Member;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Checking ServiceAttendance records...\n";
$attendances = ServiceAttendance::whereNotNull('member_id')->limit(10)->get();

if ($attendances->isEmpty()) {
    echo "No ServiceAttendance records found with member_id.\n";
} else {
    foreach($attendances as $a) {
        echo "Attendance ID: {$a->id}, Member ID: {$a->member_id} ";
        if ($a->member) {
            echo "- Member Found: {$a->member->full_name} (ID: {$a->member->id})\n";
        } else {
            echo "- Member NOT Found (Orphaned Record)\n";
            // Check if member exists directly
            $memberExists = \Illuminate\Support\Facades\DB::table('members')->where('id', $a->member_id)->exists();
            echo "  Direct DB Check: " . ($memberExists ? "Exists in DB" : "Does NOT exist in DB") . "\n";
        }
    }
}
