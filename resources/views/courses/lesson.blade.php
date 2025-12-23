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

    .lesson-container {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 0;
        min-height: calc(100vh - 60px);
        max-width: 100%;
        margin: 0;
    }

    .sidebar {
        background: white;
        border-right: 1px solid #e5e7eb;
        overflow-y: auto;
        height: calc(100vh - 60px);
        position: sticky;
        top: 60px;
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: var(--primary-color);
        color: white;
    }

    .sidebar-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .sidebar-header p {
        margin: 0.5rem 0 0 0;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .sidebar-modules {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .sidebar-module {
        border-bottom: 1px solid #e5e7eb;
    }

    .sidebar-module-header {
        padding: 1rem 1.5rem;
        background: var(--light-bg);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s ease;
    }

    .sidebar-module-header:hover {
        background: #e5e7eb;
    }

    .sidebar-lessons {
        list-style: none;
        padding: 0;
        margin: 0;
        display: none;
    }

    .sidebar-lessons.active {
        display: block;
    }

    .sidebar-lesson {
        padding: 0.75rem 1.5rem 0.75rem 2.5rem;
        border-top: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
    }

    .sidebar-lesson:hover {
        background: var(--light-bg);
    }

    .sidebar-lesson.active {
        background: #ede7f6;
        border-left: 3px solid var(--primary-color);
        font-weight: 600;
    }

    .sidebar-lesson i {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .sidebar-lesson.completed i {
        color: #10b981;
    }

    .main-content {
        padding: 2rem;
        overflow-y: auto;
    }

    .lesson-header {
        max-width: 900px;
        margin: 0 auto 2rem auto;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .breadcrumb {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .breadcrumb a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .lesson-title {
        font-size: 2rem;
        margin: 0 0 1rem 0;
        font-weight: 700;
    }

    .lesson-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .lesson-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .lesson-body {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        margin-bottom: 2rem;
    }

    .lesson-content {
        font-size: 1.05rem;
        line-height: 1.8;
    }

    .lesson-content h1,
    .lesson-content h2,
    .lesson-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .lesson-content h1 {
        font-size: 1.8rem;
    }

    .lesson-content h2 {
        font-size: 1.5rem;
    }

    .lesson-content h3 {
        font-size: 1.2rem;
    }

    .lesson-content ul,
    .lesson-content ol {
        margin: 1rem 0;
        padding-left: 2rem;
    }

    .lesson-content li {
        margin: 0.5rem 0;
    }

    .lesson-content code {
        background: #f3f4f6;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }

    .lesson-content pre {
        background: #1f2937;
        color: #f3f4f6;
        padding: 1rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1rem 0;
    }

    .lesson-content pre code {
        background: none;
        padding: 0;
        color: #f3f4f6;
    }

    .exercises-section {
        max-width: 900px;
        margin: 0 auto 2rem auto;
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }

    .exercises-section h3 {
        font-size: 1.5rem;
        margin: 0 0 1.5rem 0;
        color: var(--primary-color);
    }

    .exercise-card {
        background: var(--light-bg);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border-left: 4px solid var(--primary-color);
    }

    .exercise-card h4 {
        margin: 0 0 1rem 0;
        font-size: 1.1rem;
    }

    .exercise-difficulty {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .exercise-difficulty.easy {
        background: #d1fae5;
        color: #065f46;
    }

    .exercise-difficulty.medium {
        background: #fed7aa;
        color: #92400e;
    }

    .exercise-difficulty.hard {
        background: #fecaca;
        color: #991b1b;
    }

    .lesson-navigation {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 2rem 0;
    }

    .nav-button {
        padding: 1rem 1.5rem;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-color);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        flex: 1;
    }

    .nav-button:hover {
        border-color: var(--primary-color);
        background: #ede7f6;
    }

    .nav-button.next {
        justify-content: flex-end;
        text-align: right;
    }

    .nav-button-label {
        font-size: 0.8rem;
        color: #6b7280;
        display: block;
    }

    .complete-button {
        max-width: 900px;
        margin: 0 auto 2rem auto;
        text-align: center;
    }

    .complete-button button {
        padding: 1rem 3rem;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .complete-button button:hover {
        background: #059669;
    }

    .complete-button button:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .complete-button .completed {
        background: #6b7280;
        cursor: default;
    }

    @media (max-width: 1024px) {
        .lesson-container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            display: none;
        }
    }
</style>

<div class="lesson-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>{{ $course->title }}</h3>
            @if($enrollment)
                <p><i class="fas fa-chart-line"></i> {{ $enrollment->progress_percentage }}% Complete</p>
            @endif
        </div>

        <ul class="sidebar-modules">
            @foreach($course->modules as $module)
                <li class="sidebar-module">
                    <div class="sidebar-module-header" onclick="toggleSidebarModule(this)">
                        <span>{{ $module->title }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>

                    <ul class="sidebar-lessons {{ $module->id === $lesson->module->id ? 'active' : '' }}">
                        @foreach($module->lessons as $moduleLesson)
                            @php
                                $isCompleted = false;
                                if ($enrollment) {
                                    $lessonProg = $enrollment->lessonProgress->firstWhere('lesson_id', $moduleLesson->id);
                                    $isCompleted = $lessonProg && $lessonProg->completed;
                                }
                            @endphp
                            <li class="sidebar-lesson {{ $moduleLesson->id === $lesson->id ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                                <i class="fas {{ $isCompleted ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                <a href="{{ route('courses.lessons.show', [$course->slug, $moduleLesson->id]) }}" style="color: inherit; text-decoration: none; flex: 1;">
                                    {{ $moduleLesson->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </aside>

    <main class="main-content">
        <div class="lesson-header">
            <div class="breadcrumb">
                <a href="{{ route('courses.index') }}">Courses</a> /
                <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a> /
                <span>{{ $lesson->title }}</span>
            </div>

            <h1 class="lesson-title">{{ $lesson->title }}</h1>

            <div class="lesson-meta">
                <span><i class="fas fa-folder"></i> {{ $lesson->module->title }}</span>
                <span><i class="fas fa-clock"></i> {{ $lesson->duration_minutes }} minutes</span>
                <span><i class="fas fa-tag"></i> {{ ucfirst($lesson->type) }}</span>
                @if($lessonProgress && $lessonProgress->completed)
                    <span style="color: #10b981;"><i class="fas fa-check-circle"></i> Completed</span>
                @endif
            </div>
        </div>

        <div class="lesson-body">
            <div class="lesson-content">
                {!! $lesson->content !!}
            </div>
        </div>

        {{-- Virtual Computer for Interactive Practice --}}
        @if($lesson->type === 'exercise' || $lesson->type === 'quiz')
            @include('components.virtual-computer', [
                'tasks' => $lesson->exercises->map(function($ex) {
                    return [
                        'text' => $ex->question,
                        'completed' => false
                    ];
                })->toArray()
            ])
        @endif

        @if($lesson->exercises->count() > 0)
            <div class="exercises-section">
                <h3><i class="fas fa-code"></i> Practice Exercises</h3>

                @foreach($lesson->exercises as $exercise)
                    <div class="exercise-card">
                        <h4>
                            {{ $exercise->title }}
                            <span class="exercise-difficulty {{ $exercise->difficulty }}">
                                {{ ucfirst($exercise->difficulty) }}
                            </span>
                        </h4>
                        <p>{{ $exercise->question }}</p>

                        @if($exercise->code_template)
                            <pre><code>{{ $exercise->code_template }}</code></pre>
                        @endif

                        @if($exercise->points > 0)
                            <p style="margin-top: 1rem; color: var(--primary-color); font-weight: 600;">
                                <i class="fas fa-star"></i> {{ $exercise->points }} points
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if($enrollment)
            <div class="complete-button">
                @if($lessonProgress && $lessonProgress->completed)
                    <button class="completed" disabled>
                        <i class="fas fa-check-circle"></i>
                        Lesson Completed
                    </button>
                @else
                    <button onclick="markComplete()">
                        <i class="fas fa-check"></i>
                        Mark as Complete
                    </button>
                @endif
            </div>
        @endif

        <div class="lesson-navigation">
            @if($previousLesson)
                <a href="{{ route('courses.lessons.show', [$course->slug, $previousLesson['id']]) }}" class="nav-button previous">
                    <div>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <div>
                        <span class="nav-button-label">Previous</span>
                        <div>{{ $previousLesson['title'] }}</div>
                    </div>
                </a>
            @else
                <div></div>
            @endif

            @if($nextLesson)
                <a href="{{ route('courses.lessons.show', [$course->slug, $nextLesson['id']]) }}" class="nav-button next">
                    <div>
                        <span class="nav-button-label">Next</span>
                        <div>{{ $nextLesson['title'] }}</div>
                    </div>
                    <div>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            @else
                <div></div>
            @endif
        </div>
    </main>
</div>

<script>
function toggleSidebarModule(header) {
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

function markComplete() {
    const button = event.target.closest('button');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Marking Complete...';

    fetch('{{ route('courses.lessons.complete', $lesson->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check-circle"></i> Lesson Completed';
            button.classList.add('completed');

            // Update sidebar progress
            const progressText = document.querySelector('.sidebar-header p');
            if (progressText) {
                progressText.innerHTML = '<i class="fas fa-chart-line"></i> ' + data.progress_percentage + '% Complete';
            }

            // Update sidebar lesson icon
            const currentLesson = document.querySelector('.sidebar-lesson.active');
            if (currentLesson) {
                currentLesson.classList.add('completed');
                const icon = currentLesson.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-circle');
                    icon.classList.add('fa-check-circle');
                }
            }

            // Show success message
            setTimeout(() => {
                @if($nextLesson)
                    if (confirm('Lesson completed! Move to next lesson?')) {
                        window.location.href = '{{ route('courses.lessons.show', [$course->slug, $nextLesson['id']]) }}';
                    }
                @else
                    alert('Congratulations! You have completed all lessons in this course!');
                @endif
            }, 500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check"></i> Mark as Complete';
        alert('Error marking lesson as complete. Please try again.');
    });
}

// Auto-expand current module on load
document.addEventListener('DOMContentLoaded', function() {
    const activeModule = document.querySelector('.sidebar-lessons.active');
    if (activeModule) {
        const header = activeModule.previousElementSibling;
        const icon = header.querySelector('i');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    }
});
</script>
@endsection
