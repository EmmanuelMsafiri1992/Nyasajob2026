@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 page-sidebar">
                    @include('account.inc.sidebar')
                </div>

                <div class="col-md-9 page-content">
                    @include('flash::message')

                    <div class="inner-box">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="title-2 mb-0">
                                <strong><i class="fa-solid fa-bullseye"></i> Job Matches</strong>
                            </h2>
                            <div>
                                <a href="{{ route('account.premium.preferences') }}" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fa-solid fa-sliders"></i> Update Preferences
                                </a>
                                <a href="{{ route('account.premium.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

                        @if ($preferences && ($preferences->desired_job_title || $preferences->job_keywords || !empty($preferences->preferred_categories)))
                            <div class="alert alert-info">
                                <strong>Matching criteria:</strong>
                                @if ($preferences->desired_job_title)
                                    Title: "{{ $preferences->desired_job_title }}"
                                @endif
                                @if ($preferences->job_keywords)
                                    | Keywords: {{ $preferences->job_keywords }}
                                @endif
                                @if (!empty($preferences->preferred_categories))
                                    | {{ count($preferences->preferred_categories) }} categories selected
                                @endif
                            </div>
                        @endif

                        @if ($matches->isEmpty())
                            <div class="text-center py-5">
                                <i class="fa-solid fa-search fa-4x text-muted mb-3"></i>
                                <h4>No matching jobs found</h4>
                                <p class="text-muted">Try adjusting your job preferences to find more matches.</p>
                                <a href="{{ route('account.premium.preferences') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-sliders"></i> Update Preferences
                                </a>
                            </div>
                        @else
                            <p class="text-muted mb-3">Found {{ $matches->total() }} jobs matching your preferences</p>

                            <div class="list-group">
                                @foreach ($matches as $job)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="{{ \App\Helpers\UrlGen::post($job) }}" target="_blank">
                                                    {{ $job->title }}
                                                </a>
                                            </h5>
                                            <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted">
                                            @if ($job->company_name)
                                                <i class="fa-regular fa-building me-1"></i> {{ $job->company_name }}
                                            @endif
                                            @if ($job->city)
                                                <i class="fa-solid fa-location-dot ms-2 me-1"></i> {{ $job->city->name }}
                                            @endif
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if ($job->category)
                                                    <span class="badge bg-secondary">{{ $job->category->name }}</span>
                                                @endif
                                                @if ($job->postType)
                                                    <span class="badge bg-info">{{ $job->postType->name }}</span>
                                                @endif
                                                @if ($job->salary_min || $job->salary_max)
                                                    <span class="badge bg-success">
                                                        @if ($job->salary_min && $job->salary_max)
                                                            {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                                        @elseif ($job->salary_min)
                                                            From {{ number_format($job->salary_min) }}
                                                        @else
                                                            Up to {{ number_format($job->salary_max) }}
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ \App\Helpers\UrlGen::post($job) }}" class="btn btn-primary btn-sm" target="_blank">
                                                View Job <i class="fa-solid fa-external-link-alt ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $matches->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
