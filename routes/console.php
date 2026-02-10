<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule attendance notifications to run every Monday at 9:00 AM
Schedule::command('attendance:check-notifications')
    ->weeklyOn(1, '9:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule pledge reminders to run daily at 10:00 AM
// The command checks if last reminder was sent more than 2 days ago
Schedule::command('pledges:send-reminders')
    ->dailyAt('10:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule bereavement event closure to run daily at 11:00 PM
// Automatically closes events that have passed their contribution deadline
Schedule::command('bereavement:close-expired')
    ->dailyAt('23:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule promise guest notifications to run daily at 9:00 AM
// Sends SMS notifications to promise guests 1 day before their promised service date
Schedule::command('promise-guests:send-notifications')
    ->dailyAt('9:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule temporary membership expiry check to run daily at 8:00 AM
// Checks for temporary memberships expiring within 30 days or already expired
Schedule::command('membership:check-expiry')
    ->dailyAt('8:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule child to member transition eligibility check to run daily at 7:00 AM
// Checks for children who are 18+ and church members, creates transition requests
Schedule::command('children:check-transition-eligibility')
    ->dailyAt('7:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->runInBackground();
