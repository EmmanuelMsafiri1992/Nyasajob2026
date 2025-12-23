{{--
 * Nyasajob - Job Board Web Application
 * Subscription Checkout
--}}
@extends('layouts.master')

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row">

				@if (session()->has('flash_notification'))
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif

				@if (session('success'))
					<div class="col-xl-12">
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<i class="fas fa-check-circle me-2"></i>{{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				@endif

				@if (session('error'))
					<div class="col-xl-12">
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				@endif

				<div class="col-md-3 page-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
				</div>

				<div class="col-md-9 page-content">
					<div class="inner-box">

						{{-- Page Header --}}
						<div class="mb-4 pb-3 border-bottom">
							<div class="d-flex align-items-center justify-content-between">
								<div>
									<h2 class="title-2 mb-2">
										<i class="fas fa-shopping-cart text-primary me-2"></i>
										Complete Your Subscription
									</h2>
									<p class="text-muted mb-0">Subscribe to {{ $plan->name }} plan</p>
								</div>
								<a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
									<i class="fas fa-arrow-left me-1"></i> Back to Plans
								</a>
							</div>
						</div>

						<div class="row">
							{{-- Order Summary --}}
							<div class="col-lg-4 order-lg-2 mb-4">
								<div class="order-summary-card">
									<h5 class="card-title">
										<i class="fas fa-receipt me-2"></i>Order Summary
									</h5>

									<div class="plan-details">
										<div class="plan-badge mb-3">
											@if($plan->is_popular)
												<span class="badge bg-primary">
													<i class="fas fa-star me-1"></i> Popular Choice
												</span>
											@endif
										</div>

										<h4 class="plan-name">{{ $plan->name }}</h4>
										<p class="plan-description">{{ $plan->description }}</p>

										<div class="price-breakdown">
											<div class="price-row">
												<span>Plan Price</span>
												<span class="price-value">{{ $plan->formatted_price }}</span>
											</div>
											<div class="price-row">
												<span>Billing Cycle</span>
												<span class="price-value">{{ ucfirst($plan->interval_label) }}ly</span>
											</div>
											<hr>
											<div class="price-row total">
												<strong>Total Due Today</strong>
												<strong class="price-value text-primary">{{ $plan->formatted_price }}</strong>
											</div>
										</div>

										<div class="features-included mt-4">
											<h6 class="mb-3"><i class="fas fa-check-circle text-success me-1"></i> What's Included:</h6>
											<ul class="feature-list">
												@if($plan->features)
													@foreach($plan->features as $feature)
														<li>
															<i class="fas fa-check text-success me-2"></i>
															{{ ucwords(str_replace('_', ' ', $feature)) }}
														</li>
													@endforeach
												@endif
											</ul>
										</div>
									</div>
								</div>
							</div>

							{{-- Payment Form --}}
							<div class="col-lg-8 order-lg-1">
								<form method="POST" action="{{ route('subscriptions.subscribe') }}" id="checkoutForm">
									@csrf
									<input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">

									{{-- Payment Method --}}
									<div class="payment-section">
										<h5 class="section-title">
											<i class="fab fa-paypal me-2"></i>Payment Method
										</h5>

										<div class="alert alert-info border-0 mb-4">
											<div class="d-flex align-items-center">
												<div class="me-3">
													<i class="fab fa-paypal fa-3x text-primary"></i>
												</div>
												<div>
													<h6 class="mb-1"><strong>PayPal Payment</strong></h6>
													<p class="mb-0 small">You will be redirected to PayPal to complete your secure payment.</p>
												</div>
											</div>
										</div>

										<div class="payment-methods">
											<div class="payment-option">
												<input type="radio" name="payment_method" id="paypal" value="paypal" checked>
												<label for="paypal" class="payment-label">
													<div class="payment-icon">
														<i class="fab fa-paypal"></i>
													</div>
													<div class="payment-info">
														<strong>Pay with PayPal</strong>
														<small>Safe and secure payment via PayPal</small>
													</div>
													<div class="payment-logos">
														<i class="fab fa-cc-paypal text-primary fa-2x"></i>
													</div>
												</label>
											</div>
										</div>

										@error('payment_method')
											<div class="text-danger mt-2">
												<i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
											</div>
										@enderror

										<div class="mt-4 p-3 bg-light rounded">
											<h6 class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>Secure Payment</h6>
											<ul class="list-unstyled mb-0 small text-muted">
												<li><i class="fas fa-check text-success me-2"></i>256-bit SSL encryption</li>
												<li><i class="fas fa-check text-success me-2"></i>PCI DSS compliant</li>
												<li><i class="fas fa-check text-success me-2"></i>Buyer protection included</li>
												<li><i class="fas fa-check text-success me-2"></i>No card details stored</li>
											</ul>
										</div>
									</div>

									{{-- Terms and Conditions --}}
									<div class="payment-section">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="terms" required>
											<label class="form-check-label" for="terms">
												I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
											</label>
										</div>
									</div>

									{{-- Submit Button --}}
									<div class="payment-section">
										<button type="submit" class="btn btn-primary btn-lg w-100">
											<i class="fas fa-lock me-2"></i>
											Complete Subscription - {{ $plan->formatted_price }}
										</button>
										<p class="text-center text-muted mt-3 mb-0">
											<i class="fas fa-shield-alt me-1"></i>
											<small>Your payment information is secure and encrypted</small>
										</p>
									</div>

								</form>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
	</div>
@endsection

@section('after_styles')
<style>
/* Page Title */
.title-2 {
	font-size: 1.5rem;
	font-weight: 700;
	color: #2c3e50;
}

/* Order Summary Card */
.order-summary-card {
	background: #f8f9fa;
	border: 2px solid #e9ecef;
	border-radius: 12px;
	padding: 1.5rem;
	position: sticky;
	top: 20px;
}

.order-summary-card .card-title {
	font-size: 1.1rem;
	font-weight: 700;
	color: #2c3e50;
	margin-bottom: 1.5rem;
	padding-bottom: 1rem;
	border-bottom: 2px solid #dee2e6;
}

.plan-details .plan-name {
	font-size: 1.5rem;
	font-weight: 700;
	color: #186dde;
	margin-bottom: 0.5rem;
}

.plan-details .plan-description {
	font-size: 0.875rem;
	color: #6c757d;
	margin-bottom: 1.5rem;
}

.price-breakdown {
	background: #fff;
	padding: 1rem;
	border-radius: 8px;
	margin-bottom: 1rem;
}

.price-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 0.5rem 0;
	font-size: 0.875rem;
}

.price-row.total {
	font-size: 1rem;
	padding-top: 1rem;
}

.price-value {
	font-weight: 600;
	color: #2c3e50;
}

.features-included h6 {
	font-size: 0.95rem;
	font-weight: 600;
	color: #2c3e50;
}

.feature-list {
	list-style: none;
	padding: 0;
	margin: 0;
}

.feature-list li {
	padding: 0.4rem 0;
	font-size: 0.85rem;
	color: #2c3e50;
}

/* Payment Sections */
.payment-section {
	background: #fff;
	border: 2px solid #e9ecef;
	border-radius: 12px;
	padding: 1.5rem;
	margin-bottom: 1.5rem;
}

.section-title {
	font-size: 1.1rem;
	font-weight: 700;
	color: #2c3e50;
	margin-bottom: 1.5rem;
}

/* Payment Methods */
.payment-methods {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}

.payment-option {
	position: relative;
}

.payment-option input[type="radio"] {
	position: absolute;
	opacity: 0;
	pointer-events: none;
}

.payment-label {
	display: flex;
	align-items: center;
	gap: 1rem;
	padding: 1rem;
	background: #f8f9fa;
	border: 2px solid #e9ecef;
	border-radius: 8px;
	cursor: pointer;
	transition: all 0.3s ease;
	margin: 0;
}

.payment-label:hover {
	background: #fff;
	border-color: #186dde;
	box-shadow: 0 2px 8px rgba(24, 109, 222, 0.1);
}

.payment-option input[type="radio"]:checked + .payment-label {
	background: #fff;
	border-color: #186dde;
	box-shadow: 0 2px 12px rgba(24, 109, 222, 0.15);
}

.payment-icon {
	width: 40px;
	height: 40px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(24, 109, 222, 0.1);
	border-radius: 8px;
	font-size: 1.25rem;
	color: #186dde;
	flex-shrink: 0;
}

.payment-info {
	flex: 1;
}

.payment-info strong {
	display: block;
	font-size: 0.95rem;
	color: #2c3e50;
	margin-bottom: 0.25rem;
}

.payment-info small {
	display: block;
	font-size: 0.8rem;
	color: #6c757d;
}

.payment-logos {
	display: flex;
	gap: 0.5rem;
	font-size: 1.5rem;
	color: #6c757d;
}

/* Form Controls */
.form-label {
	font-weight: 600;
	color: #2c3e50;
	margin-bottom: 0.5rem;
	font-size: 0.875rem;
}

.form-control {
	border: 2px solid #e9ecef;
	border-radius: 8px;
	padding: 0.65rem 1rem;
	font-size: 0.875rem;
}

.form-control:focus {
	border-color: #186dde;
	box-shadow: 0 0 0 0.2rem rgba(24, 109, 222, 0.1);
}

.form-check-input {
	width: 1.25rem;
	height: 1.25rem;
	margin-top: 0.125rem;
	cursor: pointer;
}

.form-check-label {
	font-size: 0.875rem;
	color: #2c3e50;
	cursor: pointer;
	margin-left: 0.5rem;
}

/* Responsive */
@media (max-width: 991px) {
	.order-summary-card {
		position: relative;
		top: 0;
		margin-bottom: 2rem;
	}
}

@media (max-width: 575px) {
	.payment-label {
		flex-direction: column;
		text-align: center;
	}

	.payment-info {
		text-align: center;
	}

	.payment-logos {
		justify-content: center;
	}
}
</style>
@endsection

@section('after_scripts')
<script>
	$(document).ready(function() {
		// Form validation
		$('#checkoutForm').on('submit', function(e) {
			if (!$('#terms').is(':checked')) {
				e.preventDefault();
				alert('Please accept the Terms of Service and Privacy Policy to continue.');
				return false;
			}
		});
	});
</script>
@endsection
