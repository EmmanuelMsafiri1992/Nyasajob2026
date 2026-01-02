<?php

namespace App\Services\RssFeed;

use App\Models\JobFeedSource;
use App\Models\JobFeedLog;
use App\Models\JobFeedStagedItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use SimpleXMLElement;

class RssFeedFetcherService
{
    protected int $timeout = 30;
    protected int $maxRetries = 3;
    protected int $maxAgeDays = 7;

    /**
     * Fetch jobs from a feed source
     */
    public function fetchSource(JobFeedSource $source): JobFeedLog
    {
        $startTime = microtime(true);
        $log = new JobFeedLog(['feed_source_id' => $source->id]);

        try {
            // Enforce rate limiting
            $this->enforceRateLimit($source);

            // Update last fetched timestamp
            $source->update(['last_fetched_at' => now()]);

            // Fetch RSS content
            $response = Http::timeout($this->timeout)
                ->retry($this->maxRetries, 500)
                ->withHeaders([
                    'User-Agent' => 'NyasaJob RSS Aggregator/1.0 (+https://nyasajob.com)',
                    'Accept' => 'application/rss+xml, application/atom+xml, application/xml, text/xml, application/json',
                ])
                ->get($source->feed_url);

            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()}: Failed to fetch feed");
            }

            $content = $response->body();

            // Parse feed based on format
            $items = $this->parseFeed($content, $source->feed_format);

            // Process items
            $result = $this->processItems($items, $source);

            // Update log
            $log->status = $result['failed'] > 0 ? 'partial' : 'success';
            $log->items_found = $result['total'];
            $log->items_new = $result['new'];
            $log->items_duplicate = $result['duplicate'];
            $log->items_failed = $result['failed'];
            $log->details = $result['details'] ?? null;

            // Update source stats
            $source->markAsSuccessful();
            $source->increment('total_jobs_fetched', $result['new']);

