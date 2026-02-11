<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;

$m54 = Member::where('envelope_number', '54')->first();
$m55 = Member::where('envelope_number', '55')->first();

echo "Envelope 54: " . ($m54 ? "ID: {$m54->id} Name: {$m54->full_name}" : "Not assigned") . "\n";
echo "Envelope 55: " . ($m55 ? "ID: {$m55->id} Name: {$m55->full_name}" : "Not assigned") . "\n";

// Also search for Helena and Ally's names and their envelopes again just to be 100% sure
$helena = Member::where('full_name', 'LIKE', '%Helena Shija%')->get();
foreach ($helena as $h) {
    echo "HELENA: ID {$h->id}, Name: {$h->full_name}, Env: [{$h->envelope_number}]\n";
}

$ally = Member::where('full_name', 'LIKE', '%Ally Ally%')->get();
foreach ($ally as $a) {
    echo "ALLY: ID {$a->id}, Name: {$a->full_name}, Env: [{$a->envelope_number}]\n";
}
