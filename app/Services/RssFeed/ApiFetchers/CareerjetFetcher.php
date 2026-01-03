<?php

namespace App\Services\RssFeed\ApiFetchers;

use App\Models\JobFeedSource;
use App\Services\RssFeed\ApiCredentialsService;
use App\Services\RssFeed\Contracts\JobApiFetcherInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CareerjetFetcher implements JobApiFetcherInterface
{
    protected string $baseUrl;
    protected ?string $affiliateId;
    protected ApiCredentialsService $credentialsService;

    /**
     * Country code to Careerjet locale mapping.
     * Careerjet uses locale codes for different country sites.
     */
    protected array $countryLocales = [
        'US' => 'en_US',
        'GB' => 'en_GB',
        'CA' => 'en_CA',
        'AU' => 'en_AU',
        'DE' => 'de_DE',
        'FR' => 'fr_FR',
        'ES' => 'es_ES',
        'IT' => 'it_IT',
        'NL' => 'nl_NL',
        'BE' => 'nl_BE',
        'AT' => 'de_AT',
        'CH' => 'de_CH',
        'PL' => 'pl_PL',
        'SE' => 'sv_SE',
        'NO' => 'no_NO',
        'DK' => 'da_DK',
        'FI' => 'fi_FI',
        'PT' => 'pt_PT',
        'IE' => 'en_IE',
        'IN' => 'en_IN',
        'SG' => 'en_SG',
        'MY' => 'en_MY',
        'PH' => 'en_PH',
        'ID' => 'id_ID',
        'TH' => 'th_TH',
        'VN' => 'vi_VN',
        'JP' => 'ja_JP',
        'KR' => 'ko_KR',
        'CN' => 'zh_CN',
        'HK' => 'zh_HK',
        'TW' => 'zh_TW',
        'ZA' => 'en_ZA',
        'NG' => 'en_NG',
        'KE' => 'en_KE',
        'EG' => 'ar_EG',
        'MA' => 'fr_MA',
        'BR' => 'pt_BR',
        'MX' => 'es_MX',
        'AR' => 'es_AR',
        'CL' => 'es_CL',
        'CO' => 'es_CO',
        'PE' => 'es_PE',
        'AE' => 'en_AE',
        'SA' => 'ar_SA',
        'PK' => 'en_PK',
        'BD' => 'en_BD',
        'NZ' => 'en_NZ',
        'RU' => 'ru_RU',
        'UA' => 'uk_UA',
        'TR' => 'tr_TR',
        'GR' => 'el_GR',
        'RO' => 'ro_RO',
        'HU' => 'hu_HU',
        'CZ' => 'cs_CZ',
    ];

    public function __construct(?ApiCredentialsService $credentialsService = null)
    {
        $this->credentialsService = $credentialsService ?? app(ApiCredentialsService::class);

        // Load credentials from database or config
        $credentials = $this->credentialsService->getCredentials('careerjet');

        $this->baseUrl = $credentials['base_url'] ?? config('services.careerjet.base_url', 'http://public.api.careerjet.net/search');
        $this->affiliateId = $credentials['credentials']['affiliate_id'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(JobFeedSource $source, int $limit = 50): array
    {
        if (empty($this->affiliateId)) {
            Log::warning('Careerjet affiliate ID not configured. Set it in Admin > API Credentials.');
            return [];
        }

        $countryCode = strtoupper($source->country_code ?? 'US');
        $locale = $this->countryLocales[$countryCode] ?? 'en_US';

        $items = [];
        $page = 1;
        $perPage = min($limit, 20); // Conservative page size

        try {
            while (count($items) < $limit && $page <= 5) {
                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->get($this->baseUrl, [
                        'affid' => $this->affiliateId,
                        'locale_code' => $locale,
                        'page' => $page,
                        'pagesize' => $perPage,
                        'keywords' => '', // All jobs
                        'sort' => 'date', // Most recent first
                        'contracttype' => '', // All contract types
                        'contractperiod' => '', // All periods
                    ]);

                if (!$response->successful()) {
                    Log::error("Careerjet API error: HTTP {$response->status()}", [
                        'country' => $countryCode,
                        'locale' => $locale,
                        'page' => $page,
                    ]);
                    break;
                }

                $data = $response->json();

                // Check for location disambiguation
                if (($data['type'] ?? '') === 'LOCATIONS') {
                    Log::info('Careerjet returned location options', [
                        'country' => $countryCode,
                        'locations' => $data['locations'] ?? [],
                    ]);
                    break;
                }

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

                // Delay between pages - Careerjet is rate-limited
                usleep(2000000); // 2 seconds
            }
        } catch (\Exception $e) {
            Log::error('Careerjet API exception: ' . $e->getMessage(), [
                'country' => $countryCode,
            ]);
        }

        return $items;
    }

    /**
     * Normalize Careerjet job response to standard format.
     */
    protected function normalizeJob(array $job, string $countryCode): array
    {
        // Parse published date
        $publishedAt = null;
        if (!empty($job['date'])) {
            try {
                $publishedAt = Carbon::parse($job['date']);
            } catch (\Exception $e) {
                $publishedAt = Carbon::now();
            }
        }

        // Generate unique ID
        $id = md5($job['url'] ?? ($job['title'] . $job['company'] ?? uniqid()));

        // Extract location
        $location = '';
        if (!empty($job['locations'])) {
            $location = is_array($job['locations']) ? implode(', ', $job['locations']) : $job['locations'];
        }

        return [
            'id' => 'careerjet_' . $id,
            'title' => $job['title'] ?? 'Untitled Position',
            'description' => $job['description'] ?? '',
            'url' => $job['url'] ?? '',
            'company' => $job['company'] ?? '',
            'company_logo_url' => null, // Careerjet doesn't provide logos
            'location' => $location,
            'region' => '',
            'country' => $countryCode,
            'state' => '',
            'salary' => $job['salary'] ?? '',
            'salary_min' => null,
            'salary_max' => null,
            'published_at' => $publishedAt ?? Carbon::now(),
            'categories' => [],
            'source_site' => $job['site'] ?? '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $feedFormat): bool
    {
        return $feedFormat === 'api_careerjet';
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedFormat(): string
    {
        return 'api_careerjet';
    }

    /**
     * Get supported countries.
     */
    public function getSupportedCountries(): array
    {
        return array_keys($this->countryLocales);
    }
}
