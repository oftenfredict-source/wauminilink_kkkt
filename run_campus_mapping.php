<?php

use App\Models\Community;
use App\Models\Campus;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$output = "=== Campus and Community Mapping ===\n\n";

$campuses = Campus::all();
foreach ($campuses as $campus) {
    $output .= "Campus: ID: {$campus->id}, Name: {$campus->name}\n";
    $communities = Community::where('campus_id', $campus->id)->get();
    foreach ($communities as $c) {
        $output .= "  Comm: ID: {$c->id}, Name: {$c->name}\n";
    }
    
    $leaders = User::whereHas('member.activeLeadershipPositions', function($q) {
        $q->where('position', 'evangelism_leader');
    })->where(function($q) use ($campus) {
        $q->where('campus_id', $campus->id)
          ->orWhereHas('member', function($mq) use ($campus) {
              $mq->where('campus_id', $campus->id);
          });
    })->get();
    
    $output .= "  Leaders for this Campus:\n";
    foreach ($leaders as $l) {
        $output .= "    - ID: {$l->id}, Name: {$l->name}\n";
    }
    $output .= "-------------------\n";
}

file_put_contents('campus_mapping.txt', $output);
echo "Campus mapping written to campus_mapping.txt\n";
