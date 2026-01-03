<?php

namespace App\Console\Commands;

use App\Services\RssFeed\ApiFetchers\AdzunaFetcher;
use App\Services\RssFeed\ApiFetchers\JoobleFetcher;
use App\Services\RssFeed\ApiFetchers\CareerjetFetcher;
use App\Models\JobFeedSource;
use Illuminate\Console\Command;

class TestJobApi extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'job-api:test
        {api : The API to test (adzuna, jooble, careerjet)}
        {--country=US : Country code to fetch jobs for}
        {--limit=5 : Number of jobs to fetch}';

    /**
     * The console command description.
     */
    protected $description = 'Test job API fetchers without saving to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $api = strtolower($this->argument('api'));
        $countryCode = strtoupper($this->option('country'));
        $limit = (int) $this->option('limit');

        $this->info("Testing {$api} API for country: {$countryCode}");
        $this->newLine();

        // Create a mock JobFeedSource
        $source = new JobFeedSource([
            'id' => 0,
            'name' => "Test {$api}",
            'country_code' => $countryCode,
            'max_items_per_fetch' => $limit,
            'feed_format' => "api_{$api}",
        ]);

        try {
            $fetcher = match ($api) {
                'adzuna' => app(AdzunaFetcher::class),
                'jooble' => app(JoobleFetcher::class),
                'careerjet' => app(CareerjetFetcher::class),
                default => throw new \Exception("Unknown API: {$api}. Use: adzuna, jooble, or careerjet"),
            };

            $this->info("Fetching up to {$limit} jobs...");
            $startTime = microtime(true);

            $items = $fetcher->fetch($source, $limit);

            $duration = round((microtime(true) - $startTime) * 1000);

            if (empty($items)) {
                $this->warn("No jobs returned. Check your API credentials in .env file.");
                $this->newLine();
                $this->showCredentialHelp($api);
                return Command::FAILURE;
            }

            $this->info("Fetched " . count($items) . " jobs in {$duration}ms");
            $this->newLine();

            // Display jobs in a table
            $tableData = [];
            foreach ($items as $index => $item) {
                $tableData[] = [
                    $index + 1,
                    $this->truncate($item['title'] ?? 'N/A', 40),
                    $this->truncate($item['company'] ?? 'N/A', 25),
                    $item['country'] ?? 'N/A',
                    $this->truncate($item['location'] ?? 'N/A', 25),
                    $item['salary'] ?? 'N/A',
                ];
            }

            $this->table(
                ['#', 'Title', 'Company', 'Country', 'Location', 'Salary'],
                $tableData
            );

            $this->newLine();
            $this->info("First job details:");
            $this->line("  ID: " . ($items[0]['id'] ?? 'N/A'));
            $this->line("  URL: " . ($items[0]['url'] ?? 'N/A'));
            $this->line("  Published: " . ($items[0]['published_at'] ?? 'N/A'));
            $this->line("  Description: " . $this->truncate(strip_tags($items[0]['description'] ?? ''), 200));

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("API Error: " . $e->getMessage());
            $this->newLine();
            $this->showCredentialHelp($api);
            return Command::FAILURE;
        }
    }

    /**
     * Truncate a string to a maximum length.
     */
    protected function truncate(string $text, int $maxLength): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        return mb_substr($text, 0, $maxLength - 3) . '...';
    }

    /**
     * Show help for setting up API credentials.
     */
    protected function showCredentialHelp(string $api): void
    {
        $this->warn("Make sure you have configured your API credentials in .env:");
        $this->newLine();

        switch ($api) {
            case 'adzuna':
                $this->line("  1. Register at: https://developer.adzuna.com/signup");
                $this->line("  2. Create an Application");
                $this->line("  3. Add to .env:");
                $this->line("     ADZUNA_APP_ID=your_app_id");
                $this->line("     ADZUNA_APP_KEY=your_app_key");
                $this->newLine();
                $this->line("  Supported countries: GB, US, AU, AT, BR, CA, DE, FR, IN, IT, NL, NZ, PL, SG, ZA");
                break;

            case 'jooble':
                $this->line("  1. Register at: https://jooble.org/api/about");
                $this->line("  2. Get your API key via email");
                $this->line("  3. Add to .env:");
                $this->line("     JOOBLE_API_KEY=your_api_key");
                $this->newLine();
                $this->line("  Supports 71+ countries including African nations");
                break;

            case 'careerjet':
                $this->line("  1. Register at: https://www.careerjet.com/partners/");
                $this->line("  2. Get your Affiliate ID");
                $this->line("  3. Add to .env:");
                $this->line("     CAREERJET_AFFILIATE_ID=your_affiliate_id");
                $this->newLine();
                $this->line("  Supports 90+ countries");
                break;
        }
    }
}
