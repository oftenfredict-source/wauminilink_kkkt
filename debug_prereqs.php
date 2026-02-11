<?php
use App\Models\Member;
use App\Models\User;
use App\Models\Campus;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "\n--- PREREQUISITE CHECK ---\n";
echo "Members Count: " . Member::count() . "\n";
echo "Users Count: " . User::count() . "\n";
// Check if Campus model exists, might be 'App\Models\Campus' or similar
if (class_exists(Campus::class)) {
    echo "Campuses Count: " . Campus::count() . "\n";
} else {
    echo "Campus Model not found.\n";
}

if (User::count() > 0) {
    echo "First User ID: " . User::first()->id . "\n";
}
if (class_exists(Campus::class) && Campus::count() > 0) {
    echo "First Campus ID: " . Campus::first()->id . "\n";
}
echo "--------------------------\n";

file_put_contents('prereqs.log', "Members: " . Member::count() . ", Users: " . User::count());
if (class_exists(Campus::class)) {
    file_put_contents('prereqs.log', ", Campuses: " . Campus::count(), FILE_APPEND);
}
