<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveStepProgress extends Model
{
    use HasFactory;

    protected $table = 'interactive_step_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'step_id',
        'completed',
        'completed_at',
        'attempts',
        'time_spent_seconds',
        'points_earned',
        'user_actions',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'attempts' => 'integer',
        'time_spent_seconds' => 'integer',
        'points_earned' => 'integer',
        'user_actions' => 'array',
    ];

    /**
     * Get the user that owns this progress
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson this progress belongs to
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }

    /**
     * Get the step this progress belongs to
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(InteractiveStep::class, 'step_id');
    }

    /**
     * Mark step as completed
     */
    public function markCompleted(int $pointsEarned = null): self
    {
        $this->completed = true;
        $this->completed_at = now();

        if ($pointsEarned !== null) {
            $this->points_earned = $pointsEarned;
        } elseif ($this->step) {
            $this->points_earned = $this->step->points;
        }

        $this->save();

        return $this;
    }

    /**
     * Increment attempt count
     */
    public function incrementAttempts(): self
    {
        $this->increment('attempts');
        return $this;
    }

    /**
     * Add time spent
     */
    public function addTimeSpent(int $seconds): self
    {
        $this->increment('time_spent_seconds', $seconds);
        return $this;
    }

    /**
     * Log a user action
     */
    public function logAction(array $action): self
    {
        $actions = $this->user_actions ?? [];
        $action['timestamp'] = now()->toIso8601String();
        $actions[] = $action;
        $this->user_actions = $actions;
        $this->save();

        return $this;
    }

    /**
     * Get or create progress record for user and step
     */
    public static function getOrCreate(int $userId, int $lessonId, int $stepId): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'step_id' => $stepId,
            ],
            [
                'completed' => false,
                'attempts' => 0,
                'time_spent_seconds' => 0,
                'points_earned' => 0,
            ]
        );
    }

    /**
     * Get total points earned by user in a lesson
     */
    public static function getTotalPointsForLesson(int $userId, int $lessonId): int
    {
        return static::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->where('completed', true)
            ->sum('points_earned');
    }

    /**
     * Get completion percentage for a lesson
     */
    public static function getCompletionPercentage(int $userId, int $lessonId): float
    {
        $totalSteps = InteractiveStep::where('lesson_id', $lessonId)->count();

        if ($totalSteps === 0) {
            return 0;
        }

        $completedSteps = static::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->where('completed', true)
            ->count();

        return round(($completedSteps / $totalSteps) * 100, 2);
    }
}
