<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreatePastorUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-pastor {--name=} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a pastor user account with login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Create Pastor User Account ===');
        $this->newLine();

        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter Pastor Full Name', 'Pastor Sample');
        $email = $this->option('email') ?: $this->ask('Enter Email Address (Username)', 'pastor@waumini.com');
        $password = $this->option('password') ?: $this->secret('Enter Password (leave empty for default: password123)') ?: 'password123';

        // Validate email
        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
        ], [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Create the pastor user
        try {
            $pastor = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'pastor',
                'can_approve_finances' => true,
            ]);

            $this->newLine();
            $this->info('✅ Pastor user created successfully!');
            $this->newLine();
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $pastor->name],
                    ['Email (Username)', $pastor->email],
                    ['Password', $password],
                    ['Role', $pastor->role],
                    ['Can Approve Finances', $pastor->can_approve_finances ? 'Yes' : 'No'],
                ]
            );
            $this->newLine();
            $this->warn('⚠️  Please save these credentials securely!');
            $this->info('Login URL: ' . url('/login'));

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create pastor user: ' . $e->getMessage());
            return 1;
        }
    }
}
