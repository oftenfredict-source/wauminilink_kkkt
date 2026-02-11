<?php

use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Member;
use App\Models\ServiceAttendance;
use App\Models\SpecialEvent;
use App\Models\Celebration;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

function report($model, $dateCol, $approvalCol = null) {
    if (!class_exists($model)) { echo "!! $model not found\n"; return; }
    
    $name = class_basename($model);
    $total = $model::count();
    
    echo "[$name] Total: $total\n";
    
    if ($total > 0) {
        $min = $model::min($dateCol);
        $max = $model::max($dateCol);
        echo "   Range: $min to $max\n";
        
        if ($approvalCol) {
            $approved = $model::where($approvalCol, 'approved')->count();
            $pending = $model::where($approvalCol, '!=', 'approved')->count();
            echo "   Status: Approved=$approved, Pending/Other=$pending\n";
            
            // Show a sample of pending if any
            if ($pending > 0) {
                $sample = $model::where($approvalCol, '!=', 'approved')->select($approvalCol)->first();
                echo "   Sample Status Value: '" . ($sample->$approvalCol ?? 'NULL') . "'\n";
            }
        }
    }
    echo "--------------------------------------------------\n";
}

echo "\n=== ANALYTICS DATA DEBUG ===\n";
report(Tithe::class, 'tithe_date', 'approval_status');
report(Offering::class, 'offering_date', 'approval_status');
report(Donation::class, 'donation_date', 'approval_status');
report(Expense::class, 'expense_date', 'approval_status');
report(ServiceAttendance::class, 'attended_at');
report(SpecialEvent::class, 'event_date');
report(Celebration::class, 'celebration_date');
echo "=== END RESULTS ===\n";
