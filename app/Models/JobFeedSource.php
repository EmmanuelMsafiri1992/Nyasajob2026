<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobFeedSource extends Model
{
    use Crud;

    protected $table = 'job_feed_sources';

    protected $fillable = [
        'name',
        'feed_url',
        'country_code',
        'category_id',
        'post_type_id',
        'feed_format',
        'field_mapping',
        'status',
        'fetch_interval_minutes',
        'priority',
        'max_items_per_fetch',
        'rate_limit_delay_ms',
        'last_fetched_at',
        'last_successful_at',
        'consecutive_failures',
        'total_jobs_fetched',
        'total_jobs_imported',
        'notes',
        'auto_approve',
    ];

    protected $casts = [
        'field_mapping' => 'array',
        'last_fetched_at' => 'datetime',
        'last_successful_at' => 'datetime',
        'auto_approve' => 'boolean',
        'priority' => 'integer',
        'fetch_interval_minutes' => 'integer',
        'max_items_per_fetch' => 'integer',
        'rate_limit_delay_ms' => 'integer',
        'consecutive_failures' => 'integer',
        'total_jobs_fetched' => 'integer',
        'total_jobs_imported' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function logs(): HasMany
    {
        return $this->hasMany(JobFeedLog::class, 'feed_source_id');
    }

    public function stagedItems(): HasMany
    {
        return $this->hasMany(JobFeedStagedItem::class, 'feed_source_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function postType(): BelongsTo
    {
        return $this->belongsTo(PostType::class, 'post_type_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDueForFetch($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('last_fetched_at')
                    ->orWhereRaw('DATE_ADD(last_fetched_at, INTERVAL fetch_interval_minutes MINUTE) <= NOW()');
            });
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')
            ->orderBy('last_fetched_at', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeHtmlAttribute(): string
    {
        $colors = [
            'active' => 'success',
            'paused' => 'warning',
            'failed' => 'danger',
            'testing' => 'info',
        ];
        $color = $colors[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$color}'>" . ucfirst($this->status) . "</span>";
    }

    public function getSuccessRateAttribute(): ?float
    {
        if ($this->total_jobs_fetched == 0) {
            return null;
        }
        return round(($this->total_jobs_imported / $this->total_jobs_fetched) * 100, 1);
    }

    public function getSuccessRateHtmlAttribute(): string
    {
        $rate = $this->success_rate;
        if ($rate === null) {
            return '<span class="text-muted">N/A</span>';
        }
        $color = $rate >= 70 ? 'success' : ($rate >= 40 ? 'warning' : 'danger');
        return "<span class='text-{$color}'>{$rate}%</span>";
    }

    public function getLastFetchedAgoAttribute(): string
    {
        if (!$this->last_fetched_at) {
            return 'Never';
        }
        return $this->last_fetched_at->diffForHumans();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isDueForFetch(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (!$this->last_fetched_at) {
            return true;
        }

        return $this->last_fetched_at->addMinutes($this->fetch_interval_minutes)->isPast();
    }

    public function markAsFailed(?string $error = null): void
    {
        $this->increment('consecutive_failures');

        if ($this->consecutive_failures >= 5) {
            $this->update(['status' => 'failed']);
        }
    }

    public function markAsSuccessful(): void
    {
        $this->update([
            'last_fetched_at' => now(),
            'last_successful_at' => now(),
            'consecutive_failures' => 0,
        ]);
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

    public function getSuccessRateHtml(): string
    {
        return $this->success_rate_html;
    }

    public function getCountryNameHtml(): string
    {
        $country = $this->country;
        if (!$country) {
            return '<span class="text-muted">N/A</span>';
        }
        return $country->name;
    }

    public function testFetchButton(): string
    {
        $url = admin_url('job-feeds/sources/' . $this->id . '/test-fetch');
        return '<a href="' . $url . '" class="btn btn-sm btn-info" title="Test Fetch"><i class="la la-sync"></i> Test</a>';
    }
}
