<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'payment_method',
        'transaction_id',
        'amount_paid',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->status === 'active'
            && $this->ends_at
            && $this->ends_at->isFuture();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    /**
     * Scope to get expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<=', now());
    }

    /**
     * Mark subscription as cancelled
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Check if subscription has access to a feature
     */
    public function hasFeature($feature)
    {
        if (!$this->isActive()) {
            return false;
        }

        $features = $this->plan->features ?? [];
        return in_array($feature, $features);
    }
}
