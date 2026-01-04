<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class JobSeekerPreference extends BaseModel
{
    protected $fillable = [
        'user_id',
        // Job preferences
        'desired_job_title',
        'job_keywords',
        'preferred_categories',
        'preferred_job_types',
        'preferred_locations',
        'remote_only',
        // Salary
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_period',
        // Urgency
        'urgency_level',
        'available_from',
        'availability_notes',
        // Experience
        'experience_level',
        'years_of_experience',
        'key_skills',
        'qualifications',
        'languages',
        // CV
        'cv_summary',
        'career_goals',
        'cv_file_path',
        'cv_last_updated',
        // Alerts
        'email_alerts',
        'alert_frequency',
        'max_alerts_per_day',
        // Tracking
        'job_matches_count',
        'cv_reviews_count',
        'interview_tips_viewed',
        'last_job_match_at',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'preferred_job_types' => 'array',
        'preferred_locations' => 'array',
        'remote_only' => 'boolean',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'available_from' => 'date',
        'years_of_experience' => 'integer',
        'email_alerts' => 'boolean',
        'max_alerts_per_day' => 'integer',
        'job_matches_count' => 'integer',
        'cv_reviews_count' => 'integer',
        'interview_tips_viewed' => 'integer',
        'cv_last_updated' => 'datetime',
        'last_job_match_at' => 'datetime',
    ];

    /**
     * Urgency levels
     */
    const URGENCY_NOT_URGENT = 'not_urgent';
    const URGENCY_WITHIN_MONTH = 'within_month';
    const URGENCY_WITHIN_WEEK = 'within_week';
    const URGENCY_IMMEDIATE = 'immediate';

    /**
     * Experience levels
     */
    const EXP_ENTRY = 'entry';
    const EXP_JUNIOR = 'junior';
    const EXP_MID = 'mid';
    const EXP_SENIOR = 'senior';
    const EXP_EXECUTIVE = 'executive';

    /**
     * Alert frequencies
     */
    const ALERT_INSTANT = 'instant';
    const ALERT_DAILY = 'daily';
    const ALERT_WEEKLY = 'weekly';

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active premium subscription for this user
     */
    public function getActiveSubscriptionAttribute()
    {
        return PremiumSubscription::where('user_id', $this->user_id)
            ->active()
            ->first();
    }

    /**
     * Check if user has active premium subscription
     */
    public function hasPremiumAccess(): bool
    {
        return $this->activeSubscription !== null;
    }

    /**
     * Get urgency levels for dropdown
     */
    public static function getUrgencyLevels(): array
    {
        return [
            self::URGENCY_NOT_URGENT => 'Not Urgent - Just Exploring',
            self::URGENCY_WITHIN_MONTH => 'Within a Month',
            self::URGENCY_WITHIN_WEEK => 'Within a Week - Actively Looking',
            self::URGENCY_IMMEDIATE => 'Immediate - Desperately Need a Job',
        ];
    }

    /**
     * Get experience levels for dropdown
     */
    public static function getExperienceLevels(): array
    {
        return [
            self::EXP_ENTRY => 'Entry Level (0-1 years)',
            self::EXP_JUNIOR => 'Junior (1-3 years)',
            self::EXP_MID => 'Mid-Level (3-5 years)',
            self::EXP_SENIOR => 'Senior (5-10 years)',
            self::EXP_EXECUTIVE => 'Executive (10+ years)',
        ];
    }

    /**
     * Get alert frequencies for dropdown
     */
    public static function getAlertFrequencies(): array
    {
        return [
            self::ALERT_INSTANT => 'Instant - As soon as a match is found',
            self::ALERT_DAILY => 'Daily Digest',
            self::ALERT_WEEKLY => 'Weekly Summary',
        ];
    }

    /**
     * Get keywords as array
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->job_keywords)) {
            return [];
        }

        return array_map('trim', explode(',', $this->job_keywords));
    }

    /**
     * Get skills as array
     */
    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->key_skills)) {
            return [];
        }

        return array_map('trim', explode(',', $this->key_skills));
    }

    /**
     * Get languages as array
     */
    public function getLanguagesArrayAttribute(): array
    {
        if (empty($this->languages)) {
            return [];
        }

        return array_map('trim', explode(',', $this->languages));
    }

    /**
     * Increment job matches count
     */
    public function incrementJobMatches(int $count = 1): void
    {
        $this->increment('job_matches_count', $count);
        $this->update(['last_job_match_at' => now()]);
    }

    /**
     * Increment CV reviews count
     */
    public function incrementCvReviews(): void
    {
        $this->increment('cv_reviews_count');
    }

    /**
     * Increment interview tips viewed
     */
    public function incrementInterviewTips(): void
    {
        $this->increment('interview_tips_viewed');
    }

    /**
     * Get urgency label
     */
    public function getUrgencyLabelAttribute(): string
    {
        $levels = self::getUrgencyLevels();
        return $levels[$this->urgency_level] ?? 'Unknown';
    }

    /**
     * Get experience label
     */
    public function getExperienceLabelAttribute(): string
    {
        $levels = self::getExperienceLevels();
        return $levels[$this->experience_level] ?? 'Unknown';
    }

    /**
     * Get completion percentage of profile
     */
    public function getProfileCompletionAttribute(): int
    {
        $fields = [
            'desired_job_title',
            'job_keywords',
            'preferred_categories',
            'urgency_level',
            'experience_level',
            'years_of_experience',
            'key_skills',
            'qualifications',
            'cv_summary',
            'career_goals',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    /**
     * Build job search query based on preferences
     */
    public function buildJobSearchQuery(): Builder
    {
        $query = Post::query()
            ->verified()
            ->whereNotNull('title');

        // Title match
        if (!empty($this->desired_job_title)) {
            $query->where('title', 'LIKE', '%' . $this->desired_job_title . '%');
        }

        // Keyword match
        if (!empty($this->job_keywords)) {
            $keywords = $this->keywordsArray;
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('title', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        // Category match
        if (!empty($this->preferred_categories)) {
            $query->whereIn('category_id', $this->preferred_categories);
        }

        // Job type match
        if (!empty($this->preferred_job_types)) {
            $query->whereIn('post_type_id', $this->preferred_job_types);
        }

        // Salary range
        if (!empty($this->min_salary)) {
            $query->where(function ($q) {
                $q->whereNull('salary_min')
                  ->orWhere('salary_min', '>=', $this->min_salary);
            });
        }

        if (!empty($this->max_salary)) {
            $query->where(function ($q) {
                $q->whereNull('salary_max')
                  ->orWhere('salary_max', '<=', $this->max_salary);
            });
        }

        return $query->orderByDesc('created_at');
    }
}
