<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

ob_start();
$indexes = \DB::select('SHOW INDEX FROM department_member');
foreach ($indexes as $idx) {
    echo "Index: {$idx->Key_name}, Column: {$idx->Column_name}, Unique: " . (!$idx->Non_unique ? 'Yes' : 'No') . "\n";
}
file_put_contents('index_output.txt', ob_get_clean());
echo "Results saved to index_output.txt\n";
