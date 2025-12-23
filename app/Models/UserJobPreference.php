<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJobPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'skills',
        'qualifications',
        'min_salary',
        'max_salary',
        'employment_type',
        'remote_work',
        'auto_apply_enabled',
        'urgency_level',
        'max_applications_per_day',
        'min_match_percentage',
        'cover_letter_template',
        'default_resume_id',
        'total_auto_applications',
        'last_application_at',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'remote_work' => 'boolean',
        'auto_apply_enabled' => 'boolean',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'max_applications_per_day' => 'integer',
        'min_match_percentage' => 'integer',
        'total_auto_applications' => 'integer',
        'last_application_at' => 'datetime',
    ];

    /**
     * Get the user that owns the preference
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default resume
     */
    public function defaultResume()
    {
        return $this->belongsTo(Resume::class, 'default_resume_id');
    }

    /**
     * Get urgency level configuration
     */
    public function getUrgencyConfig()
    {
        $configs = [
            'not_urgent' => [
                'label' => 'Not Urgent',
                'description' => 'I\'m casually looking. Show me matches, I\'ll review and apply manually.',
                'auto_apply_threshold' => 0, // Never auto-apply
                'max_daily_applications' => 0,
                'review_required' => true,
            ],
            'moderate' => [
                'label' => 'Moderately Urgent',
                'description' => 'I need a job soon. Auto-apply to great matches (70%+), show me the rest.',
                'auto_apply_threshold' => 70,
                'max_daily_applications' => 5,
                'review_required' => false,
            ],
            'very_urgent' => [
                'label' => 'Very Urgent',
                'description' => 'I need a job quickly. Auto-apply to good matches (50%+).',
                'auto_apply_threshold' => 50,
                'max_daily_applications' => 10,
                'review_required' => false,
            ],
            'desperate' => [
                'label' => 'Extremely Urgent',
                'description' => 'I need a job immediately. Auto-apply to all reasonable matches (40%+).',
                'auto_apply_threshold' => 40,
                'max_daily_applications' => 20,
                'review_required' => false,
            ],
        ];

        return $configs[$this->urgency_level] ?? $configs['not_urgent'];
    }

    /**
     * Check if user can receive more applications today
     */
    public function canApplyToday()
    {
        if (!$this->last_application_at) {
            return true;
        }

        // Reset counter if last application was yesterday or earlier
        if ($this->last_application_at->isToday()) {
            $todayApplications = JobMatch::where('user_id', $this->user_id)
                ->where('applied', true)
                ->whereDate('applied_at', today())
                ->count();

            return $todayApplications < $this->max_applications_per_day;
        }

        return true;
    }

    /**
     * Get skills as array
     */
    public function getSkillsArray()
    {
        if (empty($this->skills)) {
            return [];
        }

        return array_map('trim', explode(',', $this->skills));
    }

    /**
     * Get qualifications as array
     */
    public function getQualificationsArray()
    {
        if (empty($this->qualifications)) {
            return [];
        }

        return array_map('trim', explode(',', $this->qualifications));
    }
}
