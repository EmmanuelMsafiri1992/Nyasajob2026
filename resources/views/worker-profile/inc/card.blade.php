@php
    $profile ??= null;
@endphp

@if($profile)
<div class="card h-100 worker-profile-card">
    <div class="card-body">
        <div class="text-center mb-3">
            <img src="{{ $profile->photo_url }}"
                 alt="{{ $profile->title }}"
                 class="rounded-circle"
                 style="width: 80px; height: 80px; object-fit: cover;">
        </div>

        <h5 class="card-title text-center mb-2">
            {{ $profile->user->name ?? 'Worker' }}
        </h5>

        <p class="text-muted text-center small mb-2">
            {{ str($profile->title)->limit(50) }}
        </p>

        <div class="text-center mb-2">
            <span class="badge {{ $profile->availability_badge_class }}">
                {{ $profile->availability_status_formatted }}
            </span>
        </div>

        @if($profile->city || $profile->district)
            <p class="small text-center mb-2">
                <i class="fa-solid fa-location-dot text-muted"></i>
                {{ $profile->district ?? '' }}{{ $profile->district && $profile->city ? ', ' : '' }}{{ $profile->city->name ?? '' }}
            </p>
        @endif

        @if($profile->experience_years)
            <p class="small text-center mb-2">
                <i class="fa-solid fa-briefcase text-muted"></i>
                {{ $profile->experience_years }} {{ t('years experience') }}
            </p>
        @endif

        @if($profile->skills->count() > 0)
            <div class="skills-preview text-center mb-3">
                @foreach($profile->skills->take(3) as $skill)
                    <span class="badge bg-light text-dark me-1 mb-1">
                        {{ $skill->name }}
                    </span>
                @endforeach
                @if($profile->skills->count() > 3)
                    <span class="badge bg-secondary">
                        +{{ $profile->skills->count() - 3 }}
                    </span>
                @endif
            </div>
        @endif

        <div class="text-center">
            <a href="{{ route('workers.show', $profile->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-eye"></i> {{ t('View Profile') }}
            </a>
        </div>
    </div>
</div>
@endif

<style>
.worker-profile-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.worker-profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
