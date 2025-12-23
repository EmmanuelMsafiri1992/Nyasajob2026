<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'objectives',
        'thumbnail',
        'price',
        'is_free',
        'level',
        'duration_hours',
        'is_published',
        'instructor_id',
        'enrollment_count',
        'rating'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:2'
    ];

    /**
     * Level labels
     */
    public const LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(CourseCertificate::class);
    }

    /**
     * Get all lessons through modules
     */
    public function lessons()
    {
        return $this->hasManyThrough(
            CourseLesson::class,
            CourseModule::class,
            'course_id',
            'module_id'
        );
    }

    /**
     * Admin panel: Level badge HTML
     */
    public function getLevelBadge(): string
    {
        $colors = [
            'beginner' => 'success',
            'intermediate' => 'warning',
            'advanced' => 'danger',
        ];
        $color = $colors[$this->level] ?? 'secondary';
        $label = self::LEVELS[$this->level] ?? ucfirst($this->level);

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Admin panel: Pricing badge HTML
     */
    public function getPricingBadge(): string
    {
        if ($this->is_free) {
            return '<span class="badge bg-success">FREE</span>';
        }
        $currency = config('settings.app.currency_symbol', '$');
        return '<span class="badge bg-primary">' . $currency . number_format($this->price, 2) . '</span>';
    }

    /**
     * Admin panel: Published badge HTML
     */
    public function getPublishedBadge(): string
    {
        if ($this->is_published) {
            return '<span class="badge bg-success">Published</span>';
        }
        return '<span class="badge bg-secondary">Draft</span>';
    }

    /**
     * Admin panel: Modules button
     */
    public function modulesBtn($xPanel = false): string
    {
        $url = admin_url('courses/' . $this->id . '/modules');
        $modulesCount = $this->modules()->count();

        return '<a class="btn btn-xs btn-info" href="' . $url . '">
            <i class="fa fa-list"></i> Modules (' . $modulesCount . ')
        </a>';
    }

    /**
     * Admin panel: Bulk activation button
     */
    public function bulkActivationBtn($xPanel = false): string
    {
        $url = admin_url('courses/bulk_actions');
        return '<a href="' . $url . '" class="btn btn-success shadow bulk-action" data-action="activation">
            <i class="fa fa-toggle-on"></i> Publish
        </a>';
    }

    /**
     * Admin panel: Bulk deactivation button
     */
    public function bulkDeactivationBtn($xPanel = false): string
    {
        $url = admin_url('courses/bulk_actions');
        return '<a href="' . $url . '" class="btn btn-warning shadow bulk-action" data-action="deactivation">
            <i class="fa fa-toggle-off"></i> Unpublish
        </a>';
    }

    /**
     * Admin panel: Bulk deletion button
     */
    public function bulkDeletionBtn($xPanel = false): string
    {
        $url = admin_url('courses/bulk_actions');
        return '<a href="' . $url . '" class="btn btn-danger shadow bulk-action" data-action="deletion">
            <i class="fa fa-trash"></i> Delete
        </a>';
    }

    /**
     * Get total lessons count
     */
    public function getTotalLessonsAttribute(): int
    {
        return $this->lessons()->count();
    }

    /**
     * Check if user is enrolled
     */
    public function isEnrolledBy($userId): bool
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }
}
