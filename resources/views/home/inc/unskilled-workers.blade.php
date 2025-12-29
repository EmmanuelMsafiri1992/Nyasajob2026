@php
    $sectionOptions = $getUnskilledWorkersOp ?? [];
    $sectionData ??= [];
    $unskilledJobs = (array)data_get($sectionData, 'unskilledJobs');
    $workerProfiles = (array)data_get($sectionData, 'workerProfiles');

    $jobs = (array)data_get($unskilledJobs, 'posts', []);
    $profiles = (array)data_get($workerProfiles, 'profiles', []);

    $hideOnMobile = (data_get($sectionOptions, 'hide_on_mobile') == '1') ? ' hidden-sm' : '';
@endphp

@includeFirst([
    config('larapen.core.customizedViewPath') . 'home.inc.spacer',
    'home.inc.spacer'
], ['hideOnMobile' => $hideOnMobile])

<div class="container{{ $hideOnMobile }}">
    <div class="row">
        {{-- Unskilled Labor Jobs (Left Column) --}}
        <div class="col-md-6 mb-4">
            <div class="col-xl-12 content-box layout-section h-100">
                <div class="row row-featured row-featured-category">
                    <div class="col-xl-12 box-title no-border">
                        <div class="inner">
                            <h2>
                                <span class="title-3">
                                    <i class="fa-solid fa-briefcase"></i>
                                    {!! data_get($unskilledJobs, 'title', t('Unskilled Labor Jobs')) !!}
                                </span>
                                <a href="{{ data_get($unskilledJobs, 'link', url('search')) }}" class="sell-your-item">
                                    {{ t('View more') }} <i class="fa-solid fa-bars"></i>
                                </a>
                            </h2>
                        </div>
                    </div>

                    <div class="posts-wrapper jobs-list p-3">
                        @if (!empty($jobs))
                            @foreach($jobs as $job)
                                <div class="item-list job-item mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-2">
                                            <a href="{{ data_get($job, 'url') }}">
                                                <img src="{{ data_get($job, 'logo_url.small') }}"
                                                     alt="{{ data_get($job, 'title') }}"
                                                     class="img-fluid rounded"
                                                     style="max-height: 50px;">
                                            </a>
                                        </div>
                                        <div class="col-10">
                                            <h6 class="mb-1">
                                                <a href="{{ data_get($job, 'url') }}" class="text-dark">
                                                    {{ str(data_get($job, 'title'))->limit(40) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                @if(data_get($job, 'company_name'))
                                                    <i class="fa-regular fa-building"></i>
                                                    {{ data_get($job, 'company_name') }}
                                                @endif
                                                @if(data_get($job, 'city.name'))
                                                    <i class="fa-solid fa-location-dot ms-2"></i>
                                                    {{ data_get($job, 'city.name') }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">
                                {{ t('No jobs available at the moment.') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-12 text-center py-3">
                        <a href="{{ data_get($unskilledJobs, 'link', url('search')) }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-search"></i> {{ t('Browse All Jobs') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Workers (Right Column) --}}
        <div class="col-md-6 mb-4">
            <div class="col-xl-12 content-box layout-section h-100">
                <div class="row row-featured row-featured-category">
                    <div class="col-xl-12 box-title no-border">
                        <div class="inner">
                            <h2>
                                <span class="title-3">
                                    <i class="fa-solid fa-users"></i>
                                    {!! data_get($workerProfiles, 'title', t('Available Workers')) !!}
                                </span>
                                <a href="{{ data_get($workerProfiles, 'link', url('workers')) }}" class="sell-your-item">
                                    {{ t('View more') }} <i class="fa-solid fa-bars"></i>
                                </a>
                            </h2>
                        </div>
                    </div>

                    <div class="posts-wrapper p-3">
                        @if (!empty($profiles))
                            <div class="row">
                                @foreach($profiles as $profile)
                                    <div class="col-6 mb-3">
                                        <div class="card h-100 worker-card text-center p-2">
                                            <img src="{{ data_get($profile, 'photo_url') }}"
                                                 alt="{{ data_get($profile, 'user_name') }}"
                                                 class="rounded-circle mx-auto mb-2"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            <h6 class="mb-1 small">
                                                {{ str(data_get($profile, 'user_name'))->limit(15) }}
                                            </h6>
                                            <p class="text-muted mb-1" style="font-size: 11px;">
                                                {{ str(data_get($profile, 'title'))->limit(25) }}
                                            </p>
                                            <span class="badge {{ data_get($profile, 'availability_badge_class') }} mb-1" style="font-size: 10px;">
                                                {{ ucfirst(data_get($profile, 'availability_status')) }}
                                            </span>
                                            @if(data_get($profile, 'city_name'))
                                                <small class="text-muted d-block" style="font-size: 10px;">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    {{ data_get($profile, 'city_name') }}
                                                </small>
                                            @endif
                                            <a href="{{ url('workers/' . data_get($profile, 'id')) }}" class="btn btn-sm btn-outline-primary mt-2" style="font-size: 11px;">
                                                {{ t('View') }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">
                                {{ t('No workers available at the moment.') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-12 text-center py-3">
                        <a href="{{ url('workers') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-users"></i> {{ t('Browse All Workers') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .worker-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eee;
    }
    .worker-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
</style>
