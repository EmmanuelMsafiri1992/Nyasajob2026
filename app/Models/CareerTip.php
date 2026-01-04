<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;

class CareerTip extends Model
{
    use Sluggable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'featured_image',
        'reading_time',
        'views',
        'is_featured',
        'active',
        'meta_tags',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'active' => 'boolean',
        'meta_tags' => 'array',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Available categories
     */
    public const CATEGORIES = [
        'general' => 'General Career Advice',
        'cv' => 'CV & Resume Tips',
        'interview' => 'Interview Preparation',
        'job-search' => 'Job Search Strategies',
        'career-growth' => 'Career Growth',
        'workplace' => 'Workplace Skills',
        'remote-work' => 'Remote Work',
    ];

    /**
     * Scope for active tips
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope for featured tips
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Get related tips
     */
    public function relatedTips(int $limit = 4)
    {
        return static::active()
            ->where('id', '!=', $this->id)
            ->where('category', $this->category)
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate reading time from content
     */
    public static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = ceil($wordCount / 200); // Average reading speed
        return max(1, $readingTime);
    }

    /**
     * Get featured image URL
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        if ($this->featured_image) {
            return url('storage/' . $this->featured_image);
        }
        return url('images/career-tips-default.jpg');
    }
}
