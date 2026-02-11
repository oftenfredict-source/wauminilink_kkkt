<?php
use App\Models\Tithe;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

try {
    file_put_contents('error.log', "Attempting Tithe Create...\n");
    DB::beginTransaction();
    Tithe::create([
        'member_id' => 1,
        'campus_id' => 1,
        'evangelism_leader_id' => 1,
        'amount' => 50000,
        'tithe_date' => Carbon::now(),
        'payment_method' => 'Cash',
        'approval_status' => 'approved',
        'approved_by' => 1,
        'is_verified' => true
    ]); // Missing reference_number, recorded_by
    DB::rollBack();
    file_put_contents('error.log', "SUCCESS\n", FILE_APPEND);
} catch (\Exception $e) {
    file_put_contents('error.log', "FAILED: " . $e->getMessage() . "\n", FILE_APPEND);
}
