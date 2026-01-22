<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Administrator user
        $admin = User::firstOrCreate(
            ['email' => 'administrator@waumini.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@2024!'),
                'role' => 'admin',
                'can_approve_finances' => true,
                'phone_number' => '+255000000000',
            ]
        );

        $this->command->info('Administrator account created:');
        $this->command->info('Email: administrator@waumini.com');
        $this->command->info('Password: Admin@2024!');
        $this->command->warn('Please change the password after first login!');

        // Create permissions
        $this->createPermissions();
        
        // Assign all permissions to admin role
        $this->assignPermissionsToRole('admin');
        
        // Assign default permissions to other roles
        $this->assignDefaultPermissions();
    }

    /**
     * Create all system permissions
     */
    protected function createPermissions(): void
    {
        $permissions = [
            // Admin permissions
            ['name' => 'View Admin Dashboard', 'slug' => 'admin.dashboard', 'category' => 'admin', 'description' => 'Access to admin dashboard'],
            ['name' => 'View Activity Logs', 'slug' => 'admin.logs.view', 'category' => 'admin', 'description' => 'View system activity logs'],
            ['name' => 'View User Sessions', 'slug' => 'admin.sessions.view', 'category' => 'admin', 'description' => 'View active user sessions'],
            ['name' => 'Manage Users', 'slug' => 'admin.users.manage', 'category' => 'admin', 'description' => 'Create, edit, and delete users'],
            ['name' => 'Manage Roles', 'slug' => 'admin.roles.manage', 'category' => 'admin', 'description' => 'Manage roles and permissions'],
            
            // Member permissions
            ['name' => 'View Members', 'slug' => 'members.view', 'category' => 'members', 'description' => 'View member list'],
            ['name' => 'Create Members', 'slug' => 'members.create', 'category' => 'members', 'description' => 'Add new members'],
            ['name' => 'Edit Members', 'slug' => 'members.edit', 'category' => 'members', 'description' => 'Edit member information'],
            ['name' => 'Delete Members', 'slug' => 'members.delete', 'category' => 'members', 'description' => 'Delete members'],
            ['name' => 'View Member Details', 'slug' => 'members.details', 'category' => 'members', 'description' => 'View detailed member information'],
            
            // Leader permissions
            ['name' => 'View Leaders', 'slug' => 'leaders.view', 'category' => 'leaders', 'description' => 'View leader list'],
            ['name' => 'Create Leaders', 'slug' => 'leaders.create', 'category' => 'leaders', 'description' => 'Add new leaders'],
            ['name' => 'Edit Leaders', 'slug' => 'leaders.edit', 'category' => 'leaders', 'description' => 'Edit leader information'],
            ['name' => 'Delete Leaders', 'slug' => 'leaders.delete', 'category' => 'leaders', 'description' => 'Delete leaders'],
            ['name' => 'Manage Leadership', 'slug' => 'leaders.manage', 'category' => 'leaders', 'description' => 'Full leadership management'],
            
            // Finance permissions
            ['name' => 'View Finance Dashboard', 'slug' => 'finance.dashboard', 'category' => 'finance', 'description' => 'Access finance dashboard'],
            ['name' => 'View Financial Records', 'slug' => 'finance.view', 'category' => 'finance', 'description' => 'View all financial records'],
            ['name' => 'Create Financial Records', 'slug' => 'finance.create', 'category' => 'finance', 'description' => 'Create tithes, offerings, donations, expenses'],
            ['name' => 'Edit Financial Records', 'slug' => 'finance.edit', 'category' => 'finance', 'description' => 'Edit financial records'],
            ['name' => 'Approve Financial Records', 'slug' => 'finance.approve', 'category' => 'finance', 'description' => 'Approve financial transactions'],
            ['name' => 'Manage Budgets', 'slug' => 'finance.budgets', 'category' => 'finance', 'description' => 'Create and manage budgets'],
            ['name' => 'View Financial Reports', 'slug' => 'finance.reports', 'category' => 'finance', 'description' => 'View financial reports'],
            
            // Service permissions
            ['name' => 'View Services', 'slug' => 'services.view', 'category' => 'services', 'description' => 'View Sunday services'],
            ['name' => 'Create Services', 'slug' => 'services.create', 'category' => 'services', 'description' => 'Create Sunday services'],
            ['name' => 'Edit Services', 'slug' => 'services.edit', 'category' => 'services', 'description' => 'Edit Sunday services'],
            ['name' => 'Manage Attendance', 'slug' => 'services.attendance', 'category' => 'services', 'description' => 'Manage service attendance'],
            
            // Settings permissions
            ['name' => 'View Settings', 'slug' => 'settings.view', 'category' => 'settings', 'description' => 'View system settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'category' => 'settings', 'description' => 'Edit system settings'],
            
            // Reports permissions
            ['name' => 'View Reports', 'slug' => 'reports.view', 'category' => 'reports', 'description' => 'View all reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'category' => 'reports', 'description' => 'Export reports to CSV/PDF'],
            
            // Analytics permissions
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'category' => 'analytics', 'description' => 'View system analytics'],
            
            // Evangelism permissions
            ['name' => 'Bereavement Management', 'slug' => 'evangelism.bereavement.manage', 'category' => 'evangelism', 'description' => 'Manage bereavement events and contributions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $this->command->info('Permissions created successfully');
    }

    /**
     * Assign permissions to a role
     */
    protected function assignPermissionsToRole(string $role): void
    {
        $permissions = Permission::all();
        
        foreach ($permissions as $permission) {
            DB::table('role_permissions')->firstOrCreate([
                'role' => $role,
                'permission_id' => $permission->id,
            ]);
        }

        $this->command->info("All permissions assigned to {$role} role");
    }

    /**
     * Assign default permissions to each role based on their typical needs
     */
    protected function assignDefaultPermissions(): void
    {
        // Pastor - Full access except admin functions
        $pastorPermissions = [
            'members.view', 'members.create', 'members.edit', 'members.details',
            'leaders.view', 'leaders.create', 'leaders.edit', 'leaders.manage',
            'finance.dashboard', 'finance.view', 'finance.approve', 'finance.reports',
            'services.view', 'services.create', 'services.edit', 'services.attendance',
            'settings.view', 'settings.edit',
            'reports.view', 'reports.export',
            'analytics.view',
        ];
        $this->assignSpecificPermissionsToRole('pastor', $pastorPermissions);

        // Secretary - Member and service management
        $secretaryPermissions = [
            'members.view', 'members.create', 'members.edit', 'members.details',
            'leaders.view', 'leaders.create', 'leaders.edit', 'leaders.manage',
            'finance.view', 'finance.create', 'finance.edit', 'finance.reports',
            'services.view', 'services.create', 'services.edit', 'services.attendance',
            'settings.view',
            'reports.view', 'reports.export',
            'analytics.view',
        ];
        $this->assignSpecificPermissionsToRole('secretary', $secretaryPermissions);

        // Treasurer - Finance focused
        $treasurerPermissions = [
            'members.view', 'members.details',
            'finance.dashboard', 'finance.view', 'finance.create', 'finance.edit', 'finance.reports',
            'reports.view', 'reports.export',
        ];
        $this->assignSpecificPermissionsToRole('treasurer', $treasurerPermissions);

        $this->command->info('Default permissions assigned to all roles');
    }

    /**
     * Assign specific permissions to a role
     */
    protected function assignSpecificPermissionsToRole(string $role, array $permissionSlugs): void
    {
        $permissions = Permission::whereIn('slug', $permissionSlugs)->get();
        
        foreach ($permissions as $permission) {
            DB::table('role_permissions')->firstOrCreate([
                'role' => $role,
                'permission_id' => $permission->id,
            ]);
        }

        $this->command->info("Default permissions assigned to {$role} role");
    }
}

