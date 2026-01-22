<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Child;

class GenerateBiometricEnrollIdsForChildren extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'children:generate-enroll-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate biometric enroll IDs (2-3 digits: 10-999) for teenager children (13-17) who should attend main service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating biometric enroll IDs for teenager children (13-17)...');
        
        // Only get children who should attend main service (teenagers 13-17)
        $children = Child::whereNull('biometric_enroll_id')
            ->whereNotNull('date_of_birth')
            ->get()
            ->filter(function ($child) {
                return $child->shouldAttendMainService(); // Only teenagers (13-17)
            });
        
        $count = 0;
        $skipped = 0;
        
        foreach ($children as $child) {
            try {
                // Check if child is a teenager
                if (!$child->shouldAttendMainService()) {
                    $skipped++;
                    $this->warn("Skipped {$child->full_name} (age: {$child->getAge()}) - not a teenager (13-17)");
                    continue;
                }
                
                $child->biometric_enroll_id = Child::generateBiometricEnrollId();
                $child->save();
                $this->info("Generated ID {$child->biometric_enroll_id} for teenager: {$child->full_name} (age: {$child->getAge()})");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to generate ID for child {$child->full_name} (ID: {$child->id}): " . $e->getMessage());
            }
        }
        
        $this->info("Generated IDs for {$count} teenager children.");
        if ($skipped > 0) {
            $this->info("Skipped {$skipped} children (not teenagers or missing date of birth).");
        }
        
        return Command::SUCCESS;
    }
}
