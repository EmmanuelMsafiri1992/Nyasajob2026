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
                    <li class="breadcrumb-item"><a href="{{ route('career.tips') }}">Career Tips</a></li>
                    <li class="breadcrumb-item active">{{ \Str::limit($tip->title, 30) }}</li>
                </ol>
            </nav>

            <div class="row">
                {{-- Main Content --}}
                <div class="col-lg-8">
                    <article class="card border-0 shadow-sm mb-4">
                        @if($tip->featured_image)
                        <img src="{{ $tip->featured_image_url }}" class="card-img-top" alt="{{ $tip->title }}" style="max-height: 400px; object-fit: cover;">
                        @endif
                        <div class="card-body p-4 p-lg-5">
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">{{ $tip->category_label }}</span>
                                @if($tip->is_featured)
                                    <span class="badge bg-warning"><i class="fa-solid fa-star"></i> Featured</span>
                                @endif
                            </div>

                            <h1 class="h2 mb-3">{{ $tip->title }}</h1>

                            <div class="d-flex align-items-center text-muted mb-4">
                                <small>
                                    <i class="fa-regular fa-calendar me-1"></i> {{ $tip->created_at->format('M d, Y') }}
                                </small>
                                <span class="mx-2">|</span>
                                <small>
                                    <i class="fa-regular fa-clock me-1"></i> {{ $tip->reading_time }} min read
                                </small>
                                <span class="mx-2">|</span>
                                <small>
                                    <i class="fa-regular fa-eye me-1"></i> {{ number_format($tip->views) }} views
                                </small>
                            </div>

                            @if($tip->excerpt)
                            <div class="lead text-muted mb-4 pb-4 border-bottom">
                                {{ $tip->excerpt }}
                            </div>
                            @endif

                            <div class="article-content">
                                {!! $tip->content !!}
                            </div>
                        </div>
                    </article>

                    {{-- Social Share --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">Share this article</h5>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                   target="_blank" class="btn btn-outline-primary">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($tip->title) }}"
                                   target="_blank" class="btn btn-outline-info">
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($tip->title) }}"
                                   target="_blank" class="btn btn-outline-primary">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($tip->title . ' - ' . request()->url()) }}"
                                   target="_blank" class="btn btn-outline-success">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Related Tips --}}
                    @if($relatedTips->isNotEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fa-solid fa-link me-2"></i> Related Articles</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($relatedTips as $related)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        @if($related->featured_image)
                                        <img src="{{ $related->featured_image_url }}" alt="{{ $related->title }}"
                                             class="rounded me-3" style="width: 80px; height: 60px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('career.tips.show', $related->slug) }}" class="text-decoration-none text-dark">
                                                    {{ \Str::limit($related->title, 50) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $related->reading_time }} min read</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Search Jobs CTA --}}
                    <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
                        <div class="card-body text-center p-4">
                            <i class="fa-solid fa-search fa-3x mb-3"></i>
                            <h5>Ready to Apply?</h5>
                            <p class="small mb-3">Find jobs that match your skills and career goals.</p>
                            <a href="{{ \App\Helpers\UrlGen::search() }}" class="btn btn-light">
                                <i class="fa-solid fa-briefcase me-1"></i> Browse Jobs
                            </a>
                        </div>
                    </div>

                    {{-- Browse Categories --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fa-solid fa-folder me-2"></i> More Topics</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach(\App\Models\CareerTip::CATEGORIES as $key => $label)
                            <a href="{{ route('career.tips', ['category' => $key]) }}" class="list-group-item list-group-item-action">
                                <i class="fa-solid fa-chevron-right me-2 text-primary"></i> {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Career Quiz --}}
                    <div class="card border-0 shadow-sm bg-warning">
                        <div class="card-body text-center p-4">
                            <i class="fa-solid fa-compass fa-3x mb-3"></i>
                            <h5>Find Your Career Path</h5>
                            <p class="small mb-3">Take our quiz to discover your ideal job type!</p>
                            <a href="{{ route('career.quiz') }}" class="btn btn-dark">
                                <i class="fa-solid fa-play me-1"></i> Take Quiz
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
.article-content {
    line-height: 1.8;
    font-size: 1.1rem;
}
.article-content h2, .article-content h3, .article-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.article-content ul, .article-content ol {
    margin-bottom: 1.5rem;
}
.article-content li {
    margin-bottom: 0.5rem;
}
.article-content p {
    margin-bottom: 1.5rem;
}
.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1rem 0;
}
.article-content blockquote {
    border-left: 4px solid #0d6efd;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
}
</style>
@endsection
