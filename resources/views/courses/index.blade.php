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
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
        }

        .course-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .course-header i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .course-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .course-content {
            padding: 1.5rem;
        }

        .course-details {
            margin-top: 1rem;
        }

        .price-tag {
            background: var(--accent-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .features-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
        }

        .features-list li:before {
            content: "•";
            color: var(--primary-color);
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .contact-button {
            display: inline-flex;
            align-items: center;
            background: #673AB7;
            color: white !important;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 1rem;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 1rem;
        }

        .contact-button:hover {
            background: #5e35b1;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(103, 58, 183, 0.3);
        }

        .contact-button i {
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Start Your Learning Journey</h1>
        <p>Master new skills with our comprehensive courses. Learn at your own pace with hands-on practice and real-world projects.</p>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($courses->count() > 0)
            <!-- Showing results info -->
            <div style="margin-bottom: 1rem; color: #6b7280; font-size: 0.95rem;">
                Showing {{ $courses->firstItem() }}-{{ $courses->lastItem() }} of {{ $courses->total() }} courses
            </div>

            <div class="courses-grid">
                @foreach($courses as $course)
                    <div class="course-card">
                        <div class="course-header">
                            <i class="fas fa-graduation-cap"></i>
                            <h2>{{ $course->title }}</h2>
                        </div>
                        <div class="course-content">
                            @if($course->is_free)
                                <div class="price-tag" style="background: #10b981;">FREE COURSE</div>
                            @else
                                <div class="price-tag">${{ number_format($course->price, 2) }}</div>
                            @endif

                            <p style="margin: 1rem 0; color: #6b7280;">{{ Str::limit($course->description, 120) }}</p>

                            <div class="course-details">
                                <ul class="features-list">
                                    <li><i class="fas fa-signal"></i> Level: {{ ucfirst($course->level) }}</li>
                                    <li><i class="fas fa-clock"></i> {{ $course->duration_hours }} hours</li>
                                    <li><i class="fas fa-book"></i> {{ $course->modules_count }} modules</li>
                                    <li><i class="fas fa-users"></i> {{ $course->enrollments_count }} students enrolled</li>
                                </ul>
                            </div>

                            @if(in_array($course->id, $enrolledCourseIds))
                                <a href="{{ route('courses.show', $course->slug) }}" class="contact-button" style="background: #10b981 !important;">
                                    <i class="fas fa-check-circle"></i>
                                    Continue Learning
                                </a>
                            @else
                                <a href="{{ route('courses.show', $course->slug) }}" class="contact-button" style="background: #673AB7 !important;">
                                    <i class="fas fa-arrow-right"></i>
                                    View Course
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            @if($courses->hasPages())
                <div class="pagination-wrapper" style="margin-top: 2rem; display: flex; justify-content: center;">
                    <nav aria-label="Courses pagination">
                        <ul class="pagination" style="display: flex; list-style: none; gap: 0.5rem; padding: 0; margin: 0; flex-wrap: wrap; justify-content: center;">
                            {{-- Previous Page Link --}}
                            @if ($courses->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" style="display: inline-block; padding: 0.5rem 1rem; background: #e5e7eb; color: #9ca3af; border-radius: 6px; text-decoration: none; cursor: not-allowed;">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $courses->previousPageUrl() }}" style="display: inline-block; padding: 0.5rem 1rem; background: white; color: #673AB7; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s;">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                                @if ($page == $courses->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link" style="display: inline-block; padding: 0.5rem 1rem; background: #673AB7; color: white; border-radius: 6px; font-weight: 600;">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}" style="display: inline-block; padding: 0.5rem 1rem; background: white; color: #673AB7; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s;">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($courses->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $courses->nextPageUrl() }}" style="display: inline-block; padding: 0.5rem 1rem; background: white; color: #673AB7; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s;">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" style="display: inline-block; padding: 0.5rem 1rem; background: #e5e7eb; color: #9ca3af; border-radius: 6px; text-decoration: none; cursor: not-allowed;">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No courses available at the moment. Check back soon!
            </div>
        @endif
    </div>
@endsection
