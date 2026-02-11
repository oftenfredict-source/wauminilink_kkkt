<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CommunityOfferingItem;
use Illuminate\Support\Facades\DB;

$eloquentCount = CommunityOfferingItem::whereNull('member_id')->count();
$dbCount = DB::table('community_offering_items')->whereNull('member_id')->count();

echo "Eloquent Null Count: $eloquentCount\n";
echo "DB Raw Null Count: $dbCount\n";
