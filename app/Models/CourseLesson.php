<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'content',
        'type',
        'video_url',
        'duration_minutes',
        'order',
        'is_free_preview',
        'interactive_config',
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'interactive_config' => 'array',
    ];

    /**
     * Lesson types
     */
    public const TYPES = [
        'text' => 'Text',
        'video' => 'Video',
        'quiz' => 'Quiz',
        'exercise' => 'Exercise',
        'interactive' => 'Interactive Simulation',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(LessonExercise::class, 'lesson_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }

    /**
     * Get the interactive steps for this lesson
     */
    public function interactiveSteps(): HasMany
    {
        return $this->hasMany(InteractiveStep::class, 'lesson_id')->orderBy('step_number', 'asc');
    }

    /**
     * Get the interactive step progress for this lesson
     */
    public function interactiveProgress(): HasMany
    {
        return $this->hasMany(InteractiveStepProgress::class, 'lesson_id');
    }

    /**
     * Get the desktop configuration for this lesson
     */
    public function desktopConfig(): HasOne
    {
        return $this->hasOne(DesktopConfig::class, 'lesson_id');
    }

    /**
     * Check if this is an interactive lesson
     */
    public function isInteractive(): bool
    {
        return $this->type === 'interactive';
    }

    /**
     * Get or create desktop config
     */
    public function getOrCreateDesktopConfig(): DesktopConfig
    {
        if (!$this->desktopConfig) {
            return DesktopConfig::createDefault($this->id);
        }
        return $this->desktopConfig;
    }

    /**
     * Get user's interactive progress percentage
     */
    public function getInteractiveProgressPercentage($userId): float
    {
        return InteractiveStepProgress::getCompletionPercentage($userId, $this->id);
    }

    /**
     * Get total points available in this lesson
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->interactiveSteps()->sum('points');
    }

    /**
     * Get user's earned points
     */
    public function getUserPoints($userId): int
    {
        return InteractiveStepProgress::getTotalPointsForLesson($userId, $this->id);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Scope to get only interactive lessons
     */
    public function scopeInteractive($query)
    {
        return $query->where('type', 'interactive');
    }

    /**
     * Admin panel: Type badge
     */
    public function getTypeBadge(): string
    {
        $colors = [
            'text' => 'secondary',
            'video' => 'primary',
            'quiz' => 'warning',
            'exercise' => 'info',
            'interactive' => 'success',
        ];
        $icons = [
            'text' => 'file-text',
            'video' => 'play-circle',
            'quiz' => 'question-circle',
            'exercise' => 'code',
            'interactive' => 'desktop',
        ];
        $color = $colors[$this->type] ?? 'secondary';
        $icon = $icons[$this->type] ?? 'file';
        $label = self::TYPES[$this->type] ?? ucfirst($this->type);

        return '<span class="badge bg-' . $color . '"><i class="fa fa-' . $icon . '"></i> ' . $label . '</span>';
    }

    /**
     * Admin panel: Free preview badge
     */
    public function getFreePreviewBadge(): string
    {
        if ($this->is_free_preview) {
            return '<span class="badge bg-success">Yes</span>';
        }
        return '<span class="badge bg-secondary">No</span>';
    }

    /**
     * Admin panel: Interactive steps button
     */
    public function interactiveStepsBtn($xPanel = false): string
    {
        if ($this->type !== 'interactive') {
            return '';
        }

        $url = admin_url('lessons/' . $this->id . '/steps');
        $count = $this->interactiveSteps()->count();

        return '<a class="btn btn-xs btn-success" href="' . $url . '">
            <i class="fa fa-list-ol"></i> Steps (' . $count . ')
        </a>';
    }

    /**
     * Admin panel: Bulk deletion button
     */
    public function bulkDeletionBtn($xPanel = false): string
    {
        $url = admin_url('modules/' . $this->module_id . '/lessons/bulk_actions');
        return '<a href="' . $url . '" class="btn btn-danger shadow bulk-action" data-action="deletion">
            <i class="fa fa-trash"></i> Delete
        </a>';
    }
}
