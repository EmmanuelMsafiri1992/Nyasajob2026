@extends('layouts.master')

@section('content')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #4f46e5;
        border: 4px solid rgba(255,255,255,0.3);
    }
    .profile-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }

    .profile-title { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
    .profile-location { opacity: 0.9; }

    .profile-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }
    .profile-card h4 { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937; }

    .skill-tag {
        display: inline-block;
        background: #e0e7ff;
        color: #4338ca;
        padding: 0.35rem 0.9rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }
    .contact-item i { font-size: 1.25rem; color: #4f46e5; width: 24px; }
    .contact-item .label { font-size: 0.85rem; color: #6b7280; }
    .contact-item .value { font-weight: 500; color: #1f2937; }

    .unlock-section {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
    }
    .unlock-section h4 { color: #92400e; margin-bottom: 0.5rem; }
    .unlock-section p { color: #a16207; margin-bottom: 1rem; }

    .unlock-btn {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        font-size: 1rem;
    }
    .unlock-btn:hover { opacity: 0.9; }
    .unlock-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .stat-item {
        text-align: center;
        padding: 1rem;
    }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #4f46e5; }
    .stat-label { font-size: 0.85rem; color: #6b7280; }

    .availability-badge {
        display: inline-block;
        padding: 0.35rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .availability-available { background: #d1fae5; color: #065f46; }
    .availability-busy { background: #fef3c7; color: #92400e; }
    .availability-not_available { background: #fee2e2; color: #991b1b; }

    .related-candidate {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .related-candidate:hover { transform: translateY(-2px); }
</style>

<div class="profile-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-4">
            <div class="profile-avatar">
                @if($candidate->user && $candidate->user->photo_url)
                    <img src="{{ $candidate->user->photo_url }}" alt="">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <div class="flex-grow-1">
                <h1 class="profile-title">{{ $candidate->title ?: 'Professional' }}</h1>
                <p class="profile-location">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ $candidate->city?->name ?? 'Location not specified' }}
                    @if($candidate->country)
                        , {{ $candidate->country->name }}
                    @endif
                </p>
                <div class="mt-2">
                    <span class="availability-badge availability-{{ $candidate->availability_status ?? 'available' }}">
                        {{ ucfirst($candidate->availability_status ?? 'Available') }}
                    </span>
                </div>
            </div>
            <div class="text-end">
                <div class="credits-badge mb-2" style="background: rgba(255,255,255,0.2); color: white; padding: 0.5rem 1rem; border-radius: 8px;">
                    <i class="fas fa-coins me-1"></i>
                    {{ $userCredits }} Credits
                </div>
                <a href="{{ route('candidates.packages') }}" class="btn btn-light btn-sm">Buy More</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="profile-card">
                <h4><i class="fas fa-user me-2"></i>About</h4>
                <p style="line-height: 1.8; color: #4b5563;">
                    {{ $candidate->bio ?: 'No bio provided.' }}
                </p>
            </div>

            <div class="profile-card">
                <h4><i class="fas fa-star me-2"></i>Skills</h4>
                @forelse($candidate->skills as $skill)
                    <span class="skill-tag">{{ $skill->name }}</span>
                @empty
                    <p class="text-muted">No skills listed.</p>
                @endforelse
                @if($candidate->custom_skills)
                    <div class="mt-3">
                        <strong class="text-muted">Other Skills:</strong>
                        <p class="mb-0">{{ $candidate->custom_skills }}</p>
                    </div>
                @endif
            </div>

            @if($candidate->experience)
                <div class="profile-card">
                    <h4><i class="fas fa-briefcase me-2"></i>Experience</h4>
                    <div style="line-height: 1.8; color: #4b5563;">
                        {!! nl2br(e($candidate->experience)) !!}
                    </div>
                </div>
            @endif

            @if($candidate->education)
                <div class="profile-card">
                    <h4><i class="fas fa-graduation-cap me-2"></i>Education</h4>
                    <div style="line-height: 1.8; color: #4b5563;">
                        {!! nl2br(e($candidate->education)) !!}
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="profile-card">
                <h4><i class="fas fa-chart-bar me-2"></i>Overview</h4>
                <div class="row">
                    <div class="col-6">
                        <div class="stat-item">
                            <div class="stat-value">{{ $candidate->experience_years ?? 0 }}</div>
                            <div class="stat-label">Years Exp.</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-item">
                            <div class="stat-value">{{ $candidate->views_count ?? 0 }}</div>
                            <div class="stat-label">Profile Views</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <h4><i class="fas fa-address-book me-2"></i>Contact Information</h4>

                @if($isUnlocked)
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <div class="label">Email</div>
                            <div class="value">{{ $candidate->email ?: $candidate->user?->email ?: 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <div class="label">Phone</div>
                            <div class="value">{{ $candidate->phone ?: 'Not provided' }}</div>
                        </div>
                    </div>
                    @if($candidate->whatsapp)
                        <div class="contact-item">
                            <i class="fab fa-whatsapp"></i>
                            <div>
                                <div class="label">WhatsApp</div>
                                <div class="value">{{ $candidate->whatsapp }}</div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="unlock-section">
                        <i class="fas fa-lock fa-2x mb-3" style="color: #92400e;"></i>
                        <h4>Contact Details Locked</h4>
                        <p>Use 1 credit to unlock this candidate's contact information.</p>

                        @auth
                            @if($userCredits > 0)
                                <button class="unlock-btn" id="unlockBtn" onclick="unlockCandidate({{ $candidate->id }})">
                                    <i class="fas fa-unlock me-1"></i> Unlock Contact (1 Credit)
                                </button>
                            @else
                                <a href="{{ route('candidates.packages') }}" class="unlock-btn">
                                    <i class="fas fa-coins me-1"></i> Buy Credits to Unlock
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="unlock-btn">
                                <i class="fas fa-sign-in-alt me-1"></i> Login to Unlock
                            </a>
                        @endauth
                    </div>
                @endif
            </div>

            @if($candidate->expected_salary)
                <div class="profile-card">
                    <h4><i class="fas fa-money-bill-wave me-2"></i>Salary Expectation</h4>
                    <p class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #4f46e5;">
                        {{ $candidate->expected_salary }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if($relatedCandidates->count() > 0)
        <div class="mt-4">
            <h4 class="mb-3">Similar Candidates</h4>
            <div class="row">
                @foreach($relatedCandidates as $related)
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('candidates.show', $related->id) }}" class="text-decoration-none">
                            <div class="related-candidate">
                                <h6 class="mb-1">{{ $related->title ?: 'Professional' }}</h6>
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $related->city?->name ?? 'N/A' }}
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@auth
<script>
function unlockCandidate(candidateId) {
    const btn = document.getElementById('unlockBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Unlocking...';

    fetch('{{ url("candidates") }}/' + candidateId + '/unlock', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to unlock contact.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-unlock me-1"></i> Unlock Contact (1 Credit)';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-unlock me-1"></i> Unlock Contact (1 Credit)';
    });
}
</script>
@endauth
@endsection
