<?php

namespace App\Http\Controllers\Web;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use App\Models\DesktopConfig;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a specific lesson.
     */
    public function show($courseSlug, $lessonId)
    {
        // Find the course and lesson
        $course = Course::where('slug', $courseSlug)
            ->where('is_published', true)
            ->with(['modules.lessons'])
            ->firstOrFail();

        $lesson = CourseLesson::with(['module', 'exercises'])
            ->findOrFail($lessonId);

        // Verify lesson belongs to this course
        if ($lesson->module->course_id != $course->id) {
            abort(404);
        }

        // Redirect interactive lessons to interactive view
        if ($lesson->type === 'interactive') {
            return redirect()->route('courses.lessons.interactive', [$courseSlug, $lessonId]);
        }

        // Check if user is enrolled
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to view lessons.');
        }

        $enrollment = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment && !$lesson->is_free_preview) {
            return redirect()->route('courses.show', $courseSlug)
                ->with('error', 'Please enroll in this course to view lessons.');
        }

        // Get lesson progress
        $lessonProgress = null;
        if ($enrollment) {
            $lessonProgress = LessonProgress::firstOrCreate([
                'user_id' => auth()->id(),
                'lesson_id' => $lessonId,
                'enrollment_id' => $enrollment->id
            ]);
        }

        // Get previous and next lessons for navigation
        $allLessons = collect();
        foreach ($course->modules as $module) {
            foreach ($module->lessons as $moduleLesson) {
                $allLessons->push([
                    'id' => $moduleLesson->id,
                    'title' => $moduleLesson->title,
                    'module_title' => $module->title
                ]);
            }
        }

        $currentIndex = $allLessons->search(function ($item) use ($lessonId) {
            return $item['id'] == $lessonId;
        });

        $previousLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        // Meta Tags
        MetaTag::set('title', $lesson->title . ' - ' . $course->title);
        MetaTag::set('description', strip_tags(substr($lesson->content, 0, 160)));

        return view('courses.lesson', compact(
            'course',
            'lesson',
            'enrollment',
            'lessonProgress',
            'previousLesson',
            'nextLesson',
            'allLessons'
        ));
    }

    /**
     * Mark a lesson as completed.
     */
    public function complete(Request $request, $lessonId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lesson = CourseLesson::with('module')->findOrFail($lessonId);
        $courseId = $lesson->module->course_id;

        $enrollment = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Mark lesson as completed
        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'lesson_id' => $lessonId,
                'enrollment_id' => $enrollment->id
            ],
            [
                'completed' => true,
                'completed_at' => now()
            ]
        );

        // Update course progress percentage
        $this->updateCourseProgress($enrollment);

        return response()->json([
            'success' => true,
            'progress_percentage' => $enrollment->fresh()->progress_percentage
        ]);
    }

    /**
     * Update the overall course progress percentage.
     */
    private function updateCourseProgress($enrollment)
    {
        $course = Course::with('modules.lessons')->find($enrollment->course_id);

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
            'completed_at' => $progressPercentage >= 100 ? now() : null
        ]);
    }

    /**
     * Display an interactive lesson with virtual desktop.
     */
    public function interactive(Request $request, $courseSlug, $lessonId)
    {
        // Find the course and lesson
        $course = Course::where('slug', $courseSlug)
            ->where('is_published', true)
            ->with(['modules.lessons'])
            ->firstOrFail();

        $lesson = CourseLesson::with(['module', 'interactiveSteps' => function ($query) {
            $query->orderBy('step_number', 'asc');
        }, 'desktopConfig'])
            ->findOrFail($lessonId);

        // Verify lesson belongs to this course and is interactive
        if ($lesson->module->course_id != $course->id) {
            abort(404);
        }

        if ($lesson->type !== 'interactive') {
            // Redirect to regular lesson view if not interactive
            return redirect()->route('courses.lessons.show', [$courseSlug, $lessonId]);
        }

        // Check if user is enrolled
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to view interactive lessons.');
        }

        $enrollment = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment && !$lesson->is_free_preview) {
            return redirect()->route('courses.show', $courseSlug)
                ->with('error', 'Please enroll in this course to access interactive lessons.');
        }

        // Get mode from request (guided or free)
        $mode = $request->get('mode', 'guided');
        if (!in_array($mode, ['guided', 'free'])) {
            $mode = 'guided';
        }

        // Get steps for guided mode
        $steps = $lesson->interactiveSteps;

        // Get or create desktop config
        $desktopConfig = $lesson->desktopConfig;
        if (!$desktopConfig) {
            $desktopConfig = new DesktopConfig();
        }

        // Meta Tags
        MetaTag::set('title', $lesson->title . ' - Interactive Lesson');
        MetaTag::set('description', 'Interactive learning experience: ' . strip_tags(substr($lesson->content, 0, 160)));

        return view('courses.interactive-lesson', compact(
            'course',
            'lesson',
            'enrollment',
            'mode',
            'steps',
            'desktopConfig'
        ));
    }
}