            Log::info("RSS fetch successful for source {$source->id}", [
                'source' => $source->name,
                'new' => $result['new'],
                'duplicate' => $result['duplicate'],
            ]);

        } catch (\Throwable $e) {
            $log->status = 'failed';
            $log->error_message = $e->getMessage();

            $source->markAsFailed($e->getMessage());

            Log::error("RSS fetch failed for source {$source->id}", [
                'error' => $e->getMessage(),
                'url' => $source->feed_url,
            ]);
        }

        $log->duration_ms = (int)((microtime(true) - $startTime) * 1000);
        $log->save();

        return $log;
    }

    /**
     * Parse feed content based on format
     */
    protected function parseFeed(string $content, string $format): array
    {
        return match ($format) {
            'json' => $this->parseJsonFeed($content),
            'atom' => $this->parseAtomFeed($content),
            default => $this->parseRssFeed($content),
        };
    }

    /**
     * Parse RSS 2.0 feed
     */
    protected function parseRssFeed(string $content): array
    {
        $items = [];

        try {
            // Suppress XML errors and handle them gracefully
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($content);
            libxml_clear_errors();

            // Check for RSS 2.0 structure
            $channel = $xml->channel ?? $xml;
            $feedItems = $channel->item ?? [];

            foreach ($feedItems as $item) {
                $items[] = $this->normalizeRssItem($item);
            }
        } catch (\Exception $e) {
            Log::warning("RSS parsing error: {$e->getMessage()}");
        }

        return $items;
    }

    /**
     * Parse Atom feed
     */
    protected function parseAtomFeed(string $content): array
    {
        $items = [];

        try {
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($content);
            libxml_clear_errors();

            // Register Atom namespace
            $xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');

            foreach ($xml->entry as $entry) {
                $items[] = $this->normalizeAtomEntry($entry);
            }
        } catch (\Exception $e) {
            Log::warning("Atom parsing error: {$e->getMessage()}");
        }

        return $items;
    }

    /**
     * Parse JSON feed
     */
    protected function parseJsonFeed(string $content): array
    {
        $items = [];

        try {
            $data = json_decode($content, true);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $items[] = $this->normalizeJsonItem($item);
                }
            }
        } catch (\Exception $e) {
            Log::warning("JSON feed parsing error: {$e->getMessage()}");
        }

        return $items;
    }

    /**
     * Normalize RSS item to standard format
     */
    protected function normalizeRssItem(SimpleXMLElement $item): array
    {
        $pubDate = (string)($item->pubDate ?? $item->date ?? '');

        return [
            'id' => (string)($item->guid ?? $item->link ?? md5((string)$item->title)),
            'title' => trim((string)$item->title),
            'description' => trim((string)($item->description ?? $item->{'content:encoded'} ?? '')),
            'url' => trim((string)$item->link),
            'company' => trim((string)($item->author ?? $item->{'dc:creator'} ?? '')),
            'location' => trim((string)($item->{'job:location'} ?? $item->location ?? '')),
            'salary' => trim((string)($item->{'job:salary'} ?? '')),
            'published_at' => $pubDate ? $this->parseDate($pubDate) : null,
            'categories' => $this->extractCategories($item),
        ];
    }

    /**
     * Normalize Atom entry to standard format
     */
    protected function normalizeAtomEntry(SimpleXMLElement $entry): array
    {
        $link = '';
        foreach ($entry->link as $l) {
            if ((string)$l['rel'] === 'alternate' || empty((string)$l['rel'])) {
                $link = (string)$l['href'];
                break;
            }
        }

        return [
            'id' => (string)($entry->id ?? $link ?? md5((string)$entry->title)),
            'title' => trim((string)$entry->title),
            'description' => trim((string)($entry->content ?? $entry->summary ?? '')),
            'url' => $link,
            'company' => trim((string)($entry->author->name ?? '')),
            'location' => '',
            'salary' => '',
            'published_at' => $this->parseDate((string)($entry->published ?? $entry->updated ?? '')),
            'categories' => [],
        ];
    }

    /**
     * Normalize JSON item to standard format
     */
    protected function normalizeJsonItem(array $item): array
    {
        return [
            'id' => $item['id'] ?? $item['url'] ?? md5($item['title'] ?? ''),
            'title' => trim($item['title'] ?? ''),
            'description' => trim($item['content_html'] ?? $item['content_text'] ?? $item['description'] ?? ''),
            'url' => trim($item['url'] ?? $item['external_url'] ?? ''),
            'company' => trim($item['author']['name'] ?? $item['company'] ?? ''),
            'location' => trim($item['location'] ?? ''),
            'salary' => trim($item['salary'] ?? ''),
            'published_at' => $this->parseDate($item['date_published'] ?? $item['date'] ?? ''),
            'categories' => $item['tags'] ?? [],
        ];
    }

    /**
     * Process normalized items and store in staging table
     */
    protected function processItems(array $items, JobFeedSource $source): array
    {
        $result = [
            'total' => count($items),
            'new' => 0,
            'duplicate' => 0,
            'failed' => 0,
            'details' => [],
        ];

        $maxItems = $source->max_items_per_fetch;
        $processed = 0;

        foreach ($items as $item) {
            if ($processed >= $maxItems) {
                break;
            }

            try {
                // Skip if too old
                if ($item['published_at'] && Carbon::parse($item['published_at'])->lt(now()->subDays($this->maxAgeDays))) {
                    continue;
                }

                // Skip if title or description is empty
                if (empty($item['title']) || empty($item['description'])) {
                    $result['failed']++;
                    continue;
                }

                // Generate checksum for duplicate detection
                $checksum = JobFeedStagedItem::generateChecksum(
                    $item['title'],
                    $item['description'],
                    $item['company']
                );

                // Check for duplicates
                $exists = JobFeedStagedItem::where('checksum', $checksum)->exists();

                if ($exists) {
                    $result['duplicate']++;
                    continue;
                }

                // Determine country code - default to MW (Malawi) for global feeds
                $countryCode = $source->country_code ?? config('settings.localization.default_country_code', 'MW');

                // Create staged item
                JobFeedStagedItem::create([
                    'feed_source_id' => $source->id,
                    'external_id' => substr($item['id'], 0, 255),
                    'external_url' => substr($item['url'], 0, 500),
                    'title' => substr($item['title'], 0, 191),
                    'raw_description' => $item['description'],
                    'company_name' => substr($item['company'], 0, 200) ?: null,
                    'location_raw' => substr($item['location'], 0, 255) ?: 'Remote',
                    'country_code' => $countryCode,
                    'category_id' => $source->category_id,
                    'published_at' => $item['published_at'],
                    'tags' => is_array($item['categories']) ? implode(',', array_slice($item['categories'], 0, 10)) : null,
                    'application_url' => substr($item['url'], 0, 500),
                    'checksum' => $checksum,
                    'raw_data' => $item,
                    'status' => $source->auto_approve ? 'approved' : 'pending',
                ]);

                $result['new']++;
                $processed++;

            } catch (\Throwable $e) {
                $result['failed']++;
                $result['details'][] = "Item failed: {$e->getMessage()}";
                Log::warning("Failed to stage RSS item", [
                    'title' => $item['title'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    /**
     * Extract categories from RSS item
     */
    protected function extractCategories(SimpleXMLElement $item): array
    {
        $categories = [];

        if (isset($item->category)) {
            foreach ($item->category as $cat) {
                $categories[] = trim((string)$cat);
            }
        }

        return array_filter($categories);
    }

    /**
     * Parse various date formats
     */
    protected function parseDate(string $dateString): ?Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Enforce rate limiting between requests
     */
    protected function enforceRateLimit(JobFeedSource $source): void
    {
        $cacheKey = "rss_rate_limit_{$source->id}";

        if (Cache::has($cacheKey)) {
            $delayMs = $source->rate_limit_delay_ms;
            usleep($delayMs * 1000);
        }

        Cache::put($cacheKey, true, now()->addSeconds(5));
    }
}
