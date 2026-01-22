<?php

/**
 * Script to grant 'members.create' permission to roles
 * 
 * Usage: php grant_member_permission.php [role]
 * 
 * If no role is specified, it will grant to: pastor, secretary
 * 
 * Valid roles: admin, pastor, secretary, treasurer
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

// Get role from command line argument or use defaults
$rolesToGrant = [];
if (isset($argv[1])) {
    $role = strtolower($argv[1]);
    if (!in_array($role, ['admin', 'pastor', 'secretary', 'treasurer'])) {
        echo "Error: Invalid role. Valid roles are: admin, pastor, secretary, treasurer\n";
        exit(1);
    }
    $rolesToGrant = [$role];
} else {
    // Default: grant to pastor and secretary
    $rolesToGrant = ['pastor', 'secretary'];
    echo "No role specified. Granting permission to: " . implode(', ', $rolesToGrant) . "\n";
}

// Get the members.create permission
$permission = Permission::where('slug', 'members.create')->first();

if (!$permission) {
    echo "Error: Permission 'members.create' not found in database.\n";
    echo "Please run the permissions seeder first: php artisan db:seed --class=AdminUserSeeder\n";
    exit(1);
}

echo "\nGranting 'members.create' permission to roles: " . implode(', ', $rolesToGrant) . "\n";
echo "Permission ID: {$permission->id}\n";
echo "Permission Name: {$permission->name}\n\n";

// Grant permission to each role
foreach ($rolesToGrant as $role) {
    // Check if permission already exists
    $exists = DB::table('role_permissions')
        ->where('role', $role)
        ->where('permission_id', $permission->id)
        ->exists();
    
    if ($exists) {
        echo "✓ Role '{$role}' already has 'members.create' permission\n";
    } else {
        // Grant the permission
        DB::table('role_permissions')->insert([
            'role' => $role,
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Successfully granted 'members.create' permission to role '{$role}'\n";
    }
}

echo "\nDone! Users with the following roles can now add members:\n";
foreach ($rolesToGrant as $role) {
    echo "  - " . ucfirst($role) . "\n";
}

echo "\nNote: Admin role automatically has all permissions, so no need to grant it explicitly.\n";
echo "Note: Users may need to log out and log back in for changes to take effect.\n";














