<?php

namespace App\Services\RssFeed\ApiFetchers;

use App\Models\JobFeedSource;
use App\Services\RssFeed\ApiCredentialsService;
use App\Services\RssFeed\Contracts\JobApiFetcherInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdzunaFetcher implements JobApiFetcherInterface
{
    protected string $baseUrl;
    protected ?string $appId;
    protected ?string $appKey;
    protected array $supportedCountries;
    protected ApiCredentialsService $credentialsService;

    public function __construct(?ApiCredentialsService $credentialsService = null)
    {
        $this->credentialsService = $credentialsService ?? app(ApiCredentialsService::class);

        // Load credentials from database or config
        $credentials = $this->credentialsService->getCredentials('adzuna');

        $this->baseUrl = $credentials['base_url'] ?? config('services.adzuna.base_url', 'https://api.adzuna.com/v1/api/jobs');
        $this->appId = $credentials['credentials']['app_id'] ?? null;
        $this->appKey = $credentials['credentials']['app_key'] ?? null;
        $this->supportedCountries = config('services.adzuna.countries', [
            'gb', 'us', 'au', 'at', 'br', 'ca', 'de', 'fr', 'in', 'it', 'nl', 'nz', 'pl', 'sg', 'za'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(JobFeedSource $source, int $limit = 50): array
    {
        if (empty($this->appId) || empty($this->appKey)) {
            Log::warning('Adzuna API credentials not configured. Set them in Admin > API Credentials.');
            return [];
        }

        // Get country code from source (lowercase for Adzuna API)
        $countryCode = strtolower($source->country_code ?? 'gb');

        if (!in_array($countryCode, $this->supportedCountries)) {
            Log::warning("Adzuna does not support country: {$countryCode}");
            return [];
        }

        $items = [];
        $page = 1;
        $perPage = min($limit, 50); // Adzuna max is 50 per page

        try {
            // Fetch multiple pages if needed
            while (count($items) < $limit && $page <= 5) { // Max 5 pages to avoid rate limits
                $url = "{$this->baseUrl}/{$countryCode}/search/{$page}";

                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->get($url, [
                        'app_id' => $this->appId,
                        'app_key' => $this->appKey,
                        'results_per_page' => $perPage,
                        'content-type' => 'application/json',
                    ]);

                if (!$response->successful()) {
                    Log::error("Adzuna API error: HTTP {$response->status()}", [
                        'country' => $countryCode,
                        'page' => $page,
                    ]);
                    break;
                }

                $data = $response->json();
                $results = $data['results'] ?? [];

                if (empty($results)) {
                    break;
                }

                foreach ($results as $job) {
                    $items[] = $this->normalizeJob($job, $countryCode);

                    if (count($items) >= $limit) {
                        break 2;
                    }
                }

                $page++;

                // Small delay between pages to respect rate limits
                usleep(500000); // 500ms
            }
        } catch (\Exception $e) {
            Log::error('Adzuna API exception: ' . $e->getMessage(), [
                'country' => $countryCode,
            ]);
        }

        return $items;
    }

    /**
     * Normalize Adzuna job response to standard format.
     */
    protected function normalizeJob(array $job, string $countryCode): array
    {
        // Extract location
        $location = $job['location']['display_name'] ?? '';
        $locationArea = $job['location']['area'] ?? [];
        $region = $locationArea[1] ?? ''; // Usually state/region

        // Extract company
        $company = $job['company']['display_name'] ?? '';

        // Extract category
        $category = $job['category']['label'] ?? '';
        $categoryTag = $job['category']['tag'] ?? '';

        // Build salary string
        $salary = '';
        if (!empty($job['salary_min']) || !empty($job['salary_max'])) {
            $min = $job['salary_min'] ?? 0;
            $max = $job['salary_max'] ?? 0;
            if ($min && $max) {
                $salary = number_format($min) . ' - ' . number_format($max);
            } elseif ($min) {
                $salary = 'From ' . number_format($min);
            } elseif ($max) {
                $salary = 'Up to ' . number_format($max);
            }
        }

        // Parse published date
        $publishedAt = null;
        if (!empty($job['created'])) {
            try {
                $publishedAt = Carbon::parse($job['created']);
            } catch (\Exception $e) {
                $publishedAt = Carbon::now();
            }
        }

        return [
            'id' => 'adzuna_' . ($job['id'] ?? md5($job['redirect_url'] ?? '')),
            'title' => $job['title'] ?? 'Untitled Position',
            'description' => $job['description'] ?? '',
            'url' => $job['redirect_url'] ?? '',
            'company' => $company,
            'company_logo_url' => null, // Adzuna doesn't provide logos
            'location' => $location,
            'region' => $region,
            'country' => strtoupper($countryCode),
            'state' => $region,
            'salary' => $salary,
            'salary_min' => $job['salary_min'] ?? null,
            'salary_max' => $job['salary_max'] ?? null,
            'published_at' => $publishedAt ?? Carbon::now(),
            'categories' => array_filter([$category, $categoryTag, $job['contract_type'] ?? '', $job['contract_time'] ?? '']),
            'latitude' => $job['latitude'] ?? null,
            'longitude' => $job['longitude'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $feedFormat): bool
    {
        return $feedFormat === 'api_adzuna';
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedFormat(): string
    {
        return 'api_adzuna';
    }

    /**
     * Get list of supported countries.
     */
    public function getSupportedCountries(): array
    {
        return $this->supportedCountries;
    }
}
