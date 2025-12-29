@extends('layouts.master')

@php
    $profile ??= null;
@endphp

@section('content')
    @includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">

                @if (session()->has('flash_notification'))
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                @include('flash::message')
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-3 page-sidebar">
                    @includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
                </div>

                <div class="col-md-9 page-content">
                    <div class="inner-box">
                        <h2 class="title-2">
                            <i class="fa-solid fa-user-tie"></i> {{ t('My Worker Profile') }}
                        </h2>

                        @if($profile)
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <img src="{{ $profile->photo_url }}"
                                                     alt="{{ $profile->title }}"
                                                     class="rounded-circle me-3"
                                                     style="width: 100px; height: 100px; object-fit: cover;">
                                                <div>
                                                    <h4 class="mb-1">{{ $profile->title }}</h4>
                                                    <p class="text-muted mb-2">
                                                        @if($profile->city)
                                                            <i class="fa-solid fa-location-dot"></i>
                                                            {{ $profile->city->name }}
                                                            @if($profile->district)
                                                                , {{ $profile->district }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                    <span class="badge {{ $profile->availability_badge_class }}">
                                                        {{ $profile->availability_status_formatted }}
                                                    </span>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>{{ t('Experience') }}:</strong>
                                                        {{ $profile->experience_years ?? 0 }} {{ t('years') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>{{ t('Hourly Rate') }}:</strong>
                                                        @if($profile->hourly_rate)
                                                            {{ $profile->currency_code ?? 'MWK' }} {{ number_format($profile->hourly_rate, 2) }}
                                                        @else
                                                            {{ t('Negotiable') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            @if($profile->bio)
                                                <div class="mt-3">
                                                    <strong>{{ t('About') }}:</strong>
                                                    <p>{{ $profile->bio }}</p>
                                                </div>
                                            @endif

                                            @if($profile->skills->count() > 0 || $profile->custom_skills)
                                                <div class="mt-3">
                                                    <strong>{{ t('Skills') }}:</strong>
                                                    <div class="mt-2">
                                                        @foreach($profile->skills as $skill)
                                                            <span class="badge bg-primary me-1 mb-1">
                                                                <i class="{{ $skill->icon ?? 'fa-solid fa-check' }}"></i>
                                                                {{ $skill->name }}
                                                            </span>
                                                        @endforeach
                                                        @if($profile->custom_skills)
                                                            @foreach(explode(',', $profile->custom_skills) as $customSkill)
                                                                <span class="badge bg-secondary me-1 mb-1">
                                                                    {{ trim($customSkill) }}
                                                                </span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="mt-3">
                                                <strong>{{ t('Contact Information') }}:</strong>
                                                <ul class="list-unstyled mt-2">
                                                    @if($profile->phone)
                                                        <li><i class="fa-solid fa-phone"></i> {{ $profile->phone }}</li>
                                                    @endif
                                                    @if($profile->email)
                                                        <li><i class="fa-solid fa-envelope"></i> {{ $profile->email }}</li>
                                                    @endif
                                                    @if($profile->whatsapp)
                                                        <li><i class="fa-brands fa-whatsapp"></i> {{ $profile->whatsapp }}</li>
                                                    @endif
                                                </ul>
                                            </div>

                                            <div class="mt-3">
                                                <strong>{{ t('Profile Views') }}:</strong> {{ number_format($profile->views) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>{{ t('Profile Visibility') }}</strong>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-3">
                                                @if($profile->is_public)
                                                    <span class="badge bg-success">
                                                        <i class="fa-solid fa-eye"></i> {{ t('Public') }}
                                                    </span>
                                                    <br><small class="text-muted">{{ t('Employers can see your profile') }}</small>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fa-solid fa-eye-slash"></i> {{ t('Private') }}
                                                    </span>
                                                    <br><small class="text-muted">{{ t('Only you can see your profile') }}</small>
                                                @endif
                                            </p>

                                            <form action="{{ route('account.worker-profile.toggle') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $profile->is_public ? 'btn-warning' : 'btn-success' }}">
                                                    @if($profile->is_public)
                                                        <i class="fa-solid fa-eye-slash"></i> {{ t('Make Private') }}
                                                    @else
                                                        <i class="fa-solid fa-eye"></i> {{ t('Make Public') }}
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <strong>{{ t('Actions') }}</strong>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ route('account.worker-profile.edit') }}" class="btn btn-primary btn-block mb-2 w-100">
                                                <i class="fa-solid fa-pen-to-square"></i> {{ t('Edit Profile') }}
                                            </a>
                                            @if($profile->is_public)
                                                <a href="{{ route('workers.show', $profile->id) }}" class="btn btn-outline-primary btn-block mb-2 w-100" target="_blank">
                                                    <i class="fa-solid fa-external-link"></i> {{ t('View Public Profile') }}
                                                </a>
                                            @endif
                                            <form action="{{ route('account.worker-profile.destroy') }}" method="POST"
                                                  onsubmit="return confirm('{{ t('Are you sure you want to delete your worker profile?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-block w-100">
                                                    <i class="fa-solid fa-trash"></i> {{ t('Delete Profile') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa-solid fa-info-circle"></i>
                                {{ t('You have not created a worker profile yet.') }}
                                <a href="{{ route('account.worker-profile.create') }}" class="alert-link">
                                    {{ t('Create one now') }}
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
