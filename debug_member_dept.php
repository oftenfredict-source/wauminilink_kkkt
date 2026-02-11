<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;
use App\Models\Department;

ob_start();

$m = Member::where('full_name', 'like', '%Ester%Shayo%')->first();
if (!$m) {
    echo "Member Ester Shayo not found\n";
    exit;
}

echo "Member: {$m->full_name} (ID: {$m->id})\n";
echo "Campus ID: " . ($m->campus_id ?? 'None') . " (Name: " . ($m->campus ? $m->campus->name : 'N/A') . ")\n";
echo "Departments:\n";
foreach ($m->departments as $dept) {
    echo "- {$dept->name} (ID: {$dept->id})\n";
}

$dept1 = Department::find(1);
if ($dept1) {
    echo "\nDepartment (ID: 1): {$dept1->name}\n";
    $membersCount = $dept1->members()->count();
    echo "Total members in this department: {$membersCount}\n";
    
    // Check if Ester is in this department
    $assignment = \DB::table('department_member')->where('department_id', 1)->where('member_id', $m->id)->first();
    if ($assignment) {
        echo "Assignment found: Department 1, Member {$m->id}\n";
        echo "Status: " . ($assignment->status ?? 'N/A') . "\n";
        echo "Created At: " . ($assignment->created_at ?? 'N/A') . "\n";
    } else {
        echo "Assignment NOT FOUND in DB table department_member\n";
    }
}

file_put_contents('debug_results.txt', ob_get_clean());
echo "Results saved to debug_results.txt\n";
