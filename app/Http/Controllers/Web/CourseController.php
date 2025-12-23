<?php

namespace App\Http\Controllers\Web;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Helpers\UrlGen;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends FrontController
{
    /**
     * Display the courses listing page.
     */
    public function index(Request $request)
    {
        // Meta Tags
        [$title, $description, $keywords] = getMetaTag('courses');
        MetaTag::set('title', 'Courses - ' . $title);
        MetaTag::set('description', 'Browse our comprehensive course catalog and start learning today.');
        MetaTag::set('keywords', $keywords);

        // Get paginated courses (10 per page)
        $courses = Course::where('is_published', true)
            ->select(['id', 'title', 'slug', 'description', 'price', 'is_free', 'level', 'duration_hours', 'enrollment_count'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get module counts for current page courses
        $moduleCounts = \DB::table('course_modules')
            ->select('course_id', \DB::raw('COUNT(*) as module_count'))
            ->whereIn('course_id', $courses->pluck('id'))
            ->groupBy('course_id')
            ->pluck('module_count', 'course_id');

        // Attach module counts to courses
        $courses->each(function ($course) use ($moduleCounts) {
            $course->modules_count = $moduleCounts[$course->id] ?? 0;
        });

        // Check which courses the user is enrolled in (if authenticated)
        $enrolledCourseIds = [];
        if (auth()->check()) {
            $enrolledCourseIds = CourseEnrollment::where('user_id', auth()->id())
                ->pluck('course_id')
                ->toArray();
        }

        return view('courses.index', compact('courses', 'enrolledCourseIds'));
    }

    /**
     * Display a specific course detail page.
     */
    public function show($slug)
    {
        // Find course by slug
        $course = Course::where('slug', $slug)
            ->where('is_published', true)
            ->with(['modules.lessons', 'instructor'])
            ->withCount('enrollments')
            ->firstOrFail();

        // Meta Tags
        MetaTag::set('title', $course->title);
        MetaTag::set('description', strip_tags($course->description));
        MetaTag::set('keywords', 'course, learning, ' . $course->title);

        // Check if user is enrolled
        $isEnrolled = false;
        $enrollment = null;
        if (auth()->check()) {
            $enrollment = CourseEnrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
            $isEnrolled = $enrollment !== null;
        }

        // Count modules and lessons
        $moduleCount = $course->modules->count();
        $lessonCount = $course->modules->sum(function ($module) {
            return $module->lessons->count();
        });

        return view('courses.show', compact('course', 'isEnrolled', 'enrollment', 'moduleCount', 'lessonCount'));
    }

    /**
     * Display user's enrolled courses dashboard.
     */
    public function myCourses()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Meta Tags
        [$title, $description, $keywords] = getMetaTag('courses');
        MetaTag::set('title', 'My Courses - ' . $title);
        MetaTag::set('description', 'View and manage your enrolled courses');
        MetaTag::set('keywords', $keywords);

        // Get user's enrollments with course data
        $enrollments = CourseEnrollment::where('user_id', auth()->id())
            ->with(['course.modules.lessons'])
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return view('courses.my-courses', compact('enrollments'));
    }
}
