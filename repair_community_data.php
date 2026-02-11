<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOffering;
use App\Models\CommunityOfferingItem;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

echo "--- DATA REPAIR START ---\n";

DB::transaction(function() {
    // 1. Unify Offering Types in CommunityOffering
    // Mapping slugs/shorthand to pretty labels used in reports
    $typeMapping = [
        'sadaka_umoja' => 'Sadaka ya Umoja',
        'sadaka_jengo' => 'Sadaka ya Jengo',
        'umoja' => 'Sadaka ya Umoja',
        'jengo' => 'Sadaka ya Jengo'
    ];

    foreach ($typeMapping as $old => $new) {
        $count = CommunityOffering::where('offering_type', $old)->update(['offering_type' => $new]);
        if ($count > 0) {
            echo "Updated $count offerings from '$old' to '$new'\n";
        }
    }

    // 2. Link Orphaned Items (member_id is NULL or 0)
    echo "Searching for orphaned items...\n";
    $orphanedItems = DB::table('community_offering_items')
        ->where(function($q) {
            $q->whereNull('member_id')
              ->orWhere('member_id', 0)
              ->orWhere('member_id', '');
        })->get();
    
    echo "Found " . $orphanedItems->count() . " orphaned items.\n";

    foreach ($orphanedItems as $item) {
        if (empty($item->envelope_number)) {
            echo "Item {$item->id}: Skipping (no envelope number)\n";
            continue;
        }

        // Find member by envelope number (trimmed)
        $envelope = trim($item->envelope_number);
        // Try exact match first
        $member = DB::table('members')->where('envelope_number', $envelope)->first();
        
        // If not found, try padded if it's numeric
        if (!$member && is_numeric($envelope)) {
            $padded = str_pad($envelope, 2, '0', STR_PAD_LEFT);
            if ($padded !== $envelope) {
                $member = DB::table('members')->where('envelope_number', $padded)->first();
            }
        }

        if ($member) {
            DB::table('community_offering_items')
                ->where('id', $item->id)
                ->update(['member_id' => $member->id]);
            echo "Item {$item->id} (Env: $envelope): Linked to member '{$member->full_name}' (ID: {$member->id})\n";
        } else {
            echo "Item {$item->id} (Env: $envelope): Could NOT find member\n";
        }
    }
});

echo "\n--- DATA REPAIR COMPLETE ---\n";
