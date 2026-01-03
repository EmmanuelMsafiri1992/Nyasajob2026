<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFeedStagedItem;
use App\Models\Country;
use App\Models\Post;
use App\Services\RssFeed\JobDataCleanerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProcessStagedJobs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rss:process
        {--status=pending : Status of items to process (pending, approved)}
        {--limit=100 : Maximum items to process}
        {--source= : Specific feed source ID}
        {--country= : Filter by country code}
        {--fix-companies : Re-parse company names from titles for existing items}
        {--redistribute : Redistribute jobs to countries that need them}
        {--all : Process all items regardless of cleaned_description status}';

    protected array $countriesNeedingJobs = [];

    /**
     * The console command description.
     */
    protected $description = 'Clean and process staged RSS feed items (resolve locations, infer categories, format descriptions)';

    /**
     * Execute the console command.
     */
    public function handle(JobDataCleanerService $cleaner): int
    {
        // Handle --fix-companies option
        if ($this->option('fix-companies')) {
            return $this->fixCompanyNames();
        }

        // Handle --redistribute option
        if ($this->option('redistribute')) {
            return $this->redistributeCountries();
        }

        $this->info('Processing staged job items...');

        $query = JobFeedStagedItem::query()
            ->where('status', $this->option('status'))
            ->orderBy('published_at', 'desc');

        // Only filter by cleaned_description if --all is not set
        if (!$this->option('all')) {
            $query->whereNull('cleaned_description');
        }

        if ($source = $this->option('source')) {
            $query->where('feed_source_id', $source);
        }

        if ($country = $this->option('country')) {
            $query->where('country_code', strtoupper($country));
        }

        $items = $query->limit($this->option('limit'))->get();

        if ($items->isEmpty()) {
            $this->info('No items to process.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$items->count()} item(s)...");

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        $processed = 0;
        $failed = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                $cleaner->cleanStagedItem($item);
                $processed++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Item {$item->id}: {$e->getMessage()}";
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Processed: <fg=green>{$processed}</>, Failed: <fg=red>{$failed}</>");

        if (!empty($errors) && $this->option('verbose')) {
            $this->newLine();
            $this->warn('Errors:');
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }
            if (count($errors) > 10) {
                $this->line("  ... and " . (count($errors) - 10) . " more errors");
            }
        }

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Fix company names by re-parsing from titles or raw_data
     */
    protected function fixCompanyNames(): int
    {
        $this->info('Fixing company names from titles...');

        $query = JobFeedStagedItem::query()
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('id', 'desc');

        if ($source = $this->option('source')) {
            $query->where('feed_source_id', $source);
        }

        $items = $query->limit($this->option('limit'))->get();

        if ($items->isEmpty()) {
            $this->info('No items to fix.');
            return Command::SUCCESS;
        }

        $this->info("Fixing {$items->count()} item(s)...");

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        $fixed = 0;

        foreach ($items as $item) {
            $updated = false;

            // First try to get original title from raw_data
            $originalTitle = $item->title;
            if (!empty($item->raw_data) && isset($item->raw_data['title'])) {
                $originalTitle = $item->raw_data['title'];
            }

            $titleData = $this->parseCompanyFromTitle($originalTitle);

            // Update company if we found one and current is empty
            if (!empty($titleData['company']) && (empty($item->company_name) || $item->company_name === 'Various Employers')) {
                $item->company_name = $titleData['company'];
                $updated = true;
            }

            // Update title if it still has company prefix
            if (!empty($titleData['company']) && str_contains($item->title, ':')) {
                $item->title = $titleData['title'];
                $updated = true;
            }

            // Also try to get logo from raw_data if missing
            if (empty($item->company_logo_url) && !empty($item->raw_data)) {
                $logoUrl = $item->raw_data['company_logo_url'] ?? null;
                if ($logoUrl) {
                    $item->company_logo_url = $logoUrl;
                    $updated = true;
                }
            }

            if ($updated) {
                $item->save();
                $fixed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Fixed <fg=green>{$fixed}</> items.");

        return Command::SUCCESS;
    }

    /**
     * Redistribute jobs based on actual location data (not random)
     * Only assigns to country if location data clearly indicates it
     */
    protected function redistributeCountries(): int
    {
        $this->info('Analyzing job locations and assigning correct countries...');

        $query = JobFeedStagedItem::query()
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('id', 'asc');

        if ($source = $this->option('source')) {
            $query->where('feed_source_id', $source);
        }

        $items = $query->limit($this->option('limit'))->get();

        if ($items->isEmpty()) {
            $this->info('No items to process.');
            return Command::SUCCESS;
        }

        $this->info("Analyzing {$items->count()} item(s)...");

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        $updated = 0;
        $worldwide = 0;
        $countryStats = [];

        foreach ($items as $item) {
            // Get location data from raw_data if available
            $locationRaw = $item->location_raw ?? '';
            $region = $item->raw_data['region'] ?? '';
            $country = $item->raw_data['country'] ?? '';
            $state = $item->raw_data['state'] ?? '';

            // Try to detect actual country from location
            $detectedCountry = $this->detectCountryFromLocation($locationRaw, $region, $country, $state);

            if ($detectedCountry) {
                if ($item->country_code !== $detectedCountry) {
                    $item->country_code = $detectedCountry;
                    $item->city_id = null; // Reset city for re-resolution
                    $item->save();
                    $updated++;
                }
                $countryStats[$detectedCountry] = ($countryStats[$detectedCountry] ?? 0) + 1;
            } else {
                // This is a worldwide/remote job - keep in MW or mark specially
                $worldwide++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Updated <fg=green>{$updated}</> jobs with correct countries.");
        $this->info("Worldwide/Remote jobs (no specific country): <fg=yellow>{$worldwide}</>");

        if (!empty($countryStats)) {
            $this->newLine();
            $this->info('Jobs by country:');
            arsort($countryStats);
            foreach (array_slice($countryStats, 0, 15, true) as $code => $count) {
                $this->line("  {$code}: {$count}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Detect country from location strings
     */
    protected function detectCountryFromLocation(?string $location, ?string $region, ?string $country, ?string $state): ?string
    {
        // Map of location strings to country codes
        $locationMap = [
            // Countries
            'united states' => 'US', 'usa' => 'US', 'u.s.a' => 'US', 'u.s.' => 'US',
            'united kingdom' => 'GB', 'uk' => 'GB', 'england' => 'GB', 'scotland' => 'GB', 'wales' => 'GB',
            'canada' => 'CA', 'germany' => 'DE', 'deutschland' => 'DE',
            'france' => 'FR', 'australia' => 'AU', 'india' => 'IN',
            'netherlands' => 'NL', 'holland' => 'NL', 'spain' => 'ES', 'españa' => 'ES',
            'italy' => 'IT', 'italia' => 'IT', 'brazil' => 'BR', 'brasil' => 'BR',
            'mexico' => 'MX', 'méxico' => 'MX', 'japan' => 'JP',
            'south africa' => 'ZA', 'nigeria' => 'NG', 'kenya' => 'KE',
            'ghana' => 'GH', 'egypt' => 'EG', 'malawi' => 'MW',
            'zambia' => 'ZM', 'zimbabwe' => 'ZW', 'tanzania' => 'TZ',
            'uganda' => 'UG', 'rwanda' => 'RW', 'ethiopia' => 'ET',
            'morocco' => 'MA', 'algeria' => 'DZ', 'tunisia' => 'TN',
            'senegal' => 'SN', 'cameroon' => 'CM', 'angola' => 'AO',
            'mozambique' => 'MZ', 'botswana' => 'BW', 'namibia' => 'NA',
            'singapore' => 'SG', 'philippines' => 'PH', 'indonesia' => 'ID',
            'malaysia' => 'MY', 'vietnam' => 'VN', 'thailand' => 'TH',
            'pakistan' => 'PK', 'bangladesh' => 'BD', 'ireland' => 'IE',
            'poland' => 'PL', 'portugal' => 'PT', 'sweden' => 'SE',
            'norway' => 'NO', 'denmark' => 'DK', 'finland' => 'FI',
            'switzerland' => 'CH', 'austria' => 'AT', 'belgium' => 'BE',
            'czech republic' => 'CZ', 'czechia' => 'CZ', 'romania' => 'RO',
            'hungary' => 'HU', 'ukraine' => 'UA', 'argentina' => 'AR',
            'chile' => 'CL', 'colombia' => 'CO', 'peru' => 'PE',
            'israel' => 'IL', 'uae' => 'AE', 'united arab emirates' => 'AE',
            'saudi arabia' => 'SA', 'qatar' => 'QA', 'new zealand' => 'NZ',
            'china' => 'CN', 'south korea' => 'KR', 'korea' => 'KR',
            'taiwan' => 'TW', 'hong kong' => 'HK', 'russia' => 'RU',
            'turkey' => 'TR', 'greece' => 'GR', 'croatia' => 'HR',
            // US States -> US
            'california' => 'US', 'new york' => 'US', 'texas' => 'US',
            'florida' => 'US', 'washington' => 'US', 'colorado' => 'US',
            'massachusetts' => 'US', 'illinois' => 'US', 'georgia' => 'US',
            'arizona' => 'US', 'oregon' => 'US', 'virginia' => 'US',
            'north carolina' => 'US', 'michigan' => 'US', 'ohio' => 'US',
            'pennsylvania' => 'US', 'minnesota' => 'US', 'utah' => 'US',
            // Major cities
            'london' => 'GB', 'manchester' => 'GB', 'edinburgh' => 'GB',
            'berlin' => 'DE', 'munich' => 'DE', 'frankfurt' => 'DE',
            'paris' => 'FR', 'lyon' => 'FR', 'marseille' => 'FR',
            'toronto' => 'CA', 'vancouver' => 'CA', 'montreal' => 'CA',
            'sydney' => 'AU', 'melbourne' => 'AU', 'brisbane' => 'AU',
            'dublin' => 'IE', 'amsterdam' => 'NL', 'barcelona' => 'ES',
            'madrid' => 'ES', 'rome' => 'IT', 'milan' => 'IT',
            'tokyo' => 'JP', 'osaka' => 'JP', 'mumbai' => 'IN',
            'bangalore' => 'IN', 'delhi' => 'IN', 'johannesburg' => 'ZA',
            'cape town' => 'ZA', 'lagos' => 'NG', 'nairobi' => 'KE',
            'accra' => 'GH', 'cairo' => 'EG', 'lilongwe' => 'MW',
            'blantyre' => 'MW', 'lusaka' => 'ZM', 'harare' => 'ZW',
            'dar es salaam' => 'TZ', 'kampala' => 'UG', 'kigali' => 'RW',
            // US Cities - Silicon Valley and major tech hubs
            'san francisco' => 'US', 'los angeles' => 'US', 'san jose' => 'US',
            'palo alto' => 'US', 'redwood city' => 'US', 'mountain view' => 'US',
            'sunnyvale' => 'US', 'cupertino' => 'US', 'menlo park' => 'US',
            'santa clara' => 'US', 'fremont' => 'US', 'oakland' => 'US',
            'sacramento' => 'US', 'san diego' => 'US', 'seattle' => 'US',
            'portland' => 'US', 'denver' => 'US', 'austin' => 'US',
            'dallas' => 'US', 'houston' => 'US', 'phoenix' => 'US',
            'las vegas' => 'US', 'salt lake city' => 'US', 'chicago' => 'US',
            'boston' => 'US', 'new york city' => 'US', 'atlanta' => 'US',
            'miami' => 'US', 'philadelphia' => 'US', 'raleigh' => 'US',
            'charlotte' => 'US', 'nashville' => 'US', 'detroit' => 'US',
            'minneapolis' => 'US', 'pittsburgh' => 'US', 'st louis' => 'US',
            'kansas city' => 'US', 'indianapolis' => 'US', 'columbus' => 'US',
        ];

        // Skip generic remote indicators
        $remoteIndicators = [
            'remote', 'anywhere', 'worldwide', 'global', 'anywhere in the world',
            'work from home', 'wfh', 'fully remote', 'remote - us',
        ];

        // Check each field
        $fieldsToCheck = array_filter([$location, $region, $state, $country]);

        foreach ($fieldsToCheck as $field) {
            $fieldLower = mb_strtolower(trim($field));

            // Skip if it's a remote indicator
            foreach ($remoteIndicators as $indicator) {
                if ($fieldLower === $indicator) {
                    continue 2;
                }
            }

            // Try to match country/city
            foreach ($locationMap as $searchTerm => $countryCode) {
                if (str_contains($fieldLower, $searchTerm)) {
                    return $countryCode;
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
        // Get job counts per country (last 30 days)
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

        // Store array of country codes prioritized by need
        $this->countriesNeedingJobs = array_keys($countriesWithScores);
    }

    /**
     * Parse company name from title
     */
    protected function parseCompanyFromTitle(string $title): array
    {
        $company = '';
        $cleanTitle = $title;

        // Try colon separator first (most common: "Company: Job Title")
        if (preg_match('/^([^:]+):\s*(.+)$/u', $title, $matches)) {
            $potentialCompany = trim($matches[1]);
            $potentialTitle = trim($matches[2]);

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
}
