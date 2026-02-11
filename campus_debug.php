<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- CAMPUS LEADERS ---\n";
$campuses = \App\Models\Campus::all();
foreach ($campuses as $c) {
    echo "Campus: {$c->name} (ID: {$c->id})\n";
    echo "  Evangelism Leader ID: " . ($c->evangelism_leader_id ?? 'NONE') . "\n";
    if ($c->evangelism_leader_id) {
        $u = \App\Models\User::find($c->evangelism_leader_id);
        if ($u) {
            echo "  Leader Name: {$u->name} (User ID: {$u->id})\n";
        } else {
            echo "  Leader User NOT FOUND for ID {$c->evangelism_leader_id}\n";
        }
    }
}
echo "--- OFTEN FRED LEADERSHIP ---\n";
$m38 = \App\Models\Member::find(38);
if ($m38) {
    echo "Member: {$m38->full_name}\n";
    foreach ($m38->activeLeadershipPositions as $p) {
        echo "  Position: {$p->position} (Start: {$p->start_date}, End: " . ($p->end_date ?? 'PRESENT') . ")\n";
    }
}
echo "--- END ---\n";
