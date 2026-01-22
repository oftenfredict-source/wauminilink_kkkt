<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class GenerateBiometricEnrollIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:generate-enroll-ids 
                            {--force : Force regeneration even if enroll ID exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate biometric enroll IDs (2-3 digits: 10-999) for all members who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating biometric enroll IDs for members...');
        $this->newLine();

        $force = $this->option('force');
        
        // Get members without enroll IDs
        $query = Member::query();
        if (!$force) {
            $query->whereNull('biometric_enroll_id')
                  ->orWhere('biometric_enroll_id', '');
        }
        
        $members = $query->get();
        $total = $members->count();

        if ($total === 0) {
            $this->info('All members already have biometric enroll IDs.');
            return 0;
        }

        $this->info("Found {$total} member(s) without enroll IDs.");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $generated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($members as $member) {
            try {
                // Skip if already has valid enroll ID and not forcing
                if (!$force && !empty($member->biometric_enroll_id)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Generate new enroll ID
                $enrollId = Member::generateBiometricEnrollId();
                $member->biometric_enroll_id = $enrollId;
                $member->save();

                $generated++;
                $bar->advance();

            } catch (\Exception $e) {
                $errors[] = [
                    'member_id' => $member->id,
                    'member_name' => $member->full_name,
                    'error' => $e->getMessage()
                ];
                $skipped++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Display results
        $this->info("✓ Generated: {$generated} enroll ID(s)");
        if ($skipped > 0) {
            $this->warn("⚠ Skipped: {$skipped} member(s)");
        }

        if (!empty($errors)) {
            $this->newLine();
            $this->error('Errors occurred:');
            foreach ($errors as $error) {
                $this->error("  - {$error['member_name']} (ID: {$error['member_id']}): {$error['error']}");
            }
        }

        $this->newLine();
        $this->info('Done!');

        return 0;
    }
}












