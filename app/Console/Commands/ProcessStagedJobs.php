<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFeedStagedItem;
use App\Services\RssFeed\JobDataCleanerService;

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
        {--all : Process all items regardless of cleaned_description status}';

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
     * Fix company names by re-parsing from titles
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
            $titleData = $this->parseCompanyFromTitle($item->title);

            if (!empty($titleData['company']) && empty($item->company_name)) {
                $item->company_name = $titleData['company'];
                $item->title = $titleData['title'];
                $item->save();
                $fixed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Fixed <fg=green>{$fixed}</> company names.");

        return Command::SUCCESS;
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
