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

    .course-hero {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
    }

    .course-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        align-items: start;
    }

    .course-info h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        font-weight: 800;
    }

    .course-info p {
        font-size: 1.1rem;
        opacity: 0.95;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .course-meta {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .meta-item i {
        font-size: 1.2rem;
    }

    .enrollment-card {
        background: white;
        color: var(--text-color);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }

    .enrollment-card h3 {
        margin: 0 0 1rem 0;
        font-size: 1.3rem;
    }

    .price-display {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .price-display.free {
        color: #10b981;
    }

    .enroll-button {
        width: 100%;
        padding: 1rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .enroll-button:hover {
        background: var(--secondary-color);
    }

    .enroll-button.enrolled {
        background: #10b981;
    }

    .enroll-button.continue {
        background: var(--primary-color);
    }

    .progress-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #e5e7eb;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #059669);
        transition: width 0.3s ease;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .course-content-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: var(--card-shadow);
        margin-bottom: 2rem;
    }

    .course-content-section h2 {
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        color: var(--text-color);
    }

    .modules-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .module-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .module-header {
        padding: 1.5rem;
        background: var(--light-bg);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s ease;
    }

    .module-header:hover {
        background: #e5e7eb;
    }

    .module-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .module-title i {
        color: var(--primary-color);
    }

    .module-info {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .lessons-list {
        padding: 0;
        margin: 0;
        list-style: none;
        display: none;
    }

    .lessons-list.active {
        display: block;
    }

    .lesson-item {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s ease;
    }

    .lesson-item:hover {
        background: var(--light-bg);
    }

    .lesson-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .lesson-icon {
        width: 32px;
        height: 32px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    .lesson-details h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 500;
    }

    .lesson-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .lesson-action {
        padding: 0.5rem 1rem;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: background 0.3s ease;
    }

    .lesson-action:hover {
        background: var(--secondary-color);
    }

    .lesson-action.locked {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        text-align: center;
    }

    .stat-card i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .course-hero-content {
            grid-template-columns: 1fr;
        }

        .course-info h1 {
            font-size: 1.8rem;
        }

        .course-meta {
            gap: 1rem;
        }
    }
</style>

<div class="course-hero">
    <div class="course-hero-content">
        <div class="course-info">
            <h1>{{ $course->title }}</h1>
            <p>{{ $course->description }}</p>

            <div class="course-meta">
                <div class="meta-item">
                    <i class="fas fa-signal"></i>
                    <span>{{ ucfirst($course->level) }} Level</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $course->duration_hours }} hours</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-book"></i>
                    <span>{{ $moduleCount }} modules</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-video"></i>
                    <span>{{ $lessonCount }} lessons</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $course->enrollment_count }} enrolled</span>
                </div>
            </div>
        </div>

        <div class="enrollment-card">
            @if($course->is_free)
                <div class="price-display free">FREE</div>
            @else
                <div class="price-display">${{ number_format($course->price, 2) }}</div>
            @endif

            @if(auth()->check())
                @if($enrollment)
                    <a href="{{ route('courses.lessons.show', [$course->slug, $course->modules->first()->lessons->first()->id]) }}" class="enroll-button continue">
                        <i class="fas fa-play-circle"></i> Continue Learning
                    </a>

                    @if($enrollment->progress_percentage > 0)
                        <div class="progress-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">Your Progress</span>
                                <span style="font-weight: 700; color: var(--primary-color);">{{ $enrollment->progress_percentage }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @endif
                @else
                    <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="enroll-button">
                            <i class="fas fa-graduation-cap"></i> Enroll Now
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="enroll-button">
                    <i class="fas fa-sign-in-alt"></i> Login to Enroll
                </a>
            @endif

            <div class="progress-section">
                <p style="margin: 0; font-size: 0.9rem; color: #6b7280;">
                    <i class="fas fa-info-circle"></i>
                    @if($course->is_free)
                        This course is completely free!
                    @else
                        One-time payment, lifetime access
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 2rem;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 2rem;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert" style="margin-bottom: 2rem;">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="course-content-section">
        <h2><i class="fas fa-list"></i> Course Content</h2>

        <ul class="modules-list">
            @foreach($course->modules as $module)
                <li class="module-item">
                    <div class="module-header" onclick="toggleModule(this)">
                        <div class="module-title">
                            <i class="fas fa-folder"></i>
                            <span>{{ $module->title }}</span>
                        </div>
                        <div class="module-info">
                            <i class="fas fa-chevron-down"></i>
                            <span>{{ $module->lessons->count() }} lessons</span>
                        </div>
                    </div>

                    <ul class="lessons-list">
                        @foreach($module->lessons as $lesson)
                            <li class="lesson-item">
                                <div class="lesson-info">
                                    <div class="lesson-icon">
                                        @if($lesson->type === 'video')
                                            <i class="fas fa-play"></i>
                                        @elseif($lesson->type === 'quiz')
                                            <i class="fas fa-question-circle"></i>
                                        @elseif($lesson->type === 'exercise')
                                            <i class="fas fa-code"></i>
                                        @else
                                            <i class="fas fa-file-alt"></i>
                                        @endif
                                    </div>
                                    <div class="lesson-details">
                                        <h4>{{ $lesson->title }}</h4>
                                        <div class="lesson-meta">
                                            <span><i class="fas fa-clock"></i> {{ $lesson->duration_minutes }} min</span>
                                            @if($lesson->is_free_preview)
                                                <span style="color: #10b981;"><i class="fas fa-unlock"></i> Free Preview</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($enrollment || $lesson->is_free_preview)
                                    <a href="{{ route('courses.lessons.show', [$course->slug, $lesson->id]) }}" class="lesson-action">
                                        <i class="fas fa-play-circle"></i> Start
                                    </a>
                                @else
                                    <span class="lesson-action locked">
                                        <i class="fas fa-lock"></i> Locked
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<script>
function toggleModule(header) {
    const lessonsList = header.nextElementSibling;
    const icon = header.querySelector('.fa-chevron-down, .fa-chevron-up');

    lessonsList.classList.toggle('active');

    if (lessonsList.classList.contains('active')) {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

// Auto-expand first module
document.addEventListener('DOMContentLoaded', function() {
    const firstModule = document.querySelector('.module-header');
    if (firstModule) {
        toggleModule(firstModule);
    }
});
</script>
@endsection
