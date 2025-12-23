<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\InteractiveStep;
use App\Models\InteractiveStepProgress;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InteractiveProgressController extends Controller
{
    /**
     * Save progress for a single interactive step.
     */
    public function saveStepProgress(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_id' => 'required|integer|exists:course_lessons,id',
            'step_id' => 'required|integer|exists:interactive_steps,id',
            'completed' => 'required|boolean',
            'attempts' => 'nullable|integer|min:1',
            'hint_used' => 'nullable|boolean',
            'points_earned' => 'nullable|integer|min:0',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $lessonId = $request->lesson_id;
        $stepId = $request->step_id;

        // Verify the step belongs to the lesson
        $step = InteractiveStep::where('id', $stepId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$step) {
            return response()->json(['error' => 'Step not found for this lesson'], 404);
        }

        // Verify user is enrolled in the course
        $lesson = CourseLesson::with('module')->findOrFail($lessonId);
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Save or update step progress
        $progress = InteractiveStepProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'step_id' => $stepId,
            ],
            [
                'completed' => $request->completed,
                'attempts' => $request->attempts ?? 1,
                'hint_used' => $request->hint_used ?? false,
                'points_earned' => $request->points_earned ?? 0,
                'time_spent' => $request->time_spent ?? 0,
                'completed_at' => $request->completed ? now() : null,
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Mark an interactive lesson as complete.
     */
    public function completeLesson(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_id' => 'required|integer|exists:course_lessons,id',
            'total_points' => 'nullable|integer|min:0',
            'time_spent' => 'nullable|integer|min:0',
            'completed_steps' => 'nullable|integer|min:0',
            'total_steps' => 'nullable|integer|min:0',
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $lessonId = $request->lesson_id;

        // Get lesson and enrollment
        $lesson = CourseLesson::with('module')->findOrFail($lessonId);
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Update or create lesson progress
        $lessonProgress = LessonProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
                'time_spent' => $request->time_spent ?? 0,
                'score' => $request->total_points ?? 0,
            ]
        );

        // Update overall course progress
        $this->updateCourseProgress($enrollment);

        return response()->json([
            'success' => true,
            'lesson_progress' => $lessonProgress,
            'course_progress' => $enrollment->fresh()->progress_percentage,
        ]);
    }

    /**
     * Get progress for a specific lesson.
     */
    public function getLessonProgress(Request $request, $lessonId): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();

        // Get lesson with steps
        $lesson = CourseLesson::with(['module', 'interactiveSteps'])
            ->findOrFail($lessonId);

        // Verify enrollment
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Get step progress for all steps in this lesson
        $stepIds = $lesson->interactiveSteps->pluck('id');
        $stepProgress = InteractiveStepProgress::where('user_id', $userId)
            ->whereIn('step_id', $stepIds)
            ->get()
            ->keyBy('step_id');

        // Get lesson progress
        $lessonProgress = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        return response()->json([
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'completed' => $lessonProgress?->completed ?? false,
            ],
            'steps' => $lesson->interactiveSteps->map(function ($step) use ($stepProgress) {
                $progress = $stepProgress->get($step->id);
                return [
                    'id' => $step->id,
                    'step_number' => $step->step_number,
                    'title' => $step->title,
                    'completed' => $progress?->completed ?? false,
                    'points_earned' => $progress?->points_earned ?? 0,
                    'attempts' => $progress?->attempts ?? 0,
                ];
            }),
            'total_points' => $stepProgress->sum('points_earned'),
            'completed_steps' => $stepProgress->where('completed', true)->count(),
            'total_steps' => $lesson->interactiveSteps->count(),
        ]);
    }

    /**
     * Reset progress for a lesson.
     */
    public function resetProgress(Request $request, $lessonId): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();

        // Get lesson
        $lesson = CourseLesson::with(['module', 'interactiveSteps'])
            ->findOrFail($lessonId);

        // Verify enrollment
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Delete step progress
        $stepIds = $lesson->interactiveSteps->pluck('id');
        InteractiveStepProgress::where('user_id', $userId)
            ->whereIn('step_id', $stepIds)
            ->delete();

        // Reset lesson progress (but don't delete enrollment)
        LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->update([
                'completed' => false,
                'completed_at' => null,
                'score' => 0,
                'time_spent' => 0,
            ]);

        // Update course progress
        $this->updateCourseProgress($enrollment);

        return response()->json([
            'success' => true,
            'message' => 'Progress reset successfully',
        ]);
    }

    /**
     * Update the overall course progress percentage.
     */
    private function updateCourseProgress(CourseEnrollment $enrollment): void
    {
        $course = $enrollment->course()->with('modules.lessons')->first();

        if (!$course) {
            return;
        }

        // Count total lessons
        $totalLessons = 0;
        foreach ($course->modules as $module) {
            $totalLessons += $module->lessons->count();
        }

        if ($totalLessons == 0) {
            return;
        }

        // Count completed lessons
        $completedLessons = LessonProgress::where('user_id', auth()->id())
            ->where('enrollment_id', $enrollment->id)
            ->where('completed', true)
            ->count();

        // Calculate percentage
        $progressPercentage = round(($completedLessons / $totalLessons) * 100);

        // Update enrollment
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'completed_at' => $progressPercentage >= 100 ? now() : null,
        ]);
    }
}
