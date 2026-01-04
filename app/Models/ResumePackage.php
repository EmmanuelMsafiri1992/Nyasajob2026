<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\Traits\Common\HasActiveColumn;

class ResumePackage extends Model
{
    use Crud, HasActiveColumn;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_code',
        'credits',
        'validity_days',
        'unlimited_search',
        'export_allowed',
        'is_featured',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'credits' => 'integer',
        'validity_days' => 'integer',
        'unlimited_search' => 'boolean',
        'export_allowed' => 'boolean',
        'is_featured' => 'boolean',
        'active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function resumeCredits(): HasMany
    {
        return $this->hasMany(ResumeCredit::class);
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedPriceAttribute(): string
    {
        $symbol = $this->currency_code === 'USD' ? '$' : $this->currency_code . ' ';
        return $symbol . number_format($this->price, 2);
    }

    public function getPricePerCreditAttribute(): float
    {
        if ($this->credits <= 0) return 0;
        return round($this->price / $this->credits, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL
    |--------------------------------------------------------------------------
    */

    public function getActiveBadge(): string
    {
        return $this->active
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }

    public function getFeaturedBadge(): string
    {
        return $this->is_featured
            ? '<span class="badge bg-warning">Featured</span>'
            : '';
    }
}
