@extends('layouts.master')

@section('content')
<style>
    .candidate-hero {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
    }
    .candidate-hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
    .candidate-hero p { opacity: 0.9; }

    .candidate-filters {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }

    .candidate-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .candidate-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .candidate-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #4f46e5;
        margin-right: 1.5rem;
    }

    .candidate-title { font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem; }
    .candidate-location { color: #6b7280; font-size: 0.9rem; }
    .candidate-bio { color: #4b5563; margin: 0.75rem 0; line-height: 1.6; }

    .skill-tag {
        display: inline-block;
        background: #e0e7ff;
        color: #4338ca;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .candidate-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    .meta-item { display: flex; align-items: center; gap: 0.5rem; color: #6b7280; font-size: 0.9rem; }
    .meta-item i { color: #9ca3af; }

    .unlock-btn {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: opacity 0.2s;
    }
    .unlock-btn:hover { opacity: 0.9; }
    .unlock-btn.unlocked { background: #10b981; }

    .credits-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
    }

    .sidebar-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }

    .availability-available { color: #10b981; }
    .availability-busy { color: #f59e0b; }
    .availability-not_available { color: #ef4444; }
</style>

<div class="candidate-hero">
    <div class="container">
        <h1>Candidate Database</h1>
        <p>Find qualified candidates for your positions. Browse profiles and unlock contact details.</p>
        <div class="d-flex align-items-center gap-3 mt-3">
            <span class="credits-badge">
                <i class="fas fa-coins me-1"></i>
                {{ $userCredits }} Credits Available
            </span>
            <a href="{{ route('candidates.packages') }}" class="btn btn-light btn-sm">Buy Credits</a>
            @auth
                <a href="{{ route('candidates.my-unlocked') }}" class="btn btn-outline-light btn-sm">My Unlocked</a>
            @endauth
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <div class="sidebar-card">
                <h5 class="mb-3">Filters</h5>
                <form method="GET" action="{{ route('candidates.index') }}">
                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" class="form-control" placeholder="Job title, skills..."
                               value="{{ $filters['q'] ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Skill</label>
                        <select name="skill" class="form-select">
                            <option value="">All Skills</option>
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ ($filters['skill'] ?? '') == $skill->id ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <select name="city" class="form-select">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ ($filters['city'] ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-select">
                            <option value="">Any</option>
                            <option value="available" {{ ($filters['availability'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="busy" {{ ($filters['availability'] ?? '') == 'busy' ? 'selected' : '' }}>Busy</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Min Experience (Years)</label>
                        <input type="number" name="experience_min" class="form-control" min="0"
                               value="{{ $filters['experience_min'] ?? '' }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear</a>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">{{ $candidates->total() }} candidates found</span>
                <select name="sort" class="form-select" style="width: auto;" onchange="location = this.value;">
                    <option value="{{ route('candidates.index', array_merge($filters, ['sort' => 'recent'])) }}" {{ ($filters['sort'] ?? 'recent') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                    <option value="{{ route('candidates.index', array_merge($filters, ['sort' => 'experience'])) }}" {{ ($filters['sort'] ?? '') == 'experience' ? 'selected' : '' }}>Most Experienced</option>
                    <option value="{{ route('candidates.index', array_merge($filters, ['sort' => 'featured'])) }}" {{ ($filters['sort'] ?? '') == 'featured' ? 'selected' : '' }}>Featured</option>
                </select>
            </div>

            @forelse($candidates as $candidate)
                <div class="candidate-card">
                    <div class="d-flex">
                        <div class="candidate-avatar">
                            @if($candidate->user && $candidate->user->photo_url)
                                <img src="{{ $candidate->user->photo_url }}" alt="" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="candidate-title">{{ $candidate->title ?: 'Professional' }}</h3>
                                    <p class="candidate-location">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $candidate->city?->name ?? 'Location not specified' }}
                                    </p>
                                </div>
                                <span class="availability-{{ $candidate->availability_status ?? 'available' }}">
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    {{ ucfirst($candidate->availability_status ?? 'available') }}
                                </span>
                            </div>

                            @if($candidate->bio)
                                <p class="candidate-bio">{{ Str::limit($candidate->bio, 150) }}</p>
                            @endif

                            <div class="skills-list">
                                @foreach($candidate->skills->take(5) as $skill)
                                    <span class="skill-tag">{{ $skill->name }}</span>
                                @endforeach
                                @if($candidate->skills->count() > 5)
                                    <span class="skill-tag">+{{ $candidate->skills->count() - 5 }} more</span>
                                @endif
                            </div>

                            <div class="candidate-meta">
                                <span class="meta-item">
                                    <i class="fas fa-briefcase"></i>
                                    {{ $candidate->experience_years ?? 0 }} years experience
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ $candidate->views_count ?? 0 }} views
                                </span>
                            </div>
                        </div>
                        <div class="ms-3 text-end">
                            @if(in_array($candidate->id, $unlockedIds))
                                <a href="{{ route('candidates.show', $candidate->id) }}" class="unlock-btn unlocked">
                                    <i class="fas fa-unlock me-1"></i> View Contact
                                </a>
                            @else
                                <a href="{{ route('candidates.show', $candidate->id) }}" class="unlock-btn">
                                    <i class="fas fa-eye me-1"></i> View Profile
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>No candidates found</h4>
                    <p class="text-muted">Try adjusting your filters to see more results.</p>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $candidates->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
