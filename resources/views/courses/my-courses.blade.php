@extends('layouts.master')

@section('content')
<style>
    :root {
        --primary-color: #673AB7;
        --secondary-color: #673AB7;
        --accent-color: #673AB7;
        --text-color: #1f2937;
        --light-bg: #f3f4f6;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        margin: 0;
        padding: 0;
        background-color: var(--light-bg);
        color: var(--text-color);
        line-height: 1.7;
    }

    .hero {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 3rem 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    .hero h1 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        font-weight: 800;
    }

    .hero p {
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
        opacity: 0.9;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem 3rem 2rem;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card i {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1rem;
        color: #6b7280;
        font-weight: 500;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-header h2 {
        font-size: 1.8rem;
        margin: 0;
        font-weight: 700;
    }

    .browse-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: gap 0.3s ease;
    }

    .browse-link:hover {
        gap: 0.75rem;
    }

    .courses-list {
        display: grid;
        gap: 2rem;
    }

    .course-card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        display: grid;
        grid-template-columns: 280px 1fr auto;
        transition: transform 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .course-thumbnail {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
        position: relative;
    }

    .course-progress-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.75rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-align: center;
    }

    .course-info {
        padding: 2rem;
        flex: 1;
    }

    .course-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: var(--text-color);
    }

    .course-description {
        color: #6b7280;
        margin: 0 0 1rem 0;
        line-height: 1.6;
    }

    .course-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .course-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .progress-section {
        margin-top: 1.5rem;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .progress-label {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .progress-percentage {
        font-weight: 800;
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    .progress-bar {
        height: 12px;
        background: #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transition: width 0.3s ease;
        border-radius: 6px;
    }

    .progress-fill.complete {
        background: linear-gradient(90deg, #10b981, #059669);
    }

    .course-actions {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1rem;
        border-left: 1px solid #e5e7eb;
    }

    .action-button {
        padding: 1rem 2rem;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        transition: background 0.3s ease;
        white-space: nowrap;
    }

    .action-button:hover {
        background: var(--secondary-color);
    }

    .action-button.secondary {
        background: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .action-button.secondary:hover {
        background: #ede7f6;
    }

    .action-button.complete {
        background: #10b981;
    }

    .action-button.complete:hover {
        background: #059669;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }

    .empty-state i {
        font-size: 5rem;
        color: #d1d5db;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin: 0 0 1rem 0;
        color: var(--text-color);
    }

    .empty-state p {
        color: #6b7280;
        margin: 0 0 2rem 0;
    }

    .empty-state a {
        display: inline-block;
        padding: 1rem 2rem;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .empty-state a:hover {
        background: var(--secondary-color);
    }

    .completed-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #d1fae5;
        color: #065f46;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 1024px) {
        .course-card {
            grid-template-columns: 1fr;
        }

        .course-thumbnail {
            height: 200px;
        }

        .course-actions {
            flex-direction: row;
            border-left: none;
            border-top: 1px solid #e5e7eb;
        }
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2rem;
        }

        .stats-overview {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="hero">
    <h1>My Learning Dashboard</h1>
    <p>Track your progress and continue your learning journey</p>
</div>

<div class="container">
    @php
        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->where('progress_percentage', 100)->count();
        $inProgressCourses = $enrollments->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100)->count();
        $avgProgress = $totalCourses > 0 ? round($enrollments->avg('progress_percentage')) : 0;
    @endphp

    <div class="stats-overview">
        <div class="stat-card">
            <i class="fas fa-graduation-cap"></i>
            <div class="stat-value">{{ $totalCourses }}</div>
            <div class="stat-label">Enrolled Courses</div>
        </div>

        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div class="stat-value">{{ $completedCourses }}</div>
            <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <div class="stat-value">{{ $inProgressCourses }}</div>
            <div class="stat-label">In Progress</div>
        </div>

        <div class="stat-card">
            <i class="fas fa-chart-line"></i>
            <div class="stat-value">{{ $avgProgress }}%</div>
            <div class="stat-label">Average Progress</div>
        </div>
    </div>

    <div class="section-header">
        <h2>My Courses</h2>
        <a href="{{ route('courses.index') }}" class="browse-link">
            <span>Browse All Courses</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    @if($enrollments->count() > 0)
        <div class="courses-list">
            @foreach($enrollments as $enrollment)
                @php
                    $course = $enrollment->course;
                    $isCompleted = $enrollment->progress_percentage >= 100;
                @endphp

                <div class="course-card">
                    <div class="course-thumbnail">
                        <i class="fas fa-book-open"></i>
                        <div class="course-progress-overlay">
                            {{ $enrollment->progress_percentage }}% Complete
                        </div>
                    </div>

                    <div class="course-info">
                        @if($isCompleted)
                            <div class="completed-badge">
                                <i class="fas fa-trophy"></i> Completed
                            </div>
                        @endif

                        <h3 class="course-title">{{ $course->title }}</h3>
                        <p class="course-description">{{ Str::limit($course->description, 150) }}</p>

                        <div class="course-meta">
                            <span><i class="fas fa-signal"></i> {{ ucfirst($course->level) }}</span>
                            <span><i class="fas fa-clock"></i> {{ $course->duration_hours }} hours</span>
                            <span><i class="fas fa-book"></i> {{ $course->modules->count() }} modules</span>
                            <span><i class="fas fa-calendar"></i> Enrolled {{ $enrollment->enrolled_at->diffForHumans() }}</span>
                        </div>

                        <div class="progress-section">
                            <div class="progress-header">
                                <span class="progress-label">Your Progress</span>
                                <span class="progress-percentage">{{ $enrollment->progress_percentage }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="course-actions">
                        @if($isCompleted)
                            <a href="{{ route('courses.show', $course->slug) }}" class="action-button complete">
                                <i class="fas fa-trophy"></i> View Course
                            </a>
                        @else
                            <a href="{{ route('courses.lessons.show', [$course->slug, $course->modules->first()->lessons->first()->id]) }}" class="action-button">
                                <i class="fas fa-play-circle"></i> Continue Learning
                            </a>
                        @endif

                        <a href="{{ route('courses.show', $course->slug) }}" class="action-button secondary">
                            <i class="fas fa-list"></i> Course Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-book-reader"></i>
            <h3>No Courses Enrolled Yet</h3>
            <p>Start your learning journey by enrolling in a course!</p>
            <a href="{{ route('courses.index') }}">
                <i class="fas fa-search"></i> Browse Courses
            </a>
        </div>
    @endif
</div>
@endsection
