<?php

namespace App\Services\RssFeed;

use App\Models\JobFeedStagedItem;
use App\Models\JobFeedSource;
use App\Models\Post;
use App\Models\City;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class JobImportService
{
    protected string $aggregatorEmail = 'info@nyasajob.com';
    protected string $aggregatorContactName = 'NyasaJob';
    protected string $partnerIdentifier = 'rss_aggregator';

    protected ?User $aggregatorUser = null;

    public function __construct()
    {
        // Get or cache the aggregator user
        $this->aggregatorUser = Cache::remember('aggregator_user', 3600, function () {
            return User::where('email', $this->aggregatorEmail)->first();
        });
    }

    /**
     * Import a staged item to the posts table
     */
    public function importStagedItem(JobFeedStagedItem $stagedItem): ?Post
    {
        // Validate before import
        if (!$this->validateStagedItem($stagedItem)) {
            Log::warning("Staged item {$stagedItem->id} failed validation");
            return null;
        }

        try {
            return DB::transaction(function () use ($stagedItem) {
                $source = $stagedItem->feedSource;
                $city = City::find($stagedItem->city_id);

                if (!$city) {
                    Log::warning("No city found for staged item {$stagedItem->id}");
                    return null;
                }

                // Get country info
                $countryCode = $stagedItem->country_code ?? $city->country_code ?? $source->country_code;

                // Get category (use staged item's or source's or default)
                $categoryId = $stagedItem->category_id ?? $source->category_id ?? $this->getDefaultCategoryId();

                // Create the post
                $post = Post::create([
                    'country_code' => $countryCode,
                    'user_id' => $this->aggregatorUser?->id,
                    'category_id' => $categoryId,
                    'post_type_id' => $source->post_type_id ?? 1,
                    'title' => $this->formatTitle($stagedItem->title),
                    'description' => $this->formatDescriptionForSEO($stagedItem),
                    'company_name' => $stagedItem->company_name ?: 'Various Employers',
                    'tags' => $stagedItem->tags,
                    'salary_min' => $stagedItem->salary_min,
                    'salary_max' => $stagedItem->salary_max,
                    'salary_type_id' => 1, // Monthly by default
                    'currency_code' => $stagedItem->currency_code ?? $this->getCurrencyForCountry($countryCode),
                    'application_url' => $stagedItem->application_url ?? $stagedItem->external_url,
                    'contact_name' => $this->aggregatorContactName,
                    'auth_field' => 'email',
                    'email' => $this->aggregatorEmail,
                    'city_id' => $city->id,
                    'lat' => $city->latitude,
                    'lon' => $city->longitude,
                    // Auto-verify aggregated posts
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'reviewed_at' => now(),
                    'partner' => $this->partnerIdentifier,
                    'accept_terms' => true,
                    'create_from_ip' => request()->ip() ?? '127.0.0.1',
                ]);

                // Update staged item
                $stagedItem->markAsImported($post->id);

                // Update source stats
                $source->increment('total_jobs_imported');

                Log::info("Imported job from RSS feed", [
                    'post_id' => $post->id,
                    'staged_id' => $stagedItem->id,
                    'source' => $source->name,
                    'title' => $post->title,
                ]);

                return $post;
            });
        } catch (\Throwable $e) {
            Log::error("Failed to import staged item {$stagedItem->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Bulk import multiple staged items
     */
    public function bulkImport(array $itemIds): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
            'imported_posts' => [],
        ];

        foreach ($itemIds as $id) {
            $item = JobFeedStagedItem::find($id);

            if (!$item) {
                $results['skipped']++;
                continue;
            }

            if (!in_array($item->status, ['pending', 'approved'])) {
                $results['skipped']++;
                continue;
            }

            try {
                $post = $this->importStagedItem($item);

                if ($post) {
                    $results['success']++;
                    $results['imported_posts'][] = $post->id;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Item {$id}: Validation failed";
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                $results['errors'][] = "Item {$id}: {$e->getMessage()}";
            }
        }

        return $results;
    }

    /**
     * Import all ready items (auto-import mode)
     */
    public function importReadyItems(int $limit = 100): array
    {
        $items = JobFeedStagedItem::query()
            ->whereIn('status', ['pending', 'approved'])
            ->whereNotNull('cleaned_description')
            ->whereNotNull('city_id')
            ->whereHas('feedSource', fn($q) => $q->where('auto_approve', true))
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        return $this->bulkImport($items);
    }

    /**
     * Validate a staged item before import
     */
    protected function validateStagedItem(JobFeedStagedItem $item): bool
    {
        // Title is required
        if (empty($item->title) || mb_strlen($item->title) < 3) {
            return false;
        }

        // Description is required and must have minimum length
        $description = $item->cleaned_description ?? $item->raw_description;
        if (empty($description)) {
            return false;
        }

        $textLength = mb_strlen(strip_tags($description));
        if ($textLength < 50) {
            return false;
        }

        // City is required
        if (empty($item->city_id)) {
            return false;
        }

        // Check for duplicate posts
        $existingPost = Post::where('title', $item->title)
            ->where('partner', $this->partnerIdentifier)
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();

        if ($existingPost) {
            return false;
        }

        return true;
    }

    /**
     * Format title for SEO
     */
    protected function formatTitle(string $title): string
    {
        // Remove excessive whitespace
        $title = preg_replace('/\s+/', ' ', trim($title));

        // Convert to title case if all uppercase
        if (mb_strtoupper($title) === $title) {
            $title = mb_convert_case($title, MB_CASE_TITLE);
        }

        // Truncate if too long
        if (mb_strlen($title) > 150) {
            $title = mb_substr($title, 0, 147) . '...';
        }

        return $title;
    }

    /**
     * Format description with SEO enhancements
     */
    protected function formatDescriptionForSEO(JobFeedStagedItem $item): string
    {
        $description = $item->cleaned_description ?? $item->raw_description;

        // If description is already cleaned, return it
        if ($item->cleaned_description) {
            return $description;
        }

        // Basic cleaning for raw description
        $description = strip_tags($description, '<p><br><ul><ol><li><strong><em><b><i>');
        $description = trim($description);

        // Wrap in SEO-friendly structure
        $formatted = '<div class="job-description aggregated-job">';
        $formatted .= $description;

        // Add source attribution
        $formatted .= '<p class="job-source text-muted small mt-3">';
        $formatted .= '<em>This opportunity was aggregated from external career sources. ';
        $formatted .= 'Posted by NyasaJob to connect talent with employers.</em>';
        $formatted .= '</p>';
        $formatted .= '</div>';

        return $formatted;
    }

    /**
     * Get default category ID
     */
    protected function getDefaultCategoryId(): int
    {
        return Cache::remember('default_category_id', 3600, function () {
            $category = Category::where('parent_id', 0)
                ->orWhereNull('parent_id')
                ->first();
            return $category?->id ?? 1;
        });
    }

    /**
     * Get currency code for a country
     */
    protected function getCurrencyForCountry(string $countryCode): string
    {
        $currencies = [
            'MW' => 'MWK',
            'ZA' => 'ZAR',
            'US' => 'USD',
            'GB' => 'GBP',
            'EU' => 'EUR',
            'KE' => 'KES',
            'NG' => 'NGN',
            'GH' => 'GHS',
            'TZ' => 'TZS',
            'UG' => 'UGX',
            'ZM' => 'ZMW',
            'BW' => 'BWP',
        ];

        return $currencies[$countryCode] ?? 'USD';
    }

    /**
     * Set custom aggregator email
     */
    public function setAggregatorEmail(string $email): self
    {
        $this->aggregatorEmail = $email;
        $this->aggregatorUser = User::where('email', $email)->first();
        return $this;
    }

    /**
     * Set custom contact name
     */
    public function setContactName(string $name): self
    {
        $this->aggregatorContactName = $name;
        return $this;
    }
}
