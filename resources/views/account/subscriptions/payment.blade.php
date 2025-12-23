{{--
 * Nyasajob - Job Board Web Application
 * Subscription Payment
--}}
@extends('layouts.master')

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">

					@if (session()->has('flash_notification'))
						<div class="col-xl-12 mb-3">
							@include('flash::message')
						</div>
					@endif

					@if (session('error'))
						<div class="alert alert-danger alert-dismissible fade show mb-3">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							<i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
						</div>
					@endif

					<h1 class="title-1 mb-4 text-center">
						<i class="fas fa-lock me-2"></i>{{ t('Complete Your Payment') }}
					</h1>

					{{-- Order Summary Card --}}
					<div class="card mb-4">
						<div class="card-header bg-light">
							<h5 class="mb-0">
								<i class="fas fa-receipt me-2"></i>{{ t('Order Summary') }}
							</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-8">
									<h6>{{ t('Subscription Plan') }}</h6>
									<p class="mb-0">
										<strong>{{ $plan->name }}</strong>
										@if($plan->is_popular)
											<span class="badge bg-primary ms-2">
												<i class="fas fa-star me-1"></i>{{ t('Popular') }}
											</span>
										@endif
									</p>
									@if($plan->description)
										<p class="text-muted small mt-2">{{ $plan->description }}</p>
									@endif

									@if($plan->features)
										<div class="mt-3">
											<h6 class="small"><i class="fas fa-check-circle text-success me-1"></i>{{ t('Included Features') }}:</h6>
											<ul class="list-unstyled small text-muted">
												@foreach($plan->features as $feature)
													<li><i class="fas fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $feature)) }}</li>
												@endforeach
											</ul>
										</div>
									@endif
								</div>
								<div class="col-md-4">
									<h6>{{ t('Billing Cycle') }}</h6>
									<p class="mb-0">{{ ucfirst($plan->interval_label) }}ly</p>

									@if($plan->interval_count > 1)
										<p class="text-muted small">{{ $plan->interval_count }} {{ $plan->interval }}(s)</p>
									@endif
								</div>
							</div>

							<hr>

							<div class="d-flex justify-content-between align-items-center">
								<h4 class="mb-0">{{ t('Total Amount') }}:</h4>
								<h3 class="mb-0 text-primary">{{ $plan->formatted_price }}</h3>
							</div>
						</div>
					</div>

					{{-- PayPal Payment Card --}}
					<div class="card">
						<div class="card-header bg-primary text-white">
							<h5 class="mb-0">
								<i class="fab fa-paypal me-2"></i>{{ t('PayPal Payment') }}
							</h5>
						</div>
						<div class="card-body text-center">
							@if($paymentMethod)
								<p class="lead">{{ t('Click the button below to proceed with PayPal payment') }}</p>

								<div class="alert alert-info border-0">
									<i class="fas fa-info-circle me-2"></i>{{ t('You will be redirected to PayPal to complete your payment securely') }}.
								</div>

								<form action="{{ route('subscriptions.payment.success', $plan->id) }}" method="GET" id="paymentForm">
									<input type="hidden" name="transaction_id" value="TXN-{{ strtoupper(uniqid()) }}">

									<button type="submit" class="btn btn-primary btn-lg">
										<i class="fab fa-paypal me-2"></i>{{ t('Pay with PayPal') }}
									</button>
								</form>

								<p class="text-muted mt-3 small">
									<i class="fas fa-lock me-1"></i>{{ t('Secure payment powered by PayPal') }}
								</p>

								<div class="alert alert-success mt-4 border-0">
									<strong><i class="fas fa-check-circle me-2"></i>{{ t('Instant Activation') }}</strong>
									<p class="mb-0 small">{{ t('Your subscription will be activated immediately after payment confirmation') }}.</p>
								</div>

								<div class="mt-3 p-3 bg-light rounded">
									<h6 class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>{{ t('Secure Payment') }}</h6>
									<ul class="list-unstyled mb-0 small text-muted">
										<li><i class="fas fa-check text-success me-2"></i>{{ t('256-bit SSL encryption') }}</li>
										<li><i class="fas fa-check text-success me-2"></i>{{ t('PCI DSS compliant') }}</li>
										<li><i class="fas fa-check text-success me-2"></i>{{ t('Buyer protection included') }}</li>
										<li><i class="fas fa-check text-success me-2"></i>{{ t('No card details stored') }}</li>
									</ul>
								</div>
							@else
								<div class="alert alert-danger">
									<h5>{{ t('Payment Method Not Available') }}</h5>
									<p>{{ t('PayPal payment is currently not configured. Please contact support') }}.</p>
								</div>
							@endif

							<hr>

							<a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
								<i class="fas fa-arrow-left me-1"></i>{{ t('Back to Plans') }}
							</a>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_styles')
<style>
.title-1 {
	font-size: 1.75rem;
	font-weight: 700;
	color: #2c3e50;
}

.card {
	border: 2px solid #e9ecef;
	border-radius: 12px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.card-header {
	border-bottom: 2px solid #e9ecef;
	padding: 1rem 1.25rem;
}

.card-header h5 {
	font-size: 1.1rem;
	font-weight: 600;
}

.card-body {
	padding: 1.5rem;
}

.card-body h6 {
	font-size: 0.9rem;
	font-weight: 600;
	color: #6c757d;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 0.5rem;
}

.btn-lg {
	padding: 0.75rem 2rem;
	font-size: 1.1rem;
	font-weight: 600;
}

.alert {
	border-radius: 8px;
}

.badge {
	font-size: 0.75rem;
	font-weight: 600;
	padding: 0.35rem 0.65rem;
}
</style>
@endsection
