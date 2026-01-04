<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\Traits\Common\HasActiveColumn;

class Coupon extends Model
{
    use Crud, HasActiveColumn;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    public const APPLICABLE_ALL = 'all';
    public const APPLICABLE_PACKAGES = 'packages';
    public const APPLICABLE_COURSES = 'courses';
    public const APPLICABLE_SUBSCRIPTIONS = 'subscriptions';
    public const APPLICABLE_RESUME_PACKAGES = 'resume_packages';

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'currency_code',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'applicable_to',
        'excluded_items',
        'starts_at',
        'expires_at',
        'is_first_order_only',
        'active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'applicable_to' => 'array',
        'excluded_items' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_first_order_only' => 'boolean',
        'active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('times_used', '<', 'usage_limit');
            });
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper(trim($code)));
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        if (!$this->active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) return false;

        return true;
    }

    /**
     * Check if coupon can be used by a specific user
     */
    public function canBeUsedByUser(int $userId): bool
    {
        if (!$this->isValid()) return false;

        // Check per-user usage limit
        $userUsageCount = $this->usages()->where('user_id', $userId)->count();
        if ($userUsageCount >= $this->usage_limit_per_user) return false;

        // Check first order restriction
        if ($this->is_first_order_only) {
            $hasOrders = Payment::where('user_id', $userId)
                ->where('active', 1)
                ->exists();
            if ($hasOrders) return false;
        }

        return true;
    }

    /**
     * Check if coupon applies to a specific type
     */
    public function appliesTo(string $type): bool
    {
        if (empty($this->applicable_to)) return true;
        if (in_array(self::APPLICABLE_ALL, $this->applicable_to)) return true;

        return in_array($type, $this->applicable_to);
    }

    /**
     * Check if item is excluded
     */
    public function isItemExcluded(string $type, int $itemId): bool
    {
        if (empty($this->excluded_items)) return false;

        $key = $type . '_' . $itemId;
        return in_array($key, $this->excluded_items);
    }

    /**
     * Calculate discount for a given amount
     */
    public function calculateDiscount(float $amount): float
    {
        // Check minimum order amount
        if ($this->min_order_amount && $amount < $this->min_order_amount) {
            return 0;
        }

        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            $discount = $amount * ($this->discount_value / 100);

            // Apply max discount cap
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }
        } else {
            $discount = min($this->discount_value, $amount);
        }

        return round($discount, 2);
    }

    /**
     * Record coupon usage
     */
    public function recordUsage(int $userId, float $originalAmount, float $discountAmount, ?int $paymentId = null, ?string $orderType = null, ?int $orderId = null): CouponUsage
    {
        $this->increment('times_used');

        return $this->usages()->create([
            'user_id' => $userId,
            'payment_id' => $paymentId,
            'discount_amount' => $discountAmount,
            'original_amount' => $originalAmount,
            'final_amount' => $originalAmount - $discountAmount,
            'order_type' => $orderType,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Find and validate a coupon by code for a user
     */
    public static function findValidByCode(string $code, int $userId, string $type = 'all', float $amount = 0): ?self
    {
        $coupon = static::valid()->byCode($code)->first();

        if (!$coupon) return null;
        if (!$coupon->canBeUsedByUser($userId)) return null;
        if (!$coupon->appliesTo($type)) return null;
        if ($coupon->min_order_amount && $amount < $coupon->min_order_amount) return null;

        return $coupon;
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getDiscountTextAttribute(): string
    {
        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            return $this->discount_value . '%';
        }
        $symbol = $this->currency_code === 'USD' ? '$' : $this->currency_code . ' ';
        return $symbol . number_format($this->discount_value, 2);
    }

    public function getStatusAttribute(): string
    {
        if (!$this->active) return 'inactive';
        if ($this->expires_at && $this->expires_at->isPast()) return 'expired';
        if ($this->starts_at && $this->starts_at->isFuture()) return 'scheduled';
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) return 'exhausted';
        return 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL
    |--------------------------------------------------------------------------
    */

    public function getStatusBadge(): string
    {
        $badges = [
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'expired' => '<span class="badge bg-danger">Expired</span>',
            'scheduled' => '<span class="badge bg-info">Scheduled</span>',
            'exhausted' => '<span class="badge bg-warning">Exhausted</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getUsageStats(): string
    {
        $limit = $this->usage_limit ? $this->usage_limit : '∞';
        return $this->times_used . ' / ' . $limit;
    }
}
