<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Leader;
use App\Models\Community;
use App\Models\Campus;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ChurchElderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a campus (use first available campus or create a default one)
        $campus = Campus::where('is_active', true)->first();
        if (!$campus) {
            $campus = Campus::create([
                'name' => 'Main Campus',
                'code' => 'MAIN',
                'is_main_campus' => true,
                'is_active' => true,
            ]);
        }

        // Create sample church elders
        $elders = [
            [
                'name' => 'Elder John Mwangi',
                'email' => 'elder.john@waumini.com',
                'password' => 'elder123',
                'phone' => '+255712345678',
                'member_id_format' => 'ELDER001',
            ],
            [
                'name' => 'Elder Mary Kamau',
                'email' => 'elder.mary@waumini.com',
                'password' => 'elder123',
                'phone' => '+255712345679',
                'member_id_format' => 'ELDER002',
            ],
            [
                'name' => 'Elder Peter Ochieng',
                'email' => 'elder.peter@waumini.com',
                'password' => 'elder123',
                'phone' => '+255712345680',
                'member_id_format' => 'ELDER003',
            ],
        ];

        $createdElders = [];

        foreach ($elders as $elderData) {
            // Create or find member
            $member = Member::where('email', $elderData['email'])
                ->orWhere('phone_number', $elderData['phone'])
                ->first();

            if (!$member) {
                // Create new member
                $member = Member::create([
                    'full_name' => $elderData['name'],
                    'email' => $elderData['email'],
                    'phone_number' => $elderData['phone'],
                    'member_id' => Member::generateMemberId(),
                    'campus_id' => $campus->id,
                    'membership_type' => 'permanent',
                    'member_type' => 'independent',
                    'gender' => str_contains($elderData['name'], 'Mary') ? 'female' : 'male',
                    'date_of_birth' => Carbon::now()->subYears(45)->format('Y-m-d'),
                    'marital_status' => 'married',
                    'region' => 'Dar es Salaam',
                    'district' => 'Kinondoni',
                    'ward' => 'Mikocheni',
                ]);
            }

            // Create leader position (elder)
            $leader = Leader::where('member_id', $member->id)
                ->where('position', 'elder')
                ->where('is_active', true)
                ->first();

            if (!$leader) {
                $leader = Leader::create([
                    'member_id' => $member->id,
                    'campus_id' => $campus->id,
                    'position' => 'elder',
                    'appointment_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                    'is_active' => true,
                    'appointed_by' => 'System Administrator',
                ]);
            }

            // Create or update user account
            $user = User::where('email', $elderData['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $elderData['name'],
                    'email' => $elderData['email'],
                    'password' => Hash::make($elderData['password']),
                    'role' => 'member', // Elders use member role but have elder leadership position
                    'member_id' => $member->id,
                    'campus_id' => $campus->id,
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update existing user to link to member
                $user->update([
                    'member_id' => $member->id,
                    'campus_id' => $campus->id,
                ]);
            }

            $createdElders[] = [
                'name' => $elderData['name'],
                'email' => $elderData['email'],
                'password' => $elderData['password'],
                'member_id' => $member->member_id,
                'leader_id' => $leader->id,
            ];
        }

        // Assign elders to communities (if communities exist)
        $communities = Community::where('is_active', true)->get();
        if ($communities->count() > 0) {
            foreach ($createdElders as $index => $elder) {
                if (isset($communities[$index])) {
                    $community = $communities[$index];
                    $leader = Leader::find($elder['leader_id']);
                    if ($leader) {
                        $community->update([
                            'church_elder_id' => $leader->id,
                        ]);
                        $this->command->info("Assigned {$elder['name']} to community: {$community->name}");
                    }
                }
            }
        }

        // Display created elders
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Church Elders Created Successfully!');
        $this->command->info('========================================');
        $this->command->info('');
        
        foreach ($createdElders as $elder) {
            $this->command->info("Name: {$elder['name']}");
            $this->command->info("Email: {$elder['email']}");
            $this->command->info("Password: {$elder['password']}");
            $this->command->info("Member ID: {$elder['member_id']}");
            $this->command->info('---');
        }
        
        $this->command->info('');
        $this->command->info('Note: All elders use the same password: elder123');
        $this->command->info('Please change passwords after first login for security.');
        $this->command->info('');
    }
}
