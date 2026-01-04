<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class QuizResult extends Model
{
    protected $fillable = [
        'result_key',
        'title',
        'description',
        'recommended_categories',
        'traits',
        'icon',
        'active',
    ];

    protected $casts = [
        'recommended_categories' => 'array',
        'traits' => 'array',
        'active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Get recommended job categories
     */
    public function getRecommendedJobCategories()
    {
        if (empty($this->recommended_categories)) {
            return collect();
        }

        return Category::whereIn('id', $this->recommended_categories)
            ->orderBy('name')
            ->get();
    }
}
