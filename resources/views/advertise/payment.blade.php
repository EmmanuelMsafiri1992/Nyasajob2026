@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<h1 class="title-1 mb-4 text-center">Complete Your Payment</h1>

					@if(session('error'))
						<div class="alert alert-danger alert-dismissible fade show">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							{{ session('error') }}
						</div>
					@endif

					<div class="card mb-4">
						<div class="card-header bg-light">
							<h5 class="mb-0">Order Summary</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<h6>Advertisement</h6>
									<p class="mb-0"><strong>{{ $ad->title }}</strong></p>
									@if($ad->description)
										<p class="text-muted small">{{ Str::limit($ad->description, 100) }}</p>
									@endif
								</div>
								<div class="col-md-6">
									<h6>Package</h6>
									<p class="mb-0"><strong>{{ $package->name }}</strong></p>
									<ul class="list-unstyled small text-muted">
										@if($package->duration_days)
											<li>Duration: {{ $package->duration_days }} days</li>
										@endif
										@if($package->first_position)
											<li>First position display</li>
										@endif
									</ul>
								</div>
							</div>

							<hr>

							<div class="d-flex justify-content-between align-items-center">
								<h4 class="mb-0">Total Amount:</h4>
								<h3 class="mb-0 text-primary">{{ $package->currency_code }} {{ number_format($package->price, 2) }}</h3>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-header bg-primary text-white">
							<h5 class="mb-0">
								<i class="fab fa-paypal"></i> PayPal Payment
							</h5>
						</div>
						<div class="card-body text-center">
							@if($paymentMethod)
								<p class="lead">Click the button below to proceed with PayPal payment</p>

								<div class="alert alert-info">
									<i class="fas fa-info-circle"></i> You will be redirected to PayPal to complete your payment securely.
								</div>

								{{-- Simple form for now - will be replaced with actual PayPal integration --}}
								<form action="{{ route('advertise.payment.callback') }}" method="POST">
									@csrf
									<input type="hidden" name="ad_id" value="{{ $ad->id }}">
									<input type="hidden" name="package_id" value="{{ $package->id }}">
									<input type="hidden" name="amount" value="{{ $package->price }}">
									<input type="hidden" name="currency" value="{{ $package->currency_code }}">

									<button type="submit" class="btn btn-primary btn-lg">
										<i class="fab fa-paypal"></i> Pay with PayPal
									</button>
								</form>

								<p class="text-muted mt-3 small">
									<i class="fas fa-lock"></i> Secure payment powered by PayPal
								</p>

								<div class="alert alert-warning mt-4">
									<strong>Note:</strong> Your advertisement will be reviewed by our admin team before going live.
									You will receive an email notification once approved.
								</div>
							@else
								<div class="alert alert-danger">
									<h5>Payment Method Not Available</h5>
									<p>PayPal payment is currently not configured. Please contact support.</p>
								</div>
							@endif

							<hr>

							<a href="{{ route('advertise.index') }}" class="btn btn-outline-secondary">
								<i class="fas fa-arrow-left"></i> Back to Packages
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
