<?php

/**
 * Script to update church name from AIC Moshi to KKKT Ushirika wa Longuo
 * Run this script from the project root: php update_church_name.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

echo "Updating church name in database...\n";

// Update the church_name setting
$setting = SystemSetting::updateOrCreate(
    ['key' => 'church_name'],
    [
        'value' => 'KKKT Ushirika wa Longuo',
        'type' => 'string',
        'category' => 'general',
        'group' => 'basic',
        'description' => 'Name of the church',
        'is_editable' => true,
        'is_public' => false,
        'validation_rules' => ['required', 'string', 'max:255'],
        'sort_order' => 0
    ]
);

// Clear the cache for this setting
Cache::forget('setting.church_name');
Cache::forget('settings.category.general');

echo "✓ Church name updated successfully!\n";
echo "  Old value: " . ($setting->getOriginal('value') ?? 'Not set') . "\n";
echo "  New value: " . $setting->value . "\n";
echo "\n";
echo "Please clear your browser cache or do a hard refresh (Ctrl+F5) to see the changes.\n";
echo "You may also need to run: php artisan cache:clear\n";




