<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\JobFeedSource;
use App\Models\JobFeedStagedItem;
use App\Models\Post;
use App\Services\RssFeed\RssFeedFetcherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmartJobFetcher extends Command
{
    protected $signature = 'jobs:smart-fetch
                            {--min-jobs=5 : Minimum jobs per country per day}
                            {--dry-run : Show what would be done without actually fetching}
                            {--country= : Fetch for specific country only}';

    protected $description = 'Intelligently fetch jobs prioritizing countries with fewer recent jobs';

    protected RssFeedFetcherService $fetcherService;

    public function __construct(RssFeedFetcherService $fetcherService)
    {
        parent::__construct();
        $this->fetcherService = $fetcherService;
    }

    public function handle(): int
    {
        $minJobs = (int) $this->option('min-jobs');
        $dryRun = $this->option('dry-run');
        $specificCountry = $this->option('country');

        $this->info('Smart Job Fetcher - Analyzing job distribution...');
        $this->newLine();

        // Get job counts per country (last 24 hours)
        $countriesNeedingJobs = $this->getCountriesNeedingJobs($minJobs, $specificCountry);

        if ($countriesNeedingJobs->isEmpty()) {
            $this->info('All countries have sufficient jobs. Nothing to do.');
            return Command::SUCCESS;
        }

        $this->table(
            ['Country', 'Code', 'Jobs (24h)', 'Jobs Needed'],
            $countriesNeedingJobs->map(fn($c) => [
                $c['name'],
                $c['code'],
                $c['recent_jobs'],
                max(0, $minJobs - $c['recent_jobs'])
            ])->toArray()
        );

        if ($dryRun) {
            $this->warn('DRY RUN - No jobs will be fetched');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('Fetching jobs for countries with insufficient jobs...');

        foreach ($countriesNeedingJobs as $country) {
            $this->fetchJobsForCountry($country['code'], $minJobs - $country['recent_jobs']);
        }

        // Also fetch from global/remote job sources and distribute
        $this->info('Fetching from global remote job sources...');
        $this->fetchGlobalJobs($countriesNeedingJobs);

        $this->newLine();
        $this->info('Smart fetch completed!');

        return Command::SUCCESS;
    }

    /**
     * Get countries that need more jobs
     */
    protected function getCountriesNeedingJobs(int $minJobs, ?string $specificCountry = null)
    {
        $query = Country::query()
            ->where('active', 1)
            ->select('code', 'name');

        if ($specificCountry) {
            $query->where('code', strtoupper($specificCountry));
        }

        $countries = $query->get();

        // Get job counts per country (last 24 hours)
        $recentJobCounts = Post::query()
            ->select('country_code', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('country_code')
            ->pluck('count', 'country_code')
            ->toArray();

        return $countries->map(function ($country) use ($recentJobCounts, $minJobs) {
            $recentJobs = $recentJobCounts[$country->code] ?? 0;
            return [
                'code' => $country->code,
                'name' => is_array($country->name) ? ($country->name['en'] ?? $country->code) : $country->name,
                'recent_jobs' => $recentJobs,
                'needs_jobs' => $recentJobs < $minJobs,
            ];
        })->filter(fn($c) => $c['needs_jobs'])
          ->sortBy('recent_jobs');
    }

    /**
     * Fetch jobs for a specific country
     */
    protected function fetchJobsForCountry(string $countryCode, int $jobsNeeded): void
    {
        $this->line("  Fetching for {$countryCode} (need {$jobsNeeded} more jobs)...");

        // Get active feed sources for this country
        $sources = JobFeedSource::query()
            ->where('status', 'active')
            ->where(function ($q) use ($countryCode) {
                $q->where('country_code', $countryCode)
                  ->orWhereNull('country_code');
            })
            ->orderByDesc('priority')
            ->get();

        $totalFetched = 0;

        foreach ($sources as $source) {
            if ($totalFetched >= $jobsNeeded) {
                break;
            }

            try {
                $log = $this->fetcherService->fetchSource($source);
                $fetched = $log->items_new ?? 0;
                $totalFetched += $fetched;
                $this->line("    - {$source->name}: {$fetched} new jobs");
            } catch (\Throwable $e) {
                $this->warn("    - {$source->name}: Failed - {$e->getMessage()}");
            }
        }

        $this->info("    Total: {$totalFetched} new jobs for {$countryCode}");
    }

    /**
     * Fetch from global/remote sources and distribute to countries needing jobs
     */
    protected function fetchGlobalJobs($countriesNeedingJobs): void
    {
        // Get global feed sources (no specific country)
        $globalSources = JobFeedSource::query()
            ->where('status', 'active')
            ->whereNull('country_code')
            ->orderByDesc('priority')
            ->get();

        foreach ($globalSources as $source) {
            try {
                $log = $this->fetcherService->fetchSource($source);
                $this->line("  - {$source->name}: " . ($log->items_new ?? 0) . " new jobs");
            } catch (\Throwable $e) {
                $this->warn("  - {$source->name}: Failed - {$e->getMessage()}");
            }
        }
    }
}
