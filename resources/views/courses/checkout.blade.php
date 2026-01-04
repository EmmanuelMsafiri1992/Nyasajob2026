@extends('layouts.master')

@section('content')
<style>
    .checkout-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .checkout-header {
        margin-bottom: 2rem;
    }
    .checkout-header h1 { font-size: 1.75rem; font-weight: 700; color: #1f2937; }

    .checkout-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .course-summary {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .course-thumbnail {
        width: 120px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    }

    .course-title { font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem; }
    .course-instructor { color: #6b7280; font-size: 0.9rem; }

    .checkout-form {
        padding: 1.5rem;
    }

    .price-breakdown {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        color: #4b5563;
    }
    .price-row.total {
        border-top: 2px solid #e5e7eb;
        margin-top: 0.5rem;
        padding-top: 1rem;
        font-weight: 700;
        font-size: 1.25rem;
        color: #1f2937;
    }
    .price-row .discount { color: #10b981; }

    .coupon-section {
        margin-bottom: 1.5rem;
    }

    .pay-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .pay-btn:hover { opacity: 0.9; }
    .pay-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1rem;
        color: #6b7280;
        font-size: 0.85rem;
    }
    .secure-badge i { color: #10b981; }

    .course-includes {
        padding: 1.5rem;
        background: #f9fafb;
    }
    .course-includes h6 { font-weight: 600; margin-bottom: 1rem; }
    .course-includes li {
        padding: 0.35rem 0;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .course-includes li i { color: #10b981; }

    .payment-methods {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 1rem;
    }
    .payment-methods img { height: 24px; opacity: 0.7; }
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left me-1"></i> Back to course
        </a>
        <h1 class="mt-2">Checkout</h1>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="checkout-card">
                <div class="course-summary d-flex gap-3">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="" class="course-thumbnail">
                    @else
                        <div class="course-thumbnail d-flex align-items-center justify-content-center">
                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="course-title">{{ $course->title }}</h3>
                        <p class="course-instructor mb-0">
                            @if($course->instructor)
                                By {{ $course->instructor->name ?? 'Instructor' }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="checkout-form">
                    <form id="checkoutForm">
                        @csrf

                        <div class="price-breakdown" id="priceBreakdown">
                            <div class="price-row">
                                <span>Course Price</span>
                                <span id="originalPrice">{{ $course->currency_code ?? 'USD' }} {{ number_format($course->price, 2) }}</span>
                            </div>
                            <div class="price-row" id="discountRow" style="display: none;">
                                <span>Discount</span>
                                <span class="discount" id="discountAmount">- $0.00</span>
                            </div>
                            <div class="price-row total">
                                <span>Total</span>
                                <span id="totalPrice">{{ $course->currency_code ?? 'USD' }} {{ number_format($course->price, 2) }}</span>
                            </div>
                        </div>

                        <div class="coupon-section">
                            <label class="form-label">Have a coupon?</label>
                            <div class="input-group">
                                <input type="text" name="coupon_code" id="couponCode" class="form-control" placeholder="Enter coupon code">
                                <button type="button" class="btn btn-outline-primary" id="applyCouponBtn">Apply</button>
                            </div>
                            <div id="couponMessage" class="mt-2" style="font-size: 0.85rem;"></div>
                        </div>

                        <button type="submit" class="pay-btn" id="payBtn">
                            <i class="fas fa-lock me-2"></i>
                            Complete Purchase
                        </button>

                        <div class="secure-badge">
                            <i class="fas fa-shield-alt"></i>
                            Secure checkout powered by PayPal
                        </div>

                        <div class="payment-methods">
                            <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" alt="PayPal">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="checkout-card">
                <div class="course-includes">
                    <h6>This course includes:</h6>
                    <ul class="list-unstyled mb-0">
                        @if($course->lessons_count > 0)
                            <li><i class="fas fa-play-circle"></i> {{ $course->lessons_count }} lessons</li>
                        @endif
                        @if($course->duration)
                            <li><i class="fas fa-clock"></i> {{ $course->duration }} total hours</li>
                        @endif
                        <li><i class="fas fa-infinity"></i> Full lifetime access</li>
                        <li><i class="fas fa-mobile-alt"></i> Access on mobile and desktop</li>
                        <li><i class="fas fa-certificate"></i> Certificate of completion</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-shield-alt text-primary me-2"></i>30-Day Money-Back Guarantee</h6>
                    <p class="text-muted small mb-0">
                        If you're not satisfied with the course, you can request a full refund within 30 days of purchase.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const coursePrice = {{ $course->price }};
const currencyCode = '{{ $course->currency_code ?? "USD" }}';
let appliedDiscount = 0;
let appliedCoupon = null;

document.getElementById('applyCouponBtn').addEventListener('click', function() {
    const couponCode = document.getElementById('couponCode').value.trim();
    const messageDiv = document.getElementById('couponMessage');

    if (!couponCode) {
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
            code: couponCode,
            amount: coursePrice,
            type: 'courses'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            appliedDiscount = data.discount_amount;
            appliedCoupon = couponCode;
            updatePriceDisplay(data);
            messageDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>' + data.message + '</span>';
        } else {
            appliedDiscount = 0;
            appliedCoupon = null;
            resetPriceDisplay();
            messageDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>' + (data.message || 'Invalid coupon') + '</span>';
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

function updatePriceDisplay(data) {
    document.getElementById('discountRow').style.display = 'flex';
    document.getElementById('discountAmount').textContent = '- ' + currencyCode + ' ' + data.discount_amount.toFixed(2);
    document.getElementById('totalPrice').textContent = currencyCode + ' ' + data.final_amount.toFixed(2);
}

function resetPriceDisplay() {
    document.getElementById('discountRow').style.display = 'none';
    document.getElementById('totalPrice').textContent = currencyCode + ' ' + coursePrice.toFixed(2);
}

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

    fetch('{{ route("courses.pay", $course->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            coupon_code: appliedCoupon
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock me-2"></i>Complete Purchase';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock me-2"></i>Complete Purchase';
    });
});
</script>
@endsection
