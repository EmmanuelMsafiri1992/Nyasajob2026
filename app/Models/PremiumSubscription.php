<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class PremiumSubscription extends BaseModel
{
    protected $fillable = [
        'user_id',
        'plan_type',
        'amount',
        'currency',
        'paypal_subscription_id',
        'paypal_payer_id',
        'paypal_payer_email',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'auto_renew',
        'terms_accepted',
        'terms_accepted_at',
        'terms_accepted_ip',
        'cancellation_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'auto_renew' => 'boolean',
        'terms_accepted' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Plan types
     */
    const PLAN_JOB_SEEKER_PREMIUM = 'job_seeker_premium';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job seeker preferences for this subscription
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(JobSeekerPreference::class, 'user_id', 'user_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->expires_at
            && $this->expires_at->isFuture();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if subscription is pending activation
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Days remaining until expiry
     */
    public function daysRemaining(): int
    {
        if (!$this->expires_at || $this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Activate the subscription
     */
    public function activate(): bool
    {
        $this->status = self::STATUS_ACTIVE;
        $this->starts_at = now();
        $this->expires_at = now()->addMonth();

        return $this->save();
    }

    /**
     * Renew the subscription for another month
     */
    public function renew(): bool
    {
        $startDate = $this->expires_at && $this->expires_at->isFuture()
            ? $this->expires_at
            : now();

        $this->expires_at = $startDate->copy()->addMonth();
        $this->status = self::STATUS_ACTIVE;

        return $this->save();
    }

    /**
     * Cancel the subscription
     */
    public function cancel(?string $reason = null): bool
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->auto_renew = false;
        $this->cancellation_reason = $reason;

        return $this->save();
    }

    /**
     * Mark subscription as expired
     */
    public function markExpired(): bool
    {
        $this->status = self::STATUS_EXPIRED;

        return $this->save();
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_EXPIRED]);
    }

    /**
     * Scope for subscriptions expiring soon (within 7 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereBetween('expires_at', [now(), now()->addDays(7)]);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->amount, 2) . '/month';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_CANCELLED => 'bg-secondary',
            self::STATUS_EXPIRED => 'bg-danger',
            self::STATUS_SUSPENDED => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_SUSPENDED => 'Suspended',
            default => 'Unknown',
        };
    }

    /**
     * Check if user has accepted terms
     */
    public function hasAcceptedTerms(): bool
    {
        return $this->terms_accepted && $this->terms_accepted_at;
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL HTML HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Get user HTML for admin panel
     */
    public function getUserHtml(): string
    {
        $user = $this->user;
        if (!$user) {
            return 'N/A';
        }

        $url = admin_url('users/' . $user->id . '/edit');
        return '<a href="' . $url . '">' . e($user->name) . '</a><br><small>' . e($user->email) . '</small>';
    }

    /**
     * Get amount HTML for admin panel
     */
    public function getAmountHtml(): string
    {
        return '$' . number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get status HTML for admin panel
     */
    public function getStatusHtml(): string
    {
        $class = $this->statusBadgeClass;
        return '<span class="badge ' . $class . '">' . $this->statusLabel . '</span>';
    }
}
