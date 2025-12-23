<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'interval',
        'interval_count',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get subscriptions for this plan
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get active subscriptions
     */
    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('status', 'active');
    }

    /**
     * Scope to get active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get popular plans
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get interval label
     */
    public function getIntervalLabelAttribute()
    {
        $labels = [
            'daily' => 'day',
            'weekly' => 'week',
            'monthly' => 'month',
            'yearly' => 'year',
        ];

        return $labels[$this->interval] ?? $this->interval;
    }
}
