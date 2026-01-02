<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\FetchRssFeeds;
use App\Console\Commands\ProcessStagedJobs;
use App\Console\Commands\ImportApprovedJobs;
use App\Console\Commands\CleanupOldFeedData;

class RssFeedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->commands([
            FetchRssFeeds::class,
            ProcessStagedJobs::class,
            ImportApprovedJobs::class,
            CleanupOldFeedData::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
