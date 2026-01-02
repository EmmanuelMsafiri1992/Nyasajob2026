<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFeedStagedItem;
use App\Models\JobFeedLog;
use Carbon\Carbon;

class CleanupOldFeedData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rss:cleanup
        {--staged-days=30 : Days to keep non-imported staged items}
        {--log-days=90 : Days to keep fetch logs}
        {--expire-days=7 : Mark items older than this as expired}
        {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old RSS feed data and logs to maintain database performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning up old RSS feed data...');
        $this->newLine();

        $stagedDays = (int) $this->option('staged-days');
        $logDays = (int) $this->option('log-days');
        $expireDays = (int) $this->option('expire-days');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN - No actual changes will be made');
            $this->newLine();
        }

        // 1. Mark expired pending items (older than 7 days)
        $expiredQuery = JobFeedStagedItem::where('status', 'pending')
            ->where('published_at', '<', Carbon::now()->subDays($expireDays));

        $expiredCount = $expiredQuery->count();

        if (!$dryRun && $expiredCount > 0) {
            $expiredQuery->update(['status' => 'expired']);
        }

        $this->info("Expired items (>{$expireDays} days old): <fg=yellow>{$expiredCount}</>");

        // 2. Delete old staged items (except imported ones)
        $stagedQuery = JobFeedStagedItem::whereIn('status', ['pending', 'rejected', 'expired'])
            ->where('created_at', '<', Carbon::now()->subDays($stagedDays));

        $stagedCount = $stagedQuery->count();

        if (!$dryRun && $stagedCount > 0) {
            $stagedQuery->delete();
        }

        $this->info("Deleted staged items (>{$stagedDays} days, non-imported): <fg=cyan>{$stagedCount}</>");

        // 3. Delete old logs
        $logQuery = JobFeedLog::where('created_at', '<', Carbon::now()->subDays($logDays));

        $logCount = $logQuery->count();

        if (!$dryRun && $logCount > 0) {
            $logQuery->delete();
        }

        $this->info("Deleted log entries (>{$logDays} days): <fg=cyan>{$logCount}</>");

        // 4. Show summary stats
        $this->newLine();
        $this->info('Current database stats:');

        $stats = [
            'Total staged items' => JobFeedStagedItem::count(),
            'Pending items' => JobFeedStagedItem::where('status', 'pending')->count(),
            'Approved items' => JobFeedStagedItem::where('status', 'approved')->count(),
            'Imported items' => JobFeedStagedItem::where('status', 'imported')->count(),
            'Expired items' => JobFeedStagedItem::where('status', 'expired')->count(),
            'Total logs' => JobFeedLog::count(),
        ];

        foreach ($stats as $label => $value) {
            $this->line("  {$label}: <fg=cyan>{$value}</>");
        }

        $this->newLine();
        $this->info('Cleanup completed.');

        return Command::SUCCESS;
    }
}
