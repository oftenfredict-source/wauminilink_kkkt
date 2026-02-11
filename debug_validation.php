<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Validator;

$data = [
    'member_id' => '',
    'child_id' => '1', // Assuming child ID 1 exists
];

$rules = [
    'member_id' => 'required_without:child_id|exists:members,id',
    'child_id' => 'required_without:member_id|exists:children,id',
];

$validator = Validator::make($data, $rules);

if ($validator->fails()) {
    echo "Validation Failed!\n";
    print_r($validator->errors()->toArray());
} else {
    echo "Validation Passed!\n";
}
