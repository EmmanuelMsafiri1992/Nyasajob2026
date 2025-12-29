@extends('layouts.master')

@section('content')
    @includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <h1 class="title-2 mb-4">
                        <i class="fa-solid fa-users"></i> {{ t('Available Workers') }}
                    </h1>
                </div>
            </div>

            <div class="row">
                {{-- Filters Sidebar --}}
                <div class="col-md-3">
                    <div class="inner-box">
                        <h4 class="title-3">{{ t('Filters') }}</h4>

                        <form method="GET" action="{{ route('workers.index') }}">
                            {{-- Search --}}
                            <div class="mb-3">
                                <label class="form-label">{{ t('Search') }}</label>
                                <input type="text" name="q" class="form-control"
                                       value="{{ request('q') }}"
                                       placeholder="{{ t('Search workers...') }}">
                            </div>

                            {{-- Skill Filter --}}
                            <div class="mb-3">
                                <label class="form-label">{{ t('Skill') }}</label>
                                <select name="skill_id" class="form-control">
                                    <option value="">{{ t('All Skills') }}</option>
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill->id }}"
                                            {{ request('skill_id') == $skill->id ? 'selected' : '' }}>
                                            {{ $skill->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- City Filter --}}
                            <div class="mb-3">
                                <label class="form-label">{{ t('City') }}</label>
                                <select name="city_id" class="form-control">
                                    <option value="">{{ t('All Cities') }}</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Availability Filter --}}
                            <div class="mb-3">
                                <label class="form-label">{{ t('Availability') }}</label>
                                <select name="availability" class="form-control">
                                    <option value="">{{ t('All') }}</option>
                                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>
                                        {{ t('Available') }}
                                    </option>
                                    <option value="busy" {{ request('availability') == 'busy' ? 'selected' : '' }}>
                                        {{ t('Busy') }}
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-filter"></i> {{ t('Apply Filters') }}
                            </button>

                            @if(request()->hasAny(['q', 'skill_id', 'city_id', 'availability']))
                                <a href="{{ route('workers.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fa-solid fa-times"></i> {{ t('Clear Filters') }}
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Workers Grid --}}
                <div class="col-md-9">
                    @if($profiles->count() > 0)
                        <div class="row">
                            @foreach($profiles as $profile)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    @include('worker-profile.inc.card', ['profile' => $profile])
                                </div>
                            @endforeach
                        </div>

                        <div class="pagination-bar text-center mt-4">
                            {{ $profiles->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle"></i>
                            {{ t('No workers found matching your criteria.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
