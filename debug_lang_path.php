<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Lang Path: " . lang_path() . "\n";
echo "Current Locale: " . app()->getLocale() . "\n";
echo "Fallback Locale: " . config('app.fallback_locale') . "\n";

$value_en = app('translator')->get('common.father', [], 'en');
echo "Force EN common.father: " . $value_en . "\n";

$value_sw = app('translator')->get('common.father', [], 'sw');
echo "Force SW common.father: " . $value_sw . "\n";

$value = trans('common.father');
echo "Current Locale common.father: " . $value . "\n";

if ($value === 'common.father') {
    echo "TRANSLATION FAILED.\n";
} else {
    echo "TRANSLATION SUCCESS.\n";
}

echo "Checking if path exists " . lang_path() . ": " . (is_dir(lang_path()) ? 'YES' : 'NO') . "\n";
echo "Checking if en/common.php exists: " . (file_exists(lang_path('en/common.php')) ? 'YES' : 'NO') . "\n";
