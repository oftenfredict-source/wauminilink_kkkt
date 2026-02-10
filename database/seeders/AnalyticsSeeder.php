<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\ServiceAttendance;
use App\Models\SundayService; // Key import
use App\Models\SpecialEvent;
use App\Models\Celebration;
use App\Models\Member;
use App\Models\Child;
use App\Models\Campus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AnalyticsSeeder extends Seeder
{
    public function run()
    {
        // 1. Get Valid IDs
        $memberIds = Member::pluck('id')->toArray();
        if (empty($memberIds)) {
            $this->seedMembers();
            $memberIds = Member::pluck('id')->toArray();
        }

        $childIds = Child::pluck('id')->toArray();
        $campusId = Campus::first()->id ?? 1;
        $userId = User::first()->id ?? 1;

        // 2. Seed Data
        $years = [2025, 2026];
        
        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                if ($year == Carbon::now()->year && $month > Carbon::now()->month) continue;

                $daysInMonth = Carbon::create($year, $month)->daysInMonth;

                // --- TITHES ---
                for ($i = 0; $i < rand(5, 15); $i++) {
                    Tithe::create([
                        'member_id' => $memberIds[array_rand($memberIds)],
                        'campus_id' => $campusId,
                        'evangelism_leader_id' => $userId,
                        'amount' => rand(10000, 100000),
                        'tithe_date' => Carbon::create($year, $month, rand(1, $daysInMonth)),
                        'payment_method' => 'Cash',
                        'reference_number' => 'REF-' . Str::random(8),
                        'notes' => 'Seeded tithe',
                        'approval_status' => 'approved',
                        'approved_by' => $userId,
                        'is_verified' => true
                    ]);
                }

                // --- SUNDAY SERVICES & ATTENDANCE & OFFERINGS ---
                $date = Carbon::create($year, $month, 1);
                while ($date->month == $month) {
                    if ($date->isSunday()) {
                        
                        /*
                         * Skipping Complex Service/Attendance linking to ensure stability.
                         * Focus is on Financials.
                         */

                        // Seed Offering (Independent)
                        Offering::create([
                            'campus_id' => $campusId,
                            'evangelism_leader_id' => $userId,
                            // 'service_id' => null, 
                            'amount' => rand(50000, 200000),
                            'offering_date' => $date->copy(),
                            'offering_type' => 'Sadaka ya Kawaida',
                            'service_type' => 'Sunday Service',
                            'payment_method' => 'Cash',
                            'reference_number' => 'OFF-' . Str::random(8),
                            'notes' => 'Seeded offering',
                            'approval_status' => 'approved',
                            'approved_by' => $userId,
                            'recorded_by' => $userId,
                            'is_verified' => true
                        ]);
                    }
                    $date->addDay();
                }

                // --- DONATIONS ---
                if (rand(0, 1)) {
                    Donation::create([
                        'member_id' => $memberIds[array_rand($memberIds)],
                        'amount' => rand(20000, 500000),
                        'donation_date' => Carbon::create($year, $month, rand(1, $daysInMonth)),
                        'donation_type' => 'Specific',
                        'purpose' => 'Development',
                        'payment_method' => 'Cash',
                        'reference_number' => 'DON-' . Str::random(8),
                        'notes' => 'Seeded donation',
                        'approval_status' => 'approved',
                        'approved_by' => $userId,
                        'is_verified' => true
                    ]);
                }

                // --- EXPENSES ---
                Expense::create([
                    'expense_category' => 'Utilities',
                    'expense_name' => 'Electricity ' . $month . '/' . $year, // Required field
                    'amount' => rand(10000, 50000),
                    'expense_date' => Carbon::create($year, $month, rand(1, 10)),
                    'payment_method' => 'Cash',
                    'reference_number' => 'EXP-' . Str::random(8),
                    'description' => 'Utilities bill',
                    'status' => 'approved',
                    'approval_status' => 'approved',
                    'approved_by' => $userId,
                    'recorded_by' => $userId,
                    'notes' => 'Seeded expense'
                ]);
            }
        }

        // 4. Seed Events
        $this->seedEvents();
    }

    private function seedMembers()
    {
        for ($i = 0; $i < 10; $i++) {
            Member::create([
                'first_name' => 'Member',
                'last_name' => 'Test' . $i,
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'phone_number' => '07000000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'date_of_birth' => Carbon::now()->subYears(rand(20, 60)),
                'marital_status' => 'Single',
                'occupation' => 'Trader',
                'residence' => 'Local',
                'membership_type' => 'Communicant',
                'member_type' => 'Adult',
                'status' => 'active'
            ]);
        }
    }

    private function seedEvents()
    {
        SpecialEvent::create([
            'title' => 'Youth Conference',
            'event_date' => Carbon::now()->subMonth(),
            'description' => 'Annual youth gathering'
        ]);

        SpecialEvent::create([
            'title' => 'VBS 2026',
            'event_date' => Carbon::now()->addMonths(2),
            'description' => 'Vacation Bible School'
        ]);

        Celebration::create([
            'title' => 'Church Anniversary',
            'celebration_date' => Carbon::now()->addMonths(5),
            'type' => 'Anniversary'
        ]);
    }
}
