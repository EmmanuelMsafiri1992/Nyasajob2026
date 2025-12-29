@extends('layouts.master')

@section('content')
    @includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">
                {{-- Main Profile Content --}}
                <div class="col-md-8">
                    <div class="inner-box">
                        {{-- Profile Header --}}
                        <div class="d-flex align-items-start mb-4">
                            <img src="{{ $profile->photo_url }}"
                                 alt="{{ $profile->title }}"
                                 class="rounded-circle me-4"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <div>
                                <h1 class="h3 mb-2">{{ $profile->user->name ?? 'Worker' }}</h1>
                                <p class="text-muted mb-2">{{ $profile->title }}</p>
                                <span class="badge {{ $profile->availability_badge_class }} mb-2">
                                    {{ $profile->availability_status_formatted }}
                                </span>

                                @if($profile->city || $profile->district)
                                    <p class="mb-1">
                                        <i class="fa-solid fa-location-dot text-muted"></i>
                                        {{ $profile->district ?? '' }}{{ $profile->district && $profile->city ? ', ' : '' }}{{ $profile->city->name ?? '' }}
                                    </p>
                                @endif

                                @if($profile->experience_years)
                                    <p class="mb-0">
                                        <i class="fa-solid fa-briefcase text-muted"></i>
                                        {{ $profile->experience_years }} {{ t('years of experience') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        {{-- About Section --}}
                        @if($profile->bio)
                            <div class="mb-4">
                                <h4><i class="fa-solid fa-user"></i> {{ t('About') }}</h4>
                                <p>{{ $profile->bio }}</p>
                            </div>
                        @endif

                        {{-- Skills Section --}}
                        @if($profile->skills->count() > 0 || $profile->custom_skills)
                            <div class="mb-4">
                                <h4><i class="fa-solid fa-star"></i> {{ t('Skills & Services') }}</h4>
                                <div class="mt-2">
                                    @foreach($profile->skills as $skill)
                                        <span class="badge bg-primary me-1 mb-2">
                                            <i class="{{ $skill->icon ?? 'fa-solid fa-check' }}"></i>
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                    @if($profile->custom_skills)
                                        @foreach(explode(',', $profile->custom_skills) as $customSkill)
                                            <span class="badge bg-secondary me-1 mb-2">
                                                {{ trim($customSkill) }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Rate Section --}}
                        @if($profile->hourly_rate)
                            <div class="mb-4">
                                <h4><i class="fa-solid fa-money-bill"></i> {{ t('Expected Rate') }}</h4>
                                <p class="h5">
                                    {{ $profile->currency_code ?? 'MWK' }} {{ number_format($profile->hourly_rate, 2) }}
                                    <small class="text-muted">/ {{ t('hour') }}</small>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-md-4">
                    {{-- Contact Details Card --}}
                    <div class="inner-box">
                        <h4><i class="fa-solid fa-address-book"></i> {{ t('Contact Information') }}</h4>

                        @if($canViewContact)
                            <ul class="list-unstyled mt-3">
                                @if($contactDetails['phone'])
                                    <li class="mb-2">
                                        <i class="fa-solid fa-phone text-primary"></i>
                                        <a href="tel:{{ $contactDetails['phone'] }}">{{ $contactDetails['phone'] }}</a>
                                    </li>
                                @endif
                                @if($contactDetails['email'])
                                    <li class="mb-2">
                                        <i class="fa-solid fa-envelope text-primary"></i>
                                        <a href="mailto:{{ $contactDetails['email'] }}">{{ $contactDetails['email'] }}</a>
                                    </li>
                                @endif
                                @if($contactDetails['whatsapp'])
                                    <li class="mb-2">
                                        <i class="fa-brands fa-whatsapp text-success"></i>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactDetails['whatsapp']) }}" target="_blank">
                                            {{ $contactDetails['whatsapp'] }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        @else
                            <div class="alert alert-warning mt-3">
                                <i class="fa-solid fa-lock"></i>
                                <strong>{{ t('Contact details hidden') }}</strong>
                                <p class="mb-0 mt-2">
                                    {{ t('To view contact information, you need to be a verified employer with an active subscription.') }}
                                </p>
                            </div>

                            @guest
                                <a href="{{ url('login') }}" class="btn btn-primary w-100 mb-2">
                                    <i class="fa-solid fa-sign-in-alt"></i> {{ t('Login') }}
                                </a>
                                <a href="{{ url('register') }}" class="btn btn-outline-primary w-100">
                                    <i class="fa-solid fa-user-plus"></i> {{ t('Register as Employer') }}
                                </a>
                            @else
                                <a href="{{ route('subscription.pricing') }}" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-crown"></i> {{ t('Upgrade to View Contact') }}
                                </a>
                            @endguest
                        @endif
                    </div>

                    {{-- Profile Stats --}}
                    <div class="inner-box mt-3">
                        <h4><i class="fa-solid fa-chart-bar"></i> {{ t('Profile Stats') }}</h4>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2">
                                <i class="fa-solid fa-eye text-muted"></i>
                                {{ number_format($profile->views) }} {{ t('profile views') }}
                            </li>
                            <li class="mb-2">
                                <i class="fa-solid fa-calendar text-muted"></i>
                                {{ t('Member since') }} {{ $profile->created_at->format('M Y') }}
                            </li>
                        </ul>
                    </div>

                    @if($isOwner)
                        <div class="inner-box mt-3">
                            <a href="{{ route('account.worker-profile.edit') }}" class="btn btn-outline-primary w-100">
                                <i class="fa-solid fa-pen-to-square"></i> {{ t('Edit My Profile') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Similar Workers --}}
            @if($similarProfiles->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="inner-box">
                            <h4><i class="fa-solid fa-users"></i> {{ t('Similar Workers') }}</h4>
                            <div class="row mt-3">
                                @foreach($similarProfiles as $similar)
                                    <div class="col-md-3 col-6 mb-3">
                                        @include('worker-profile.inc.card', ['profile' => $similar])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
