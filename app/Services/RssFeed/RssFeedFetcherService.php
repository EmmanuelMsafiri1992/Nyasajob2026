<?php

namespace App\Services\RssFeed;

use App\Models\JobFeedSource;
use App\Models\JobFeedLog;
use App\Models\JobFeedStagedItem;
use App\Models\Country;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleXMLElement;

class RssFeedFetcherService
{
    protected int $timeout = 30;
    protected int $maxRetries = 3;
    protected int $maxAgeDays = 7;

    /**
     * Country codes that need jobs (prioritized for distribution)
     * Will be populated dynamically based on job counts
     */
    protected array $countriesNeedingJobs = [];

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
        $rawTitle = trim((string)$item->title);

        // Extract company name and clean title
        // Many feeds use format "Company: Job Title" or "Company - Job Title"
        $titleData = $this->parseCompanyFromTitle($rawTitle);
        $cleanTitle = $titleData['title'];
        $company = $titleData['company'];

        // If company not in title, try author/dc:creator
        if (empty($company)) {
            $company = trim((string)($item->author ?? $item->{'dc:creator'} ?? ''));
        }

        // Extract logo URL from media:content or other sources
        $logoUrl = $this->extractLogoUrl($item);

        // Extract location from multiple possible fields
        $location = $this->extractLocation($item);

        // Extract region/country info for better country detection
        $region = trim((string)($item->region ?? ''));
        $country = trim((string)($item->country ?? ''));
        $state = trim((string)($item->state ?? ''));

