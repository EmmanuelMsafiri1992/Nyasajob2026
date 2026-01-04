<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResumeCredit extends Model
{
    protected $fillable = [
        'user_id',
        'resume_package_id',
        'credits_purchased',
        'credits_used',
        'credits_remaining',
        'transaction_id',
        'payment_method',
        'amount_paid',
        'currency_code',
        'expires_at',
    ];

    protected $casts = [
        'credits_purchased' => 'integer',
        'credits_used' => 'integer',
        'credits_remaining' => 'integer',
        'amount_paid' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resumePackage(): BelongsTo
    {
        return $this->belongsTo(ResumePackage::class);
    }

    public function resumeViews(): HasMany
    {
        return $this->hasMany(ResumeView::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('credits_remaining', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    public function hasCredits(): bool
    {
        return $this->credits_remaining > 0 && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function useCredit(): bool
    {
        if (!$this->hasCredits()) {
            return false;
        }

        $this->increment('credits_used');
        $this->decrement('credits_remaining');

        return true;
    }

    /**
     * Get total active credits for a user
     */
    public static function getActiveCreditsForUser(int $userId): int
    {
        return static::active()
            ->forUser($userId)
            ->sum('credits_remaining');
    }

    /**
     * Use a credit from the user's oldest active package
     */
    public static function useCreditForUser(int $userId): ?ResumeCredit
    {
        $credit = static::active()
            ->forUser($userId)
            ->orderBy('expires_at')
            ->orderBy('created_at')
            ->first();

        if ($credit && $credit->useCredit()) {
            return $credit;
        }

        return null;
    }
}
