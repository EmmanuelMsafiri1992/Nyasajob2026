<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupPostsWithoutApplicationDetails extends Command
{
    protected $signature = 'posts:cleanup-no-apply
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--limit=500 : Maximum number of posts to process}';

    protected $description = 'Delete posts that have no way for users to apply (no URL, email, phone, or address)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $this->info($dryRun ? 'DRY RUN - No posts will be deleted' : 'Cleaning up posts without application details...');

        // Only clean up RSS aggregator posts (don't touch user-submitted posts)
        $posts = Post::where('partner', 'rss_aggregator')
            ->where(function ($query) {
                $query->whereNull('application_url')
                    ->orWhere('application_url', '');
            })
            ->limit($limit)
            ->get();

        $deleted = 0;
        $kept = 0;

        foreach ($posts as $post) {
            if ($this->hasContactInfo($post)) {
                $kept++;
                continue;
            }

            if ($dryRun) {
                $this->line("Would delete: [{$post->id}] {$post->title}");
            } else {
                $post->delete();
                $this->line("Deleted: [{$post->id}] {$post->title}");
            }
            $deleted++;
        }

        $action = $dryRun ? 'Would delete' : 'Deleted';
        $this->info("{$action} {$deleted} posts without application details. Kept {$kept} posts with contact info in description.");

        if (!$dryRun && $deleted > 0) {
            Log::info("Cleaned up {$deleted} posts without application details");
        }

        return Command::SUCCESS;
    }

    /**
     * Check if post has any contact information
     */
    protected function hasContactInfo(Post $post): bool
    {
        // Check direct fields
        if (!empty($post->application_url) && filter_var($post->application_url, FILTER_VALIDATE_URL)) {
            return true;
        }

        if (!empty($post->email) && filter_var($post->email, FILTER_VALIDATE_EMAIL)) {
            // Skip the default aggregator email
            if ($post->email !== 'info@nyasajob.com') {
                return true;
            }
        }

        if (!empty($post->phone)) {
            return true;
        }

        // Check description for contact info
        return $this->descriptionHasContactInfo($post->description ?? '');
    }

    /**
     * Check if description contains contact information
     */
    protected function descriptionHasContactInfo(string $description): bool
    {
        if (empty($description)) {
            return false;
        }

        $text = strip_tags($description);

        // Check for email addresses
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text)) {
            return true;
        }

        // Check for phone numbers
        if (preg_match('/(?:\+?\d{1,3}[-.\s]?)?\(?\d{2,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{3,4}/', $text)) {
            return true;
        }

        // Check for application/contact patterns
        $patterns = [
            '/apply\s+(at|to|via|through)\s+\S+/i',
            '/send\s+(your\s+)?(cv|resume|application)\s+(to|at)/i',
            '/email\s+(your\s+)?(cv|resume|application)/i',
            '/submit\s+(your\s+)?(cv|resume|application)/i',
            '/contact\s+(us\s+)?(at|on|via)/i',
            '/call\s+(us\s+)?(at|on)/i',
            '/write\s+to\s+us/i',
            '/mail\s+(your\s+)?(cv|resume|application)/i',
            '/P\.?\s*O\.?\s*Box\s*\d+/i',
            '/Post\s*Office\s*Box/i',
            '/Private\s*Bag/i',
            '/postal\s+address/i',
            '/mailing\s+address/i',
            '/street\s+address/i',
            '/office\s+address/i',
            '/physical\s+address/i',
            '/located\s+at/i',
            '/visit\s+(us\s+)?(at|in)/i',
            '/our\s+office(s)?\s+(is|are)\s+(at|in|located)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
