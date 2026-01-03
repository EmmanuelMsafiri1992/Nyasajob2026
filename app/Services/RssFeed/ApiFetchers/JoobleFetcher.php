<?php

namespace App\Services\RssFeed\ApiFetchers;

use App\Models\JobFeedSource;
use App\Services\RssFeed\ApiCredentialsService;
use App\Services\RssFeed\Contracts\JobApiFetcherInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JoobleFetcher implements JobApiFetcherInterface
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ApiCredentialsService $credentialsService;

    /**
     * Country code to Jooble location mapping.
     * Jooble uses location strings rather than country codes.
     */
    protected array $countryLocations = [
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'AU' => 'Australia',
        'DE' => 'Germany',
        'FR' => 'France',
        'IN' => 'India',
        'NL' => 'Netherlands',
        'ES' => 'Spain',
        'IT' => 'Italy',
        'BR' => 'Brazil',
        'MX' => 'Mexico',
        'JP' => 'Japan',
        'ZA' => 'South Africa',
        'NG' => 'Nigeria',
        'KE' => 'Kenya',
        'GH' => 'Ghana',
        'EG' => 'Egypt',
        'MW' => 'Malawi',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
        'TZ' => 'Tanzania',
        'UG' => 'Uganda',
        'RW' => 'Rwanda',
        'SG' => 'Singapore',
        'PH' => 'Philippines',
        'ID' => 'Indonesia',
        'MY' => 'Malaysia',
        'PK' => 'Pakistan',
        'AE' => 'United Arab Emirates',
        'SA' => 'Saudi Arabia',
        'PL' => 'Poland',
        'SE' => 'Sweden',
        'NO' => 'Norway',
        'DK' => 'Denmark',
        'FI' => 'Finland',
        'CH' => 'Switzerland',
        'AT' => 'Austria',
        'BE' => 'Belgium',
        'IE' => 'Ireland',
        'PT' => 'Portugal',
        'NZ' => 'New Zealand',
        'AR' => 'Argentina',
        'CL' => 'Chile',
        'CO' => 'Colombia',
    ];

    public function __construct(?ApiCredentialsService $credentialsService = null)
    {
        $this->credentialsService = $credentialsService ?? app(ApiCredentialsService::class);

        // Load credentials from database or config
        $credentials = $this->credentialsService->getCredentials('jooble');

        $this->baseUrl = $credentials['base_url'] ?? config('services.jooble.base_url', 'https://jooble.org/api');
        $this->apiKey = $credentials['credentials']['api_key'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(JobFeedSource $source, int $limit = 50): array
    {
        if (empty($this->apiKey)) {
            Log::warning('Jooble API key not configured. Set it in Admin > API Credentials.');
            return [];
        }

        $countryCode = strtoupper($source->country_code ?? 'US');
        $location = $this->countryLocations[$countryCode] ?? $countryCode;

        $items = [];
        $page = 1;
        $perPage = min($limit, 20); // Conservative page size

        try {
            while (count($items) < $limit && $page <= 5) {
                $url = "{$this->baseUrl}/{$this->apiKey}";

                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'keywords' => '', // Empty for all jobs
                        'location' => $location,
                        'page' => $page,
                        'ResultOnPage' => $perPage,
                    ]);

                if (!$response->successful()) {
                    Log::error("Jooble API error: HTTP {$response->status()}", [
                        'country' => $countryCode,
                        'location' => $location,
                        'page' => $page,
                    ]);
                    break;
                }

                $data = $response->json();
                $jobs = $data['jobs'] ?? [];

                if (empty($jobs)) {
                    break;
                }

                foreach ($jobs as $job) {
                    $items[] = $this->normalizeJob($job, $countryCode);

                    if (count($items) >= $limit) {
                        break 2;
                    }
                }

                $page++;

                // Delay between pages
                usleep(1000000); // 1 second - be conservative with Jooble
            }
        } catch (\Exception $e) {
            Log::error('Jooble API exception: ' . $e->getMessage(), [
                'country' => $countryCode,
            ]);
        }

        return $items;
    }

    /**
     * Normalize Jooble job response to standard format.
     */
    protected function normalizeJob(array $job, string $countryCode): array
    {
        // Parse published date
        $publishedAt = null;
        if (!empty($job['updated'])) {
            try {
                $publishedAt = Carbon::parse($job['updated']);
            } catch (\Exception $e) {
                $publishedAt = Carbon::now();
            }
        }

        // Extract ID from link or generate one
        $id = !empty($job['id']) ? $job['id'] : md5($job['link'] ?? $job['title'] ?? uniqid());

        return [
            'id' => 'jooble_' . $id,
            'title' => $job['title'] ?? 'Untitled Position',
            'description' => $job['snippet'] ?? $job['description'] ?? '',
            'url' => $job['link'] ?? '',
            'company' => $job['company'] ?? '',
            'company_logo_url' => null, // Jooble doesn't provide logos in API
            'location' => $job['location'] ?? '',
            'region' => '',
            'country' => $countryCode,
            'state' => '',
            'salary' => $job['salary'] ?? '',
            'salary_min' => null,
            'salary_max' => null,
            'published_at' => $publishedAt ?? Carbon::now(),
            'categories' => !empty($job['type']) ? [$job['type']] : [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $feedFormat): bool
    {
        return $feedFormat === 'api_jooble';
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedFormat(): string
    {
        return 'api_jooble';
    }

    /**
     * Get supported countries.
     */
    public function getSupportedCountries(): array
    {
        return array_keys($this->countryLocations);
    }
}
