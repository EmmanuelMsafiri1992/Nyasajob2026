<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobFeedLog extends Model
{
    use Crud;

    protected $table = 'job_feed_logs';

    protected $fillable = [
        'feed_source_id',
        'status',
        'items_found',
        'items_new',
        'items_duplicate',
        'items_failed',
        'duration_ms',
        'error_message',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'items_found' => 'integer',
        'items_new' => 'integer',
        'items_duplicate' => 'integer',
        'items_failed' => 'integer',
        'duration_ms' => 'integer',
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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeHtmlAttribute(): string
    {
        $colors = [
            'success' => 'success',
            'partial' => 'warning',
            'failed' => 'danger',
        ];
        $color = $colors[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$color}'>" . ucfirst($this->status) . "</span>";
    }

    public function getDurationFormattedAttribute(): string
    {
        if ($this->duration_ms < 1000) {
            return $this->duration_ms . 'ms';
        }
        return round($this->duration_ms / 1000, 2) . 's';
    }

    public function getSourceNameAttribute(): string
    {
        return $this->feedSource?->name ?? 'Unknown';
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

    public function getDurationFormatted(): string
    {
        return $this->duration_formatted;
    }
}
