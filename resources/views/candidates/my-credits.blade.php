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

    .credit-summary {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .credit-summary .total { font-size: 3rem; font-weight: 800; color: #4f46e5; }
    .credit-summary .label { color: #6b7280; }

    .credit-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .credit-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .credit-badge.active { background: #d1fae5; color: #065f46; }
    .credit-badge.expired { background: #fee2e2; color: #991b1b; }
    .credit-badge.exhausted { background: #fef3c7; color: #92400e; }

    .progress-bar-credits {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .progress-bar-credits .fill {
        height: 100%;
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        border-radius: 4px;
    }
</style>

<div class="page-header">
    <div class="container">
        <h1>My Credits History</h1>
        <p class="mb-0 opacity-75">Track your credit purchases and usage.</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="credit-summary">
                <div class="total">{{ $totalCredits }}</div>
                <div class="label">Available Credits</div>
                <div class="mt-3">
                    <a href="{{ route('candidates.packages') }}" class="btn btn-primary">Buy More Credits</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Links</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="{{ route('candidates.index') }}" class="text-decoration-none">
                                <i class="fas fa-users me-2"></i> Browse Candidates
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('candidates.my-unlocked') }}" class="text-decoration-none">
                                <i class="fas fa-unlock me-2"></i> My Unlocked Candidates
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('candidates.packages') }}" class="text-decoration-none">
                                <i class="fas fa-box me-2"></i> View Packages
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <h5 class="mb-3">Credit History</h5>

            @forelse($credits as $credit)
                <div class="credit-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $credit->resumePackage?->name ?? 'Credit Package' }}</h6>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                Purchased {{ $credit->created_at->format('M d, Y') }}
                                @if($credit->payment_method)
                                    via {{ ucfirst($credit->payment_method) }}
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            @php
                                $isActive = $credit->credits_remaining > 0 && (!$credit->expires_at || $credit->expires_at->isFuture());
                                $isExpired = $credit->expires_at && $credit->expires_at->isPast();
                            @endphp
                            <span class="credit-badge {{ $isExpired ? 'expired' : ($isActive ? 'active' : 'exhausted') }}">
                                {{ $isExpired ? 'Expired' : ($isActive ? 'Active' : 'Exhausted') }}
                            </span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            <div class="text-muted small">Purchased</div>
                            <div class="fw-bold">{{ $credit->credits_purchased }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Used</div>
                            <div class="fw-bold">{{ $credit->credits_used }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Remaining</div>
                            <div class="fw-bold text-primary">{{ $credit->credits_remaining }}</div>
                        </div>
                    </div>

                    <div class="progress-bar-credits">
                        @php
                            $percent = $credit->credits_purchased > 0
                                ? (($credit->credits_purchased - $credit->credits_remaining) / $credit->credits_purchased) * 100
                                : 0;
                        @endphp
                        <div class="fill" style="width: {{ $percent }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between mt-2" style="font-size: 0.8rem; color: #9ca3af;">
                        <span>
                            @if($credit->amount_paid > 0)
                                Paid {{ $credit->currency_code }} {{ number_format($credit->amount_paid, 2) }}
                            @else
                                Free
                            @endif
                        </span>
                        <span>
                            @if($credit->expires_at)
                                {{ $credit->expires_at->isPast() ? 'Expired' : 'Expires' }}
                                {{ $credit->expires_at->format('M d, Y') }}
                            @else
                                Never expires
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                    <h5>No credit purchases yet</h5>
                    <p class="text-muted">Purchase a package to start unlocking candidate contacts.</p>
                    <a href="{{ route('candidates.packages') }}" class="btn btn-primary">View Packages</a>
                </div>
            @endforelse

            @if($credits->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $credits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
