<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$campuses = \App\Models\Campus::all();
foreach ($campuses as $c) {
    echo "ID: {$c->id}, Name: {$c->name}, Leader ID: " . ($c->evangelism_leader_id ?? 'None') . "\n";
    if ($c->evangelism_leader_id) {
        $u = \App\Models\User::find($c->evangelism_leader_id);
        if ($u) {
            echo "  Leader User: {$u->name} (Email: {$u->email})\n";
        }
    }
}
