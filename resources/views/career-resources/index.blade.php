@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            {{-- Hero Section --}}
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h1 class="display-5 fw-bold text-primary mb-3">Career Resources</h1>
                    <p class="lead text-muted">Free tools and guides to help you land your dream job</p>
                </div>
            </div>

            {{-- Feature Cards --}}
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-chart-line fa-2x"></i>
                            </div>
                            <h3 class="h4">Salary Insights</h3>
                            <p class="text-muted">Discover salary ranges by job category and location. Know your worth before negotiating.</p>
                            <a href="{{ route('career.salary-insights') }}" class="btn btn-outline-primary">
                                Explore Salaries <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-success bg-gradient text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-lightbulb fa-2x"></i>
                            </div>
                            <h3 class="h4">Career Tips</h3>
                            <p class="text-muted">Expert advice on CV writing, interview preparation, and job search strategies.</p>
                            <a href="{{ route('career.tips') }}" class="btn btn-outline-success">
                                Read Tips <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-warning bg-gradient text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-compass fa-2x"></i>
                            </div>
                            <h3 class="h4">Career Quiz</h3>
                            <p class="text-muted">Take our interactive quiz to discover your ideal job type and get personalized recommendations.</p>
                            <a href="{{ route('career.quiz') }}" class="btn btn-outline-warning">
                                Take Quiz <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Featured Career Tips --}}
            @if($featuredTips->isNotEmpty())
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="h3 mb-4"><i class="fa-solid fa-star text-warning me-2"></i> Featured Career Tips</h2>
                </div>
                @foreach($featuredTips as $tip)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $tip->category_label }}</span>
                            <h4 class="h5">
                                <a href="{{ route('career.tips.show', $tip->slug) }}" class="text-decoration-none text-dark">
                                    {{ $tip->title }}
                                </a>
                            </h4>
                            <p class="text-muted small">{{ \Str::limit($tip->excerpt ?: strip_tags($tip->content), 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> {{ $tip->reading_time }} min read</small>
                                <a href="{{ route('career.tips.show', $tip->slug) }}" class="btn btn-sm btn-link">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Quick Stats --}}
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-md-3 col-6 mb-3 mb-md-0">
                                    <div class="h2 mb-0">{{ number_format(\App\Models\Post::count()) }}</div>
                                    <small>Jobs Available</small>
                                </div>
                                <div class="col-md-3 col-6 mb-3 mb-md-0">
                                    <div class="h2 mb-0">{{ number_format(\App\Models\Category::count()) }}</div>
                                    <small>Job Categories</small>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="h2 mb-0">{{ number_format(\App\Models\User::count()) }}</div>
                                    <small>Registered Users</small>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="h2 mb-0">{{ \App\Models\CareerTip::active()->count() }}</div>
                                    <small>Career Tips</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Browse by Category --}}
            <div class="row">
                <div class="col-12">
                    <h2 class="h3 mb-4"><i class="fa-solid fa-folder-open me-2"></i> Browse Career Tips by Category</h2>
                </div>
                @foreach($categories as $key => $label)
                <div class="col-md-4 col-6 mb-3">
                    <a href="{{ route('career.tips', ['category' => $key]) }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body d-flex align-items-center">
                            <i class="fa-solid fa-folder text-primary me-3"></i>
                            <span class="text-dark">{{ $label }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-5px);
}
</style>
@endsection
