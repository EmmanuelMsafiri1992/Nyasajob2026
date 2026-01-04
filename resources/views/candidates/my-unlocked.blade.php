@extends('layouts.master')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .page-header h1 { font-size: 1.75rem; margin-bottom: 0.25rem; }

    .unlocked-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .candidate-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #4f46e5;
    }
    .candidate-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }

    .contact-pills {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }
    .contact-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #f3f4f6;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #374151;
    }
    .contact-pill i { color: #4f46e5; }
    .contact-pill a { color: inherit; text-decoration: none; }
    .contact-pill a:hover { color: #4f46e5; }

    .unlocked-date { font-size: 0.8rem; color: #9ca3af; }

    .stats-bar {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .stat { text-align: center; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #4f46e5; }
    .stat-label { font-size: 0.85rem; color: #6b7280; }
</style>

<div class="page-header">
    <div class="container">
        <h1>My Unlocked Candidates</h1>
        <p class="mb-0 opacity-75">View all candidates whose contact details you've unlocked.</p>
    </div>
</div>

<div class="container">
    <div class="stats-bar">
        <div class="row">
            <div class="col-4">
                <div class="stat">
                    <div class="stat-value">{{ $unlocked->count() }}</div>
                    <div class="stat-label">Total Unlocked</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat">
                    <div class="stat-value">{{ $userCredits }}</div>
                    <div class="stat-label">Credits Remaining</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat text-end">
                    <a href="{{ route('candidates.packages') }}" class="btn btn-primary btn-sm">Buy Credits</a>
                    <a href="{{ route('candidates.index') }}" class="btn btn-outline-primary btn-sm">Browse More</a>
                </div>
            </div>
        </div>
    </div>

    @forelse($unlocked as $view)
        @if($view->workerProfile)
            <div class="unlocked-card">
                <div class="d-flex align-items-start gap-3">
                    <div class="candidate-avatar">
                        @if($view->workerProfile->user && $view->workerProfile->user->photo_url)
                            <img src="{{ $view->workerProfile->user->photo_url }}" alt="">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-0">{{ $view->workerProfile->title ?: 'Professional' }}</h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $view->workerProfile->city?->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="unlocked-date">
                                    Unlocked {{ $view->contact_unlocked_at?->diffForHumans() ?? $view->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="contact-pills">
                            @if($view->workerProfile->email || $view->workerProfile->user?->email)
                                <span class="contact-pill">
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:{{ $view->workerProfile->email ?: $view->workerProfile->user?->email }}">
                                        {{ $view->workerProfile->email ?: $view->workerProfile->user?->email }}
                                    </a>
                                </span>
                            @endif
                            @if($view->workerProfile->phone)
                                <span class="contact-pill">
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:{{ $view->workerProfile->phone }}">{{ $view->workerProfile->phone }}</a>
                                </span>
                            @endif
                            @if($view->workerProfile->whatsapp)
                                <span class="contact-pill">
                                    <i class="fab fa-whatsapp"></i>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $view->workerProfile->whatsapp) }}" target="_blank">
                                        {{ $view->workerProfile->whatsapp }}
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('candidates.show', $view->workerProfile->id) }}" class="btn btn-outline-primary btn-sm">
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="text-center py-5">
            <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
            <h4>No unlocked candidates yet</h4>
            <p class="text-muted">Browse our candidate database and unlock contact details.</p>
            <a href="{{ route('candidates.index') }}" class="btn btn-primary">Browse Candidates</a>
        </div>
    @endforelse

    @if($unlocked instanceof \Illuminate\Pagination\LengthAwarePaginator && $unlocked->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $unlocked->links() }}
        </div>
    @endif
</div>
@endsection
