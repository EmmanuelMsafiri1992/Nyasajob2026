@extends('layouts.master')

@section('content')
<style>
    .packages-hero {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }
    .packages-hero h1 { font-size: 2.25rem; margin-bottom: 0.5rem; }
    .packages-hero p { opacity: 0.9; max-width: 600px; margin: 0 auto; }

    .package-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .package-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .package-card.featured {
        border: 2px solid #4f46e5;
    }
    .package-card.featured::before {
        content: 'Most Popular';
        position: absolute;
        top: 12px;
        right: -32px;
        background: #4f46e5;
        color: white;
        padding: 0.25rem 2.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        transform: rotate(45deg);
    }

    .package-name { font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
    .package-credits {
        font-size: 3rem;
        font-weight: 800;
        color: #4f46e5;
        line-height: 1;
    }
    .package-credits span { font-size: 1rem; font-weight: 400; color: #6b7280; }

    .package-price {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin: 1rem 0;
    }
    .package-price .currency { font-size: 1rem; vertical-align: super; }
    .package-price .period { font-size: 0.9rem; font-weight: 400; color: #6b7280; }

    .package-features {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
        text-align: left;
    }
    .package-features li {
        padding: 0.5rem 0;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .package-features li i { color: #10b981; }

    .package-btn {
        width: 100%;
        padding: 0.875rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    .package-btn-primary {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
    }
    .package-btn-primary:hover { opacity: 0.9; }
    .package-btn-outline {
        background: transparent;
        border: 2px solid #4f46e5;
        color: #4f46e5;
    }
    .package-btn-outline:hover { background: #4f46e5; color: white; }

    .per-credit { font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem; }

    .current-credits {
        background: #fef3c7;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 2rem;
    }
    .current-credits h3 { color: #92400e; margin-bottom: 0.25rem; }
    .current-credits .value { font-size: 2.5rem; font-weight: 800; color: #92400e; }

    .coupon-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }
</style>

<div class="packages-hero">
    <h1>Resume Access Packages</h1>
    <p>Purchase credits to unlock candidate contact details. Each credit unlocks one candidate's full profile.</p>
</div>

<div class="container">
    @if($userCredits > 0)
        <div class="current-credits">
            <h3>Your Current Balance</h3>
            <div class="value">{{ $userCredits }} Credits</div>
            <a href="{{ route('candidates.index') }}" class="btn btn-warning mt-2">Browse Candidates</a>
        </div>
    @endif

    <div class="row justify-content-center">
        @forelse($packages as $package)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="package-card {{ $package->is_featured ? 'featured' : '' }}">
                    <div class="package-name">{{ $package->name }}</div>
                    <div class="package-credits">
                        {{ $package->credits }}
                        <span>credits</span>
                    </div>
                    <div class="package-price">
                        <span class="currency">{{ $package->currency_code }}</span>
                        {{ number_format($package->price, 2) }}
                    </div>
                    <div class="per-credit">
                        {{ $package->currency_code }} {{ number_format($package->price_per_credit, 2) }} per credit
                    </div>

                    <ul class="package-features">
                        <li><i class="fas fa-check-circle"></i> {{ $package->credits }} candidate contact unlocks</li>
                        <li><i class="fas fa-check-circle"></i> Valid for {{ $package->validity_days }} days</li>
                        @if($package->unlimited_search)
                            <li><i class="fas fa-check-circle"></i> Unlimited candidate search</li>
                        @endif
                        @if($package->export_allowed)
                            <li><i class="fas fa-check-circle"></i> Export contacts to CSV</li>
                        @endif
                    </ul>

                    @if($package->description)
                        <p class="text-muted mb-3" style="font-size: 0.9rem;">{{ $package->description }}</p>
                    @endif

                    @auth
                        <form action="{{ route('candidates.purchase') }}" method="POST" class="purchase-form">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">

                            <div class="mb-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="coupon_code" class="form-control" placeholder="Coupon code">
                                    <button type="button" class="btn btn-outline-secondary apply-coupon-btn">Apply</button>
                                </div>
                                <div class="coupon-message mt-1" style="font-size: 0.8rem;"></div>
                            </div>

                            <button type="submit" class="package-btn {{ $package->is_featured ? 'package-btn-primary' : 'package-btn-outline' }}">
                                @if($package->price <= 0)
                                    Get Free Credits
                                @else
                                    Purchase Now
                                @endif
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="package-btn {{ $package->is_featured ? 'package-btn-primary' : 'package-btn-outline' }}">
                            Login to Purchase
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>No packages available</h4>
                <p class="text-muted">Please check back later for available packages.</p>
            </div>
        @endforelse
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shield-alt me-2 text-primary"></i>Why Choose Our Packages?</h5>
                    <div class="row mt-3">
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h6>Verified Candidates</h6>
                            <p class="text-muted small">All profiles are verified and active.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-lock fa-2x text-primary mb-2"></i>
                            <h6>Secure Payments</h6>
                            <p class="text-muted small">PayPal secure payment processing.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                            <h6>24/7 Support</h6>
                            <p class="text-muted small">We're here to help anytime.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@auth
<script>
document.querySelectorAll('.apply-coupon-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('form');
        const couponInput = form.querySelector('input[name="coupon_code"]');
        const packageId = form.querySelector('input[name="package_id"]').value;
        const messageDiv = form.querySelector('.coupon-message');

        if (!couponInput.value.trim()) {
            messageDiv.innerHTML = '<span class="text-danger">Please enter a coupon code</span>';
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('{{ url("api/coupon/validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                code: couponInput.value,
                amount: parseFloat(form.closest('.package-card').querySelector('.package-price').textContent.replace(/[^0-9.]/g, '')),
                type: 'resume_packages'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                messageDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
            } else {
                messageDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + (data.message || 'Invalid coupon') + '</span>';
            }
        })
        .catch(error => {
            messageDiv.innerHTML = '<span class="text-danger">Error validating coupon</span>';
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = 'Apply';
        });
    });
});
</script>
@endauth
@endsection
