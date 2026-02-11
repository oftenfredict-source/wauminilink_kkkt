<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Department;
use App\Models\Child;

$dept = Department::first();
if (!$dept) {
    echo "No department found.\n";
    exit;
}

$child = Child::first();
if (!$child) {
    echo "No child found.\n";
    exit;
}

echo "Testing assignment of Child ID {$child->id} to Department ID {$dept->id} ({$dept->name})...\n";

try {
    // Check if already assigned
    if ($dept->children()->where('department_member.child_id', $child->id)->exists()) {
        echo "Child already assigned. Detaching first for test...\n";
        $dept->children()->detach($child->id);
    }

    $dept->children()->attach($child->id, ['status' => 'active']);
    echo "Assignment successful!\n";

    $isAssigned = $dept->children()->where('department_member.child_id', $child->id)->exists();
    echo "Verification: " . ($isAssigned ? "Confirmed" : "Failed") . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