        return [
            'id' => (string)($item->guid ?? $item->link ?? md5((string)$item->title)),
            'title' => $cleanTitle,
            'description' => trim((string)($item->description ?? $item->{'content:encoded'} ?? '')),
            'url' => trim((string)$item->link),
            'company' => $company,
            'company_logo_url' => $logoUrl,
            'location' => $location,
            'region' => $region,
            'country' => $country,
            'state' => $state,
            'salary' => trim((string)($item->{'job:salary'} ?? '')),
            'published_at' => $pubDate ? $this->parseDate($pubDate) : null,
            'categories' => $this->extractCategories($item),
        ];
    }

    /**
     * Parse company name from title
     * Handles formats like "Company: Job Title" or "Company - Job Title"
     */
    protected function parseCompanyFromTitle(string $title): array
    {
        $company = '';
        $cleanTitle = $title;

        // Try colon separator first (most common: "Company: Job Title")
        if (preg_match('/^([^:]+):\s*(.+)$/u', $title, $matches)) {
            $potentialCompany = trim($matches[1]);
            $potentialTitle = trim($matches[2]);

            // Only use if company part is reasonable length and title is meaningful
            if (mb_strlen($potentialCompany) >= 2 &&
                mb_strlen($potentialCompany) <= 100 &&
                mb_strlen($potentialTitle) >= 5) {
                $company = $potentialCompany;
                $cleanTitle = $potentialTitle;
            }
        }
        // Try dash separator with spaces (" - ")
        elseif (preg_match('/^([^-]+)\s+-\s+(.+)$/u', $title, $matches)) {
            $potentialCompany = trim($matches[1]);
            $potentialTitle = trim($matches[2]);

            if (mb_strlen($potentialCompany) >= 2 &&
                mb_strlen($potentialCompany) <= 100 &&
                mb_strlen($potentialTitle) >= 5) {
                $company = $potentialCompany;
                $cleanTitle = $potentialTitle;
            }
        }

        return [
            'company' => $company,
            'title' => $cleanTitle,
        ];
    }

    /**
     * Extract logo URL from RSS item
     */
    protected function extractLogoUrl(SimpleXMLElement $item): ?string
    {
        // Try media:content namespace (WeWorkRemotely uses this)
        $namespaces = $item->getNamespaces(true);

        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content)) {
                $url = (string)$media->content->attributes()->url;
                if (!empty($url)) {
                    return $url;
                }
            }
        }

        // Try enclosure (common RSS media attachment)
        if (isset($item->enclosure)) {
            $type = (string)$item->enclosure->attributes()->type;
            if (str_starts_with($type, 'image/')) {
                return (string)$item->enclosure->attributes()->url;
            }
        }

        // Try image element
        if (isset($item->image)) {
            $url = (string)($item->image->url ?? $item->image);
            if (!empty($url)) {
                return $url;
            }
        }

        // Try to extract logo from description (some feeds embed it)
        $description = (string)($item->description ?? '');
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
            $imgUrl = $matches[1];
            // Only use if it looks like a logo (contains "logo" in path or is small)
            if (stripos($imgUrl, 'logo') !== false) {
                return $imgUrl;
            }
        }

        return null;
    }

    /**
     * Extract location from multiple possible RSS fields
     */
    protected function extractLocation(SimpleXMLElement $item): string
    {
        // Priority order for location fields
        $locationFields = [
            $item->{'job:location'} ?? null,
            $item->location ?? null,
            $item->region ?? null,
        ];

        foreach ($locationFields as $field) {
            if ($field !== null) {
                $location = trim((string)$field);
                if (!empty($location) && $location !== 'Anywhere in the World') {
                    return $location;
                }
            }
        }

        // Combine state and region if available
        $state = trim((string)($item->state ?? ''));
        $region = trim((string)($item->region ?? ''));

        if (!empty($state) && !empty($region) && $state !== $region) {
            return "{$state}, {$region}";
        }

        if (!empty($state)) {
            return $state;
        }

        return 'Remote';
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

        $rawTitle = trim((string)$entry->title);
        $titleData = $this->parseCompanyFromTitle($rawTitle);
        $cleanTitle = $titleData['title'];
        $company = $titleData['company'];

        if (empty($company)) {
            $company = trim((string)($entry->author->name ?? ''));
        }

        // Try to get logo from entry
        $logoUrl = null;
        if (isset($entry->logo)) {
            $logoUrl = trim((string)$entry->logo);
        }

        return [
            'id' => (string)($entry->id ?? $link ?? md5((string)$entry->title)),
            'title' => $cleanTitle,
            'description' => trim((string)($entry->content ?? $entry->summary ?? '')),
            'url' => $link,
            'company' => $company,
            'company_logo_url' => $logoUrl,
            'location' => 'Remote',
            'region' => '',
            'country' => '',
            'state' => '',
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
        $rawTitle = trim($item['title'] ?? '');
        $titleData = $this->parseCompanyFromTitle($rawTitle);
        $cleanTitle = $titleData['title'];
        $company = $titleData['company'];

        if (empty($company)) {
            $company = trim($item['author']['name'] ?? $item['company'] ?? '');
        }

        return [
            'id' => $item['id'] ?? $item['url'] ?? md5($item['title'] ?? ''),
            'title' => $cleanTitle,
            'description' => trim($item['content_html'] ?? $item['content_text'] ?? $item['description'] ?? ''),
            'url' => trim($item['url'] ?? $item['external_url'] ?? ''),
            'company' => $company,
            'company_logo_url' => $item['company_logo'] ?? $item['logo'] ?? $item['image'] ?? null,
            'location' => trim($item['location'] ?? 'Remote'),
            'region' => $item['region'] ?? '',
            'country' => $item['country'] ?? '',
            'state' => $item['state'] ?? '',
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

        // Load countries needing jobs for distribution (only for global feeds)
        if (empty($source->country_code)) {
            $this->loadCountriesNeedingJobs();
        }

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

                // Determine country code intelligently
                $countryCode = $this->determineCountryCode($source, $item);

                // Create staged item
                JobFeedStagedItem::create([
                    'feed_source_id' => $source->id,
                    'external_id' => substr($item['id'], 0, 255),
                    'external_url' => substr($item['url'], 0, 500),
                    'title' => substr($item['title'], 0, 191),
                    'raw_description' => $item['description'],
                    'company_name' => substr($item['company'], 0, 200) ?: null,
                    'company_logo_url' => isset($item['company_logo_url']) ? substr($item['company_logo_url'], 0, 500) : null,
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
     * Determine the best country code for a job item
     */
    protected function determineCountryCode(JobFeedSource $source, array $item): string
    {
        // If source has a specific country, use it
        if (!empty($source->country_code)) {
            return $source->country_code;
        }

        // Try to detect country from item's location/region data
        $detectedCountry = $this->detectCountryFromItem($item);
        if ($detectedCountry) {
            return $detectedCountry;
        }

        // For global/remote jobs, distribute to countries that need jobs most
        return $this->getCountryNeedingJobs();
    }

    /**
     * Try to detect country from item's location data
     */
    protected function detectCountryFromItem(array $item): ?string
    {
        // Map of common location strings to country codes
        $locationToCountry = [
            'united states' => 'US',
            'usa' => 'US',
            'u.s.a' => 'US',
            'us' => 'US',
            'united kingdom' => 'GB',
            'uk' => 'GB',
            'england' => 'GB',
            'canada' => 'CA',
            'germany' => 'DE',
            'france' => 'FR',
            'australia' => 'AU',
            'india' => 'IN',
            'netherlands' => 'NL',
            'spain' => 'ES',
            'italy' => 'IT',
            'brazil' => 'BR',
            'mexico' => 'MX',
            'japan' => 'JP',
            'south africa' => 'ZA',
            'nigeria' => 'NG',
            'kenya' => 'KE',
            'ghana' => 'GH',
            'egypt' => 'EG',
            'malawi' => 'MW',
            'zambia' => 'ZM',
            'zimbabwe' => 'ZW',
            'tanzania' => 'TZ',
            'uganda' => 'UG',
            'rwanda' => 'RW',
            'ethiopia' => 'ET',
            'morocco' => 'MA',
            'algeria' => 'DZ',
            'tunisia' => 'TN',
            'senegal' => 'SN',
            'ivory coast' => 'CI',
            'cameroon' => 'CM',
            'angola' => 'AO',
            'mozambique' => 'MZ',
            'botswana' => 'BW',
            'namibia' => 'NA',
            'singapore' => 'SG',
            'philippines' => 'PH',
            'indonesia' => 'ID',
            'malaysia' => 'MY',
            'vietnam' => 'VN',
            'thailand' => 'TH',
            'pakistan' => 'PK',
            'bangladesh' => 'BD',
            'ireland' => 'IE',
            'poland' => 'PL',
            'portugal' => 'PT',
            'sweden' => 'SE',
            'norway' => 'NO',
            'denmark' => 'DK',
            'finland' => 'FI',
            'switzerland' => 'CH',
            'austria' => 'AT',
            'belgium' => 'BE',
            'czech republic' => 'CZ',
            'romania' => 'RO',
            'hungary' => 'HU',
            'ukraine' => 'UA',
            'argentina' => 'AR',
            'chile' => 'CL',
            'colombia' => 'CO',
            'peru' => 'PE',
            'israel' => 'IL',
            'uae' => 'AE',
            'dubai' => 'AE',
            'saudi arabia' => 'SA',
            'qatar' => 'QA',
            'new zealand' => 'NZ',
            'california' => 'US',
            'new york' => 'US',
            'texas' => 'US',
            'florida' => 'US',
            'london' => 'GB',
            'berlin' => 'DE',
            'paris' => 'FR',
            'toronto' => 'CA',
            'sydney' => 'AU',
            'dublin' => 'IE',
        ];

        // Check location, region, state, country fields
        $fieldsToCheck = ['location', 'region', 'state', 'country'];

        foreach ($fieldsToCheck as $field) {
            if (!empty($item[$field])) {
                $locationLower = mb_strtolower(trim($item[$field]));

                // Skip generic remote indicators
                if (in_array($locationLower, ['remote', 'anywhere', 'worldwide', 'global', 'anywhere in the world', ''])) {
                    continue;
                }

                foreach ($locationToCountry as $location => $code) {
                    if (str_contains($locationLower, $location)) {
                        return $code;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Load countries that need jobs (have zero or few jobs)
     */
    protected function loadCountriesNeedingJobs(): void
    {
        $cacheKey = 'countries_needing_jobs';

        $this->countriesNeedingJobs = Cache::remember($cacheKey, 3600, function () {
            // Get job counts per country
            $jobCounts = Post::query()
                ->select('country_code', DB::raw('COUNT(*) as job_count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('country_code')
                ->pluck('job_count', 'country_code')
                ->toArray();

            // Get all active countries
            $allCountries = Country::withoutGlobalScopes()
                ->where('active', 1)
                ->pluck('code')
                ->toArray();

            // Sort countries by job count (ascending) - countries with zero/few jobs first
            $countriesWithScores = [];
            foreach ($allCountries as $code) {
                $jobCount = $jobCounts[$code] ?? 0;
                $countriesWithScores[$code] = $jobCount;
            }

            // Sort by job count (ascending)
            asort($countriesWithScores);

            // Return array of country codes prioritized by need
            return array_keys($countriesWithScores);
        });
    }

    /**
     * Get a country that needs jobs (rotating through those with fewest jobs)
     */
    protected function getCountryNeedingJobs(): string
    {
        if (empty($this->countriesNeedingJobs)) {
            return config('settings.localization.default_country_code', 'MW');
        }

        // Use a rotating index to distribute jobs
        static $index = 0;

        // Get country from the prioritized list (countries with fewer jobs are earlier in list)
        // Focus on first 50 countries (those with fewest jobs)
        $priorityCountries = array_slice($this->countriesNeedingJobs, 0, 50);

        if (empty($priorityCountries)) {
            return config('settings.localization.default_country_code', 'MW');
        }

        $country = $priorityCountries[$index % count($priorityCountries)];
        $index++;

        return $country;
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
