<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\FetchRssFeeds;
use App\Console\Commands\ProcessStagedJobs;
use App\Console\Commands\ImportApprovedJobs;
use App\Console\Commands\CleanupOldFeedData;
use App\Console\Commands\TestJobApi;
use App\Services\RssFeed\ApiCredentialsService;

class RssFeedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register API Credentials Service as singleton
        $this->app->singleton(ApiCredentialsService::class, function ($app) {
            return new ApiCredentialsService();
        });

        $this->commands([
            FetchRssFeeds::class,
            ProcessStagedJobs::class,
            ImportApprovedJobs::class,
            CleanupOldFeedData::class,
            TestJobApi::class,
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
