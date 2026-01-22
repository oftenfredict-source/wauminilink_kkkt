<?php

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Find an evangelism leader user
// We'll look for one who has the 'evangelism_leader' role or is linked to a leader with that position
$user = User::where('role', 'evangelism_leader')->first();

if (!$user) {
    // Try finding by email or member_id if you know a specific one, or just look for a leader
    echo "No user with 'evangelism_leader' role found directly.\n";
    
    // Let's find a user who IS an evangelism leader via relationship logic
    $users = User::all();
    foreach ($users as $u) {
        if ($u->isEvangelismLeader()) {
            $user = $u;
            echo "Found user via isEvangelismLeader(): {$u->email} (ID: {$u->id}, Role Column: '{$u->role}')\n";
            break;
        }
    }
} else {
    echo "Found user with 'evangelism_leader' role: {$user->email} (ID: {$user->id})\n";
}

if ($user) {
    echo "\nChecking permissions for user ID: {$user->id}\n";
    echo "User Role Column: '{$user->role}'\n";
    
    $hasPermission = $user->hasPermission('members.create');
    echo "hasPermission('members.create'): " . ($hasPermission ? 'YES' : 'NO') . "\n";
    
    // Check raw DB permissions for this role
    $rolePermissions = DB::table('role_permissions')
        ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
        ->where('role_permissions.role', $user->role)
        ->pluck('permissions.slug')
        ->toArray();
        
    echo "Permissions found in DB for role '{$user->role}':\n";
    print_r($rolePermissions);
    
    // Check if 'evangelism_leader' role specifically has it
    $elPermissions = DB::table('role_permissions')
        ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
        ->where('role_permissions.role', 'evangelism_leader')
        ->pluck('permissions.slug')
        ->toArray();
        
    echo "Permissions found in DB for 'evangelism_leader' role explicitly:\n";
    print_r($elPermissions);
    
} else {
    echo "Could not find any Evangelism Leader user to test.\n";
}
