@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('career.index') }}">Career Resources</a></li>
                    <li class="breadcrumb-item active">Career Tips</li>
                </ol>
            </nav>

            <div class="row">
                {{-- Main Content --}}
                <div class="col-lg-8">
                    <h1 class="h2 mb-4">
                        <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                        {{ $selectedCategory && isset($categories[$selectedCategory]) ? $categories[$selectedCategory] : 'Career Tips & Advice' }}
                    </h1>

                    @if($tips->isEmpty())
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            No career tips available yet. Check back soon!
                        </div>
                    @else
                        <div class="row">
                            @foreach($tips as $tip)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm hover-shadow">
                                    @if($tip->featured_image)
                                    <img src="{{ $tip->featured_image_url }}" class="card-img-top" alt="{{ $tip->title }}" style="height: 160px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">{{ $tip->category_label }}</span>
                                            @if($tip->is_featured)
                                                <span class="badge bg-warning"><i class="fa-solid fa-star"></i> Featured</span>
                                            @endif
                                        </div>
                                        <h5 class="card-title">
                                            <a href="{{ route('career.tips.show', $tip->slug) }}" class="text-decoration-none text-dark stretched-link">
                                                {{ $tip->title }}
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted small">
                                            {{ \Str::limit($tip->excerpt ?: strip_tags($tip->content), 100) }}
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <small class="text-muted">
                                            <i class="fa-regular fa-clock me-1"></i> {{ $tip->reading_time }} min read
                                            <span class="mx-2">|</span>
                                            <i class="fa-regular fa-eye me-1"></i> {{ number_format($tip->views) }} views
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $tips->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Categories --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fa-solid fa-folder me-2"></i> Categories</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('career.tips') }}" class="list-group-item list-group-item-action {{ !$selectedCategory ? 'active' : '' }}">
                                <i class="fa-solid fa-globe me-2"></i> All Categories
                            </a>
                            @foreach($categories as $key => $label)
                            <a href="{{ route('career.tips', ['category' => $key]) }}"
                               class="list-group-item list-group-item-action {{ $selectedCategory == $key ? 'active' : '' }}">
                                <i class="fa-solid fa-chevron-right me-2"></i> {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Featured Tips --}}
                    @if($featuredTips->isNotEmpty())
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fa-solid fa-star me-2"></i> Featured Tips</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($featuredTips as $tip)
                            <a href="{{ route('career.tips.show', $tip->slug) }}" class="list-group-item list-group-item-action">
                                <div class="fw-bold">{{ \Str::limit($tip->title, 50) }}</div>
                                <small class="text-muted">{{ $tip->reading_time }} min read</small>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Career Quiz CTA --}}
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body text-center p-4">
                            <i class="fa-solid fa-compass fa-3x mb-3"></i>
                            <h5>Find Your Ideal Job</h5>
                            <p class="small mb-3">Take our career quiz to discover jobs that match your personality and skills!</p>
                            <a href="{{ route('career.quiz') }}" class="btn btn-light">
                                <i class="fa-solid fa-play me-1"></i> Start Quiz
                            </a>
                        </div>
                    </div>
                </div>
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
    transform: translateY(-3px);
}
</style>
@endsection
