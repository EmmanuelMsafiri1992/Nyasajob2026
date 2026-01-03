<?php

namespace App\Services\RssFeed\Contracts;

use App\Models\JobFeedSource;

interface JobApiFetcherInterface
{
    /**
     * Fetch jobs from the API source.
     *
     * @param JobFeedSource $source The feed source configuration
     * @param int $limit Maximum number of jobs to fetch
     * @return array Array of normalized job items
     */
    public function fetch(JobFeedSource $source, int $limit = 50): array;

    /**
     * Check if this fetcher supports the given feed format.
     *
     * @param string $feedFormat The feed format identifier
     * @return bool
     */
    public function supports(string $feedFormat): bool;

    /**
     * Get the feed format this fetcher handles.
     *
     * @return string
     */
    public function getFeedFormat(): string;
}
