<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InteractiveStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'step_number',
        'title',
        'instruction',
        'action_type',
        'target_element',
        'action_data',
        'validation_rules',
        'hint',
        'points',
        'is_required',
        'timeout_seconds',
    ];

    protected $casts = [
        'action_data' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'points' => 'integer',
        'step_number' => 'integer',
        'timeout_seconds' => 'integer',
    ];

    /**
     * Action types available for interactive steps
     */
    public const ACTION_TYPES = [
        'click' => 'Click',
        'double_click' => 'Double Click',
        'right_click' => 'Right Click',
        'type' => 'Type Text',
        'drag' => 'Drag & Drop',
        'open_app' => 'Open Application',
        'close_window' => 'Close Window',
        'minimize_window' => 'Minimize Window',
        'maximize_window' => 'Maximize Window',
        'navigate' => 'Navigate/Browse',
        'create_file' => 'Create File',
        'create_folder' => 'Create Folder',
        'rename' => 'Rename',
        'delete' => 'Delete',
        'copy' => 'Copy',
        'paste' => 'Paste',
        'select' => 'Select Item',
    ];

    /**
     * Get the lesson that owns this step
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }

    /**
     * Get the progress records for this step
     */
    public function progress(): HasMany
    {
        return $this->hasMany(InteractiveStepProgress::class, 'step_id');
    }

    /**
     * Get user's progress for this step
     */
    public function userProgress($userId)
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    /**
     * Check if step is completed by user
     */
    public function isCompletedBy($userId): bool
    {
        $progress = $this->userProgress($userId);
        return $progress && $progress->completed;
    }

    /**
     * Scope to get steps in order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_number', 'asc');
    }

    /**
     * Get the next step in the lesson
     */
    public function getNextStep()
    {
        return static::where('lesson_id', $this->lesson_id)
            ->where('step_number', '>', $this->step_number)
            ->orderBy('step_number', 'asc')
            ->first();
    }

    /**
     * Get the previous step in the lesson
     */
    public function getPreviousStep()
    {
        return static::where('lesson_id', $this->lesson_id)
            ->where('step_number', '<', $this->step_number)
            ->orderBy('step_number', 'desc')
            ->first();
    }

    /**
     * Get action type label
     */
    public function getActionTypeLabelAttribute(): string
    {
        return self::ACTION_TYPES[$this->action_type] ?? $this->action_type;
    }

    /**
     * Admin panel: Action type badge
     */
    public function getActionTypeBadge(): string
    {
        $colors = [
            'click' => 'primary',
            'double_click' => 'info',
            'right_click' => 'secondary',
            'type' => 'success',
            'drag' => 'warning',
            'open_app' => 'primary',
            'close_window' => 'danger',
        ];
        $color = $colors[$this->action_type] ?? 'secondary';
        $label = self::ACTION_TYPES[$this->action_type] ?? $this->action_type;

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Admin panel: Required badge
     */
    public function getRequiredBadge(): string
    {
        if ($this->is_required) {
            return '<span class="badge bg-danger">Required</span>';
        }
        return '<span class="badge bg-secondary">Optional</span>';
    }

    /**
     * Admin panel: Bulk deletion button
     */
    public function bulkDeletionBtn($xPanel = false): string
    {
        $url = admin_url('lessons/' . $this->lesson_id . '/steps/bulk_actions');
        return '<a href="' . $url . '" class="btn btn-danger shadow bulk-action" data-action="deletion">
            <i class="fa fa-trash"></i> Delete
        </a>';
    }

    /**
     * Admin panel: Preview lesson button
     */
    public function previewLessonBtn($xPanel = false): string
    {
        $lesson = $this->lesson;
        if (!$lesson) {
            return '';
        }

        $course = $lesson->module->course ?? null;
        if (!$course) {
            return '';
        }

        $url = url('courses/' . $course->slug . '/lessons/' . $lesson->id . '/interactive');
        return '<a href="' . $url . '" class="btn btn-primary shadow" target="_blank">
            <i class="fa fa-eye"></i> Preview Lesson
        </a>';
    }
}
