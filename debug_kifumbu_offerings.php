<?php
// Debug script to check Kifumbu offerings
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Campus;
use App\Models\OfferingCollectionSession;
use Carbon\Carbon;

echo "=== Checking Mtaa wa Kifumbu Offerings ===\n\n";

// Find Kifumbu campus
$campus = Campus::where('name', 'LIKE', '%Kifumbu%')->first();

if (!$campus) {
    echo "Campus 'Kifumbu' not found!\n";
    echo "Available campuses:\n";
    foreach (Campus::all() as $c) {
        echo "  - {$c->name} (ID: {$c->id})\n";
    }
    exit;
}

echo "Campus: {$campus->name} (ID: {$campus->id})\n\n";

// Get all offering sessions for this campus
$allSessions = OfferingCollectionSession::where('campus_id', $campus->id)->get();

echo "Total sessions for this campus: " . $allSessions->count() . "\n\n";

if ($allSessions->count() > 0) {
    echo "Session Details:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-12s %-12s %-15s %-15s\n", "Date", "Status", "Total Amount", "Created At");
    echo str_repeat("-", 80) . "\n";

    foreach ($allSessions as $session) {
        printf(
            "%-12s %-12s %-15s %-15s\n",
            $session->collection_date,
            $session->status,
            number_format($session->total_amount, 2),
            $session->created_at->format('Y-m-d H:i')
        );
    }

    echo str_repeat("-", 80) . "\n";
    echo "Total Amount (All Statuses): TZS " . number_format($allSessions->sum('total_amount'), 2) . "\n";
    echo "Total Amount (Submitted/Received): TZS " . number_format(
        $allSessions->whereIn('status', ['submitted', 'received'])->sum('total_amount'),
        2
    ) . "\n\n";
}

// Check current week range
$startDate = Carbon::now()->startOfWeek();
$endDate = Carbon::now()->endOfWeek();

echo "Current Week Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";

$weekSessions = OfferingCollectionSession::where('campus_id', $campus->id)
    ->whereBetween('collection_date', [$startDate, $endDate])
    ->get();

echo "Sessions in current week: " . $weekSessions->count() . "\n";
if ($weekSessions->count() > 0) {
    echo "Total for current week: TZS " . number_format($weekSessions->sum('total_amount'), 2) . "\n";
}
