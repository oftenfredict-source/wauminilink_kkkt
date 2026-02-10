<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Child;
use App\Models\ChildToMemberTransition;
use Carbon\Carbon;

class CheckEligibleChildrenForTransition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'children:check-transition-eligibility';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for children who are 18+ and church members, and create transition requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for eligible children for transition...');

        // Find children who are:
        // 1. 18 years or older
        // 2. Marked as church members (is_church_member = true)
        // 3. Don't already have a pending or approved transition
        $eligibleChildren = Child::where('is_church_member', true)
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')
            ->whereDoesntHave('transitions', function($query) {
                $query->whereIn('status', ['pending', 'approved', 'completed']);
            })
            ->get();

        $count = 0;
        foreach ($eligibleChildren as $child) {
            $age = $child->getAge();
            
            if ($age >= 18) {
                // Create a transition request
                ChildToMemberTransition::create([
                    'child_id' => $child->id,
                    'status' => 'pending',
                ]);
                $count++;
                $this->info("Created transition request for: {$child->full_name} (Age: {$age})");
            }
        }

        $this->info("Created {$count} transition request(s).");
        return 0;
    }
}
