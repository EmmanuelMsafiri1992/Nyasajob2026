<?php

namespace App\Http\Controllers\Web;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Enroll the authenticated user in a course.
     */
    public function enroll(Request $request, $courseId)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to enroll in courses.');
        }

        $course = Course::findOrFail($courseId);

        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('info', 'You are already enrolled in this course.');
        }

        // Create enrollment
        $enrollment = CourseEnrollment::create([
            'user_id' => auth()->id(),
            'course_id' => $courseId,
            'enrolled_at' => now(),
            'progress_percentage' => 0
        ]);

        // Increment enrollment count
        $course->increment('enrollment_count');

        return redirect()->route('courses.show', $course->slug)
            ->with('success', 'Successfully enrolled! You can now start learning.');
    }

    /**
     * Unenroll the authenticated user from a course.
     */
    public function unenroll($courseId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $course = Course::findOrFail($courseId);

        $enrollment = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.index')
                ->with('error', 'You are not enrolled in this course.');
        }

        // Delete enrollment and related progress
        $enrollment->lessonProgress()->delete();
        $enrollment->delete();

        // Decrement enrollment count
        $course->decrement('enrollment_count');

        return redirect()->route('courses.index')
            ->with('success', 'Successfully unenrolled from the course.');
    }
}
