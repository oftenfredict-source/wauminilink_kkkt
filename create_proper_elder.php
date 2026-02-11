<?php
// Check database state and create necessary test data
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Member;
use App\Models\Leader;
use App\Models\Campus;
use App\Models\Community;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "Checking database state...\n\n";

// Check campuses
$campuses = Campus::all();
echo "Campuses found: " . $campuses->count() . "\n";
foreach ($campuses->take(3) as $campus) {
    echo "  - {$campus->name} (ID: {$campus->id}, Active: " . ($campus->is_active ? 'Yes' : 'No') . ")\n";
}

// Check communities
$communities = Community::all();
echo "\nCommunities found: " . $communities->count() . "\n";
foreach ($communities->take(3) as $community) {
    echo "  - {$community->name} (ID: {$community->id}, Campus: {$community->campus_id}, Active: " . ($community->is_active ? 'Yes' : 'No') . ")\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Creating test Church Elder...\n";
echo str_repeat("=", 50) . "\n\n";

try {
    DB::beginTransaction();

    // Get or create campus
    $campus = Campus::where('is_active', true)->first();
    if (!$campus) {
        echo "No active campus found. Creating test campus...\n";
        $campus = Campus::create([
            'name' => 'Test Campus',
            'location' => 'Test Location',
            'is_active' => true,
            'is_main_campus' => false,
        ]);
        echo "Created campus: {$campus->name}\n";
    } else {
        echo "Using existing campus: {$campus->name}\n";
    }

    // Get or create community
    $community = Community::where('campus_id', $campus->id)->where('is_active', true)->first();
    if (!$community) {
        echo "No active community found for this campus. Creating test community...\n";
        $community = Community::create([
            'name' => 'Test Community',
            'campus_id' => $campus->id,
            'is_active' => true,
        ]);
        echo "Created community: {$community->name}\n";
    } else {
        echo "Using existing community: {$community->name}\n";
    }

    // Check if elder test user already exists
    $existingUser = User::where('email', 'elder@test.com')->first();
    if ($existingUser) {
        echo "\nUser elder@test.com already exists. Deleting and recreating...\n";
        if ($existingUser->member_id) {
            Leader::where('member_id', $existingUser->member_id)->delete();
            Member::where('id', $existingUser->member_id)->delete();
        }
        $existingUser->delete();
    }

    // Generate unique member_id
    $year = date('Y');
    $quarter = 'Q' . ceil(date('n') / 3);
    $random = strtoupper(substr(md5(uniqid()), 0, 2));
    $memberId = $year . $quarter . $random . '-WL';

    // Create member
    $member = Member::create([
        'member_id' => $memberId,
        'first_name' => 'Test',
        'middle_name' => 'Church',
        'last_name' => 'Elder',
        'full_name' => 'Test Church Elder',
        'phone_number' => '+255700000001',
        'email' => 'elder@test.com',
        'gender' => 'male',
        'date_of_birth' => '1980-01-01',
        'marital_status' => 'married',
        'region' => 'Dar es Salaam',
        'district' => 'Ilala',
        'ward' => 'Buguruni',
        'campus_id' => $campus->id,
        'community_id' => $community->id,
        'is_active' => true,
        'membership_status' => 'active',
        'registration_date' => now(),
    ]);

    echo "\n✓ Created Member: {$member->first_name} {$member->last_name} (ID: {$member->id})\n";

    // Create user
    $user = User::create([
        'name' => 'Test Church Elder',
        'email' => 'elder@test.com',
        'password' => Hash::make('password'),
        'role' => 'member',
        'member_id' => $member->id,
        'campus_id' => $campus->id,
    ]);

    echo "✓ Created User: {$user->email} (ID: {$user->id})\n";

    // Create leadership position
    $leader = Leader::create([
        'member_id' => $member->id,
        'position' => 'elder',
        'campus_id' => $campus->id,
        'community_id' => $community->id,
        'start_date' => now()->subYear(),
        'appointment_date' => now()->subYear(),
        'end_date' => null,
        'is_active' => true,
    ]);

    echo "✓ Created Leadership Position: Elder\n";

    DB::commit();

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ SUCCESS!\n";
    echo str_repeat("=", 50) . "\n";
    echo "Email: elder@test.com\n";
    echo "Password: password\n";
    echo "Campus: {$campus->name}\n";
    echo "Community: {$community->name}\n";
    echo "Position: Elder\n";
    echo "\nYou can now log in as a Church Elder!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
