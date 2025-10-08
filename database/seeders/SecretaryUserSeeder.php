<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SecretaryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create secretary user
        User::create([
            'name' => 'Secretary User',
            'email' => 'secretary@example.com',
            'password' => Hash::make('12345@@'),
            'role' => 'secretary',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Secretary user created successfully!');
        $this->command->info('Email: secretary@example.com');
        $this->command->info('Password: 12345@@');
    }
}
