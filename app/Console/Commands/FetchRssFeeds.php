<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFeedSource;
use App\Services\RssFeed\RssFeedFetcherService;

class FetchRssFeeds extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rss:fetch
        {--source= : Specific source ID to fetch}
        {--country= : Fetch sources for specific country code}
        {--status=active : Filter by source status}
        {--force : Force fetch regardless of schedule}
        {--limit=10 : Maximum sources to process}
        {--rest=5 : Seconds to rest between sources}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch jobs from RSS feed sources with rate limiting and staggered execution';

    protected int $restBetweenSources = 5;
    protected int $restBetweenCountries = 15;

    /**
     * Execute the console command.
     */
    public function handle(RssFeedFetcherService $fetcher): int
    {
        $this->info('Starting RSS feed fetcher...');
        $this->restBetweenSources = (int) $this->option('rest');

        $query = JobFeedSource::query()
            ->where('status', $this->option('status'))
            ->orderBy('priority', 'desc')
            ->orderBy('last_fetched_at', 'asc');

        // Apply filters
        if ($sourceId = $this->option('source')) {
            $query->where('id', $sourceId);
        }

        if ($country = $this->option('country')) {
            $query->where('country_code', strtoupper($country));
        }

        if (!$this->option('force')) {
            // Only fetch sources due for refresh
            $query->where(function ($q) {
                $q->whereNull('last_fetched_at')
                    ->orWhereRaw('DATE_ADD(last_fetched_at, INTERVAL fetch_interval_minutes MINUTE) <= NOW()');
            });
        }

        $sources = $query->limit($this->option('limit'))->get();

        if ($sources->isEmpty()) {
            $this->info('No sources due for fetching.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$sources->count()} source(s)...");
        $this->newLine();

        $currentCountry = null;
        $successCount = 0;
        $failCount = 0;
        $totalNewJobs = 0;

        foreach ($sources as $source) {
            // Rest period between countries
            if ($currentCountry && $currentCountry !== $source->country_code) {
                $this->line("  <fg=gray>Switching to {$source->country_code}, resting {$this->restBetweenCountries}s...</>");
                sleep($this->restBetweenCountries);
            }
            $currentCountry = $source->country_code;

            $this->line("Fetching: <fg=cyan>{$source->name}</> ({$source->country_code})");

            try {
                $log = $fetcher->fetchSource($source);

                if ($log->status === 'success') {
                    $this->info("  -> <fg=green>Success</>: {$log->items_new} new, {$log->items_duplicate} duplicates");
                    $successCount++;
                    $totalNewJobs += $log->items_new;
                } elseif ($log->status === 'partial') {
                    $this->warn("  -> <fg=yellow>Partial</>: {$log->items_new} new, {$log->items_failed} failed");
                    $successCount++;
                    $totalNewJobs += $log->items_new;
                } else {
                    $this->error("  -> <fg=red>Failed</>: {$log->error_message}");
                    $failCount++;
                }
            } catch (\Throwable $e) {
                $this->error("  -> <fg=red>Error</>: {$e->getMessage()}");
                $failCount++;
            }

            // Rest between sources
            if ($sources->last() !== $source) {
                sleep($this->restBetweenSources);
            }
        }

        $this->newLine();
        $this->info("Completed: <fg=green>{$successCount} success</>, <fg=red>{$failCount} failed</>");
        $this->info("Total new jobs staged: <fg=cyan>{$totalNewJobs}</>");

        return $failCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
