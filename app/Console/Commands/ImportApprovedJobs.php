<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobFeedStagedItem;
use App\Services\RssFeed\JobImportService;

class ImportApprovedJobs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rss:import
        {--limit=50 : Maximum items to import}
        {--auto-approve : Also import pending items from auto-approve sources}
        {--source= : Specific feed source ID}
        {--country= : Filter by country code}
        {--dry-run : Show what would be imported without actually importing}';

    /**
     * The console command description.
     */
    protected $description = 'Import approved/ready staged jobs into the posts table';

    /**
     * Execute the console command.
     */
    public function handle(JobImportService $importer): int
    {
        $this->info('Importing staged jobs to posts...');

        $query = JobFeedStagedItem::query()
            ->whereNotNull('cleaned_description')
            ->whereNotNull('city_id')
            ->orderBy('published_at', 'desc');

        // Status filter
        if ($this->option('auto-approve')) {
            $query->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhere(function ($sq) {
                        $sq->where('status', 'pending')
                            ->whereHas('feedSource', fn($s) => $s->where('auto_approve', true));
                    });
            });
        } else {
            $query->where('status', 'approved');
        }

        if ($source = $this->option('source')) {
            $query->where('feed_source_id', $source);
        }

        if ($country = $this->option('country')) {
            $query->where('country_code', strtoupper($country));
        }

        $items = $query->limit($this->option('limit'))->get();

        if ($items->isEmpty()) {
            $this->info('No items ready for import.');
            return Command::SUCCESS;
        }

        $this->info("Found {$items->count()} item(s) ready for import...");

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN - No actual imports will be made');
            $this->newLine();

            $this->table(
                ['ID', 'Title', 'Company', 'Country', 'Status'],
                $items->map(fn($item) => [
                    $item->id,
                    substr($item->title, 0, 40) . (strlen($item->title) > 40 ? '...' : ''),
                    substr($item->company_name ?? 'N/A', 0, 20),
                    $item->country_code,
                    $item->status,
                ])->toArray()
            );

            return Command::SUCCESS;
        }

        $this->newLine();

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                $post = $importer->importStagedItem($item);

                if ($post) {
                    $success++;
                    $this->line("<fg=green>✓</> Imported: {$item->title} -> Post #{$post->id}");
                } else {
                    $failed++;
                    $this->line("<fg=yellow>⚠</> Skipped: {$item->title} (validation failed)");
                }
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Item {$item->id}: {$e->getMessage()}";
                $this->line("<fg=red>✗</> Failed: {$item->title} - {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Complete: <fg=green>{$success} imported</>, <fg=red>{$failed} failed</>");

        return $failed > 0 && $success === 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
