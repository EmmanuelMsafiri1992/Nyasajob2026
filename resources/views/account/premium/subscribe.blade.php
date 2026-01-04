@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 page-sidebar">
                    @include('account.inc.sidebar')
                </div>

                <div class="col-md-9 page-content">
                    @include('flash::message')

                    <div class="inner-box">
                        <h2 class="title-2">
                            <strong><i class="fa-solid fa-crown"></i> Subscribe to Premium</strong>
                        </h2>

                        <div class="row">
                            <div class="col-lg-8">
                                {{-- Plan Details --}}
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h4 class="mb-0">Job Seeker Premium</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-4">
                                            <span class="display-4 fw-bold">${{ number_format($price, 2) }}</span>
                                            <span class="text-muted">for 30 days</span>
                                        </div>

                                        <ul class="list-unstyled">
                                            <li class="mb-3">
                                                <i class="fa-solid fa-check text-success me-2"></i>
                                                <strong>Exact Job Matching</strong> - Find jobs that match your exact preferences
                                            </li>
                                            <li class="mb-3">
                                                <i class="fa-solid fa-check text-success me-2"></i>
                                                <strong>CV Refinement Tips</strong> - Get personalized advice to improve your CV
                                            </li>
                                            <li class="mb-3">
                                                <i class="fa-solid fa-check text-success me-2"></i>
                                                <strong>Interview Preparation</strong> - Tips based on job type and company
                                            </li>
                                            <li class="mb-3">
                                                <i class="fa-solid fa-check text-success me-2"></i>
                                                <strong>Email Alerts</strong> - Instant notifications for matching jobs
                                            </li>
                                            <li class="mb-3">
                                                <i class="fa-solid fa-check text-success me-2"></i>
                                                <strong>Priority Support</strong> - Get help when you need it
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Terms and Conditions --}}
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Terms and Conditions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="terms-content" style="max-height: 300px; overflow-y: auto; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                                            <h6>NyasaJob Premium Subscription Terms</h6>
                                            <p><strong>Last Updated: {{ now()->format('F d, Y') }}</strong></p>

                                            <p>By subscribing to NyasaJob Premium, you agree to the following terms:</p>

                                            <h6>1. Subscription Fee</h6>
                                            <p>The subscription fee is $5.00 USD for 30 days access, paid via PayPal. This is a one-time payment - your access will expire after 30 days and you can renew manually if desired.</p>

                                            <h6>2. Non-Refundable Policy</h6>
                                            <p><strong>IMPORTANT: All subscription fees are NON-REFUNDABLE.</strong> Once payment is processed, no refunds will be issued under any circumstances. This includes:</p>
                                            <ul>
                                                <li>Partial month cancellations</li>
                                                <li>Unused features</li>
                                                <li>Change of mind</li>
                                                <li>Account closures</li>
                                            </ul>

                                            <h6>3. Cancellation</h6>
                                            <p>You may cancel your subscription at any time. Upon cancellation:</p>
                                            <ul>
                                                <li>You will retain access to premium features until the end of your current billing period</li>
                                                <li>No partial refunds will be provided</li>
                                                <li>Your preferences and data will be retained for 30 days</li>
                                            </ul>

                                            <h6>4. Premium Features</h6>
                                            <p>Premium features include job matching based on your preferences, CV refinement tips, and interview preparation guidance. We do not guarantee employment outcomes.</p>

                                            <h6>5. Data Usage</h6>
                                            <p>Your job preferences and profile information will be used solely to provide personalized job recommendations and career guidance.</p>

                                            <h6>6. Service Availability</h6>
                                            <p>While we strive for 100% uptime, we do not guarantee uninterrupted access to premium features.</p>

                                            <h6>7. Modifications</h6>
                                            <p>We reserve the right to modify these terms or the premium features at any time. Continued use after changes constitutes acceptance.</p>

                                            <h6>8. Governing Law</h6>
                                            <p>These terms are governed by the laws of the jurisdiction where NyasaJob operates.</p>

                                            <p class="mt-3"><em>By proceeding with payment, you acknowledge that you have read, understood, and agree to these terms.</em></p>
                                        </div>

                                        <div class="form-check mt-3">
                                            <input type="checkbox" class="form-check-input" id="termsAccepted" required>
                                            <label class="form-check-label" for="termsAccepted">
                                                <strong>I have read and agree to the Terms and Conditions, including the NON-REFUNDABLE policy</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Button --}}
                                <div class="d-grid gap-2">
                                    <button type="button" id="subscribeBtn" class="btn btn-primary btn-lg" disabled>
                                        <i class="fa-brands fa-paypal me-2"></i> Pay with PayPal - ${{ number_format($price, 2) }}/month
                                    </button>
                                    <a href="{{ route('account.premium.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>

                                <p class="text-muted text-center mt-3">
                                    <i class="fa-solid fa-lock me-1"></i>
                                    Secure payment processed by PayPal
                                </p>
                            </div>

                            <div class="col-lg-4">
                                {{-- Order Summary --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Order Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Job Seeker Premium</span>
                                            <span>${{ number_format($price, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Billing Cycle</span>
                                            <span>Monthly</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total (billed today)</span>
                                            <span>${{ number_format($price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Your subscription will renew automatically each month. You can cancel anytime from your account settings.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const termsCheckbox = document.getElementById('termsAccepted');
    const subscribeBtn = document.getElementById('subscribeBtn');

    termsCheckbox.addEventListener('change', function() {
        subscribeBtn.disabled = !this.checked;
    });

    subscribeBtn.addEventListener('click', function() {
        if (!termsCheckbox.checked) {
            alert('Please accept the terms and conditions to continue.');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processing...';

        fetch('{{ route("account.premium.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                terms_accepted: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'An error occurred. Please try again.');
                this.disabled = false;
                this.innerHTML = '<i class="fa-brands fa-paypal me-2"></i> Pay with PayPal - ${{ number_format($price, 2) }}/month';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="fa-brands fa-paypal me-2"></i> Pay with PayPal - ${{ number_format($price, 2) }}/month';
        });
    });
});
</script>
@endsection
