<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\User;
use App\Notifications\TemporaryMembershipExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTemporaryMembershipExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for temporary memberships expiring soon or expired and send notifications to Pastor/Admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking temporary membership expiry...');
        
        $now = Carbon::now();
        $thirtyDaysFromNow = $now->copy()->addDays(30);
        
        // Find temporary members expiring within 30 days (but not expired yet)
        $expiringMembers = Member::where('membership_type', 'temporary')
            ->where('membership_status', 'active')
            ->whereNotNull('membership_end_date')
            ->whereBetween('membership_end_date', [$now, $thirtyDaysFromNow])
            ->get();
        
        // Find expired temporary members
        $expiredMembers = Member::where('membership_type', 'temporary')
            ->where('membership_status', 'active')
            ->whereNotNull('membership_end_date')
            ->where('membership_end_date', '<', $now)
            ->get();
        
        $this->info("Found {$expiringMembers->count()} members expiring soon");
        $this->info("Found {$expiredMembers->count()} expired members");
        
        // Get Pastor and Admin users
        $pastorsAndAdmins = User::whereIn('role', ['pastor', 'admin'])
            ->get();
        
        if ($pastorsAndAdmins->isEmpty()) {
            $this->warn('No Pastor or Admin users found. Notifications will not be sent.');
            return 0;
        }
        
        $notificationsSent = 0;
        
        // Send notifications for expiring members
        foreach ($expiringMembers as $member) {
            $daysUntilExpiry = $now->diffInDays($member->membership_end_date, false);
            
            // Only send notification if within 30 days
            if ($daysUntilExpiry <= 30 && $daysUntilExpiry >= 0) {
                foreach ($pastorsAndAdmins as $user) {
                    try {
                        $user->notify(new TemporaryMembershipExpiryNotification($member, 'expiring', $daysUntilExpiry));
                        $notificationsSent++;
                        Log::info('Temporary membership expiring notification sent', [
                            'member_id' => $member->id,
                            'user_id' => $user->id,
                            'days_until_expiry' => $daysUntilExpiry
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send expiring membership notification', [
                            'member_id' => $member->id,
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        // Send notifications for expired members
        foreach ($expiredMembers as $member) {
            foreach ($pastorsAndAdmins as $user) {
                try {
                    $user->notify(new TemporaryMembershipExpiryNotification($member, 'expired'));
                    $notificationsSent++;
                    Log::info('Temporary membership expired notification sent', [
                        'member_id' => $member->id,
                        'user_id' => $user->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send expired membership notification', [
                        'member_id' => $member->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        $this->info("Sent {$notificationsSent} notifications");
        
        return 0;
    }
}
