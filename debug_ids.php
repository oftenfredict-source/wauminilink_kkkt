<?php
use App\Models\Member;
use App\Models\User;
use App\Models\Campus;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "User IDs: " . implode(', ', User::pluck('id')->toArray()) . "\n";
if (class_exists(Campus::class)) {
    echo "Campus IDs: " . implode(', ', Campus::pluck('id')->toArray()) . "\n";
}
if (class_exists(Member::class)) {
    echo "Member IDs: " . implode(', ', Member::pluck('id')->toArray()) . "\n";
}
