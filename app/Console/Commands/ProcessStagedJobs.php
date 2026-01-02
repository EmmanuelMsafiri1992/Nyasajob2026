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
        {--country= : Filter by country code}';

    /**
     * The console command description.
     */
    protected $description = 'Clean and process staged RSS feed items (resolve locations, infer categories, format descriptions)';

    /**
     * Execute the console command.
     */
    public function handle(JobDataCleanerService $cleaner): int
    {
        $this->info('Processing staged job items...');

        $query = JobFeedStagedItem::query()
            ->where('status', $this->option('status'))
            ->whereNull('cleaned_description')
            ->orderBy('published_at', 'desc');

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
}
