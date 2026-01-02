<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobFeedStagedItem extends Model
{
    use Crud;

    protected $table = 'job_feed_staged_items';

    protected $fillable = [
        'feed_source_id',
        'external_id',
        'external_url',
        'title',
        'raw_description',
        'cleaned_description',
        'company_name',
        'company_logo_url',
        'location_raw',
        'country_code',
        'city_id',
        'category_id',
        'salary_min',
        'salary_max',
        'currency_code',
        'tags',
        'contact_email',
        'application_url',
        'published_at',
        'status',
        'rejection_reason',
        'imported_post_id',
        'checksum',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'published_at' => 'datetime',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function feedSource(): BelongsTo
    {
        return $this->belongsTo(JobFeedSource::class, 'feed_source_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function importedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'imported_post_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeImported($query)
    {
        return $query->where('status', 'imported');
    }

    public function scopeReadyForImport($query)
    {
        return $query->whereIn('status', ['pending', 'approved'])
            ->whereNotNull('cleaned_description')
            ->whereNotNull('city_id');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    public function scopeNotExpired($query)
    {
        return $query->where('published_at', '>=', now()->subDays(7));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeHtmlAttribute(): string
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'imported' => 'success',
            'expired' => 'secondary',
        ];
        $color = $colors[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$color}'>" . ucfirst($this->status) . "</span>";
    }

    public function getSalaryRangeAttribute(): ?string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return null;
        }

        $currency = $this->currency_code ?? 'USD';

        if ($this->salary_min && $this->salary_max) {
            return "{$currency} " . number_format($this->salary_min) . ' - ' . number_format($this->salary_max);
        }

        if ($this->salary_min) {
            return "{$currency} " . number_format($this->salary_min) . '+';
        }

        return "Up to {$currency} " . number_format($this->salary_max);
    }

    public function getDescriptionPreviewAttribute(): string
    {
        $text = $this->cleaned_description ?? $this->raw_description;
        $text = strip_tags($text);
        return mb_strlen($text) > 200 ? mb_substr($text, 0, 200) . '...' : $text;
    }

    public function getSourceNameAttribute(): string
    {
        return $this->feedSource?->name ?? 'Unknown';
    }

    public function getPublishedAgoAttribute(): string
    {
        if (!$this->published_at) {
            return 'Unknown';
        }
        return $this->published_at->diffForHumans();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public static function generateChecksum(string $title, string $description, ?string $companyName = null): string
    {
        $content = mb_strtolower(trim($title) . '|' . trim($description) . '|' . trim($companyName ?? ''));
        return hash('sha256', $content);
    }

    public function isReadyForImport(): bool
    {
        return !empty($this->title)
            && !empty($this->cleaned_description)
            && mb_strlen(strip_tags($this->cleaned_description)) >= 50
            && !empty($this->city_id);
    }

    public function approve(): bool
    {
        return $this->update(['status' => 'approved']);
    }

    public function reject(?string $reason = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsImported(int $postId): bool
    {
        return $this->update([
            'status' => 'imported',
            'imported_post_id' => $postId,
        ]);
    }

    public function markAsExpired(): bool
    {
        return $this->update(['status' => 'expired']);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL METHODS
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeHtml(): string
    {
        return $this->status_badge_html;
    }

    public function getSourceName(): string
    {
        return $this->source_name;
    }

    public function importButton(): string
    {
        if ($this->status === 'imported') {
            return '<span class="text-muted">Imported</span>';
        }

        if (!$this->isReadyForImport()) {
            return '<span class="text-muted" title="Not ready - needs processing">N/A</span>';
        }

        $url = admin_url('job-feeds/staged/' . $this->id . '/import');
        return '<a href="' . $url . '" class="btn btn-sm btn-success" title="Import to Posts"><i class="la la-upload"></i></a>';
    }

    public function processButton(): string
    {
        if ($this->status === 'imported') {
            return '';
        }

        $url = admin_url('job-feeds/staged/' . $this->id . '/process');
        return '<a href="' . $url . '" class="btn btn-sm btn-info" title="Process/Clean"><i class="la la-magic"></i></a>';
    }
}
