<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Notifications\DailyJobDigest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendDailyJobDigest extends Command
{
    protected $signature = 'jobs:send-daily-digest';
    protected $description = 'Send daily job digest to subscribed users';

    public function handle()
    {
        $this->info('Starting daily job digest...');

        // Get jobs posted in the last 24 hours
        $yesterday = Carbon::now()->subDay();

        // Get all countries that have new jobs
        $countriesWithJobs = Post::where('created_at', '>=', $yesterday)
            ->whereNull('archived_at')
            ->whereNotNull('country_code')
            ->distinct()
            ->pluck('country_code');

        if ($countriesWithJobs->isEmpty()) {
            $this->info('No new jobs found in the last 24 hours.');
            return 0;
        }

        $this->info("Found new jobs in " . $countriesWithJobs->count() . " countries");

        $totalEmailsSent = 0;

        foreach ($countriesWithJobs as $countryCode) {
            // Get new jobs for this country
            $newJobs = Post::where('country_code', $countryCode)
                ->where('created_at', '>=', $yesterday)
                ->whereNull('archived_at')
                ->with(['city', 'country', 'category'])
                ->orderBy('created_at', 'desc')
                ->limit(20) // Max 20 jobs per email
                ->get();

            if ($newJobs->isEmpty()) {
                continue;
            }

            // Get users who want notifications for this country
            // IMPORTANT: Only sends to users who have NOT unsubscribed
            // Rate limit: Only 100 users per country per day to avoid overwhelming email providers
            $users = User::where('country_code', $countryCode)
                ->where('job_notification_enabled', true) // Respects unsubscribe preference (false = unsubscribed)
                ->where('is_admin', 0)
                ->whereNotNull('email')
                ->whereNotNull('email_verified_at')
                ->whereDoesntHave('jobPreference') // Only users without preferences (others get match notifications)
                ->inRandomOrder() // Randomize to be fair
                ->take(100) // LIMIT TO 100 PER COUNTRY PER DAY
                ->get();

            if ($users->isEmpty()) {
                continue;
            }

            $this->info("Sending digest to {$users->count()} users in {$countryCode} ({$newJobs->count()} jobs)");

            // Send in small batches to avoid overwhelming the mail server
            foreach ($users->chunk(10) as $userChunk) {
                try {
                    Notification::send($userChunk, new DailyJobDigest($newJobs, $countryCode));
                    $totalEmailsSent += $userChunk->count();

                    // Sleep 2 seconds between batches to rate limit
                    sleep(2);
                } catch (\Exception $e) {
                    $this->error("Failed to send batch: " . $e->getMessage());
                }
            }
        }

        $this->info("Daily job digest complete! Sent {$totalEmailsSent} emails.");

        return 0;
    }
}
