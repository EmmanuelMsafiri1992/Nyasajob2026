@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<div class="section-content">
						<div class="text-center mb-5">
							<h1 class="title-1">Advertise Your Products With Us</h1>
							<p class="lead">Reach thousands of job seekers and professionals with our targeted advertising packages</p>
						</div>

						@if(session('success'))
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								{!! session('success') !!}
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						@endif

						<div class="row justify-content-center">
							@forelse($packages as $package)
								<div class="col-lg-4 col-md-6 mb-4">
									<div class="card pricing-card h-100 @if($package->recommended) border-primary shadow @endif">
										@if($package->recommended)
											<div class="ribbon">
												<span class="badge bg-primary">Recommended</span>
											</div>
										@endif

										<div class="card-header text-center bg-light">
											<h3 class="h4 mb-0">{{ $package->name }}</h3>
											@if($package->short_name)
												<small class="text-muted">{{ $package->short_name }}</small>
											@endif
										</div>

										<div class="card-body">
											<div class="text-center mb-4">
												<h2 class="display-4 mb-0">
													{{ $package->currency_code }} {{ number_format($package->price, 2) }}
												</h2>
												@if($package->duration_days)
													<p class="text-muted">for {{ $package->duration_days }} days</p>
												@endif
											</div>

											<ul class="list-unstyled mb-4">
												@if($package->duration_days)
													<li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ $package->duration_days }} days campaign</li>
												@endif

												@if($package->first_position)
													<li class="mb-2"><i class="fas fa-star text-warning me-2"></i> <strong>First position display</strong></li>
												@endif

												@if($package->impressions_limit)
													<li class="mb-2"><i class="fas fa-eye text-info me-2"></i> Up to {{ number_format($package->impressions_limit) }} impressions</li>
												@else
													<li class="mb-2"><i class="fas fa-eye text-info me-2"></i> Unlimited impressions</li>
												@endif

												@if($package->clicks_limit)
													<li class="mb-2"><i class="fas fa-mouse-pointer text-primary me-2"></i> Up to {{ number_format($package->clicks_limit) }} clicks</li>
												@else
													<li class="mb-2"><i class="fas fa-mouse-pointer text-primary me-2"></i> Unlimited clicks</li>
												@endif

												<li class="mb-2"><i class="fas fa-globe text-success me-2"></i> Geographic targeting</li>

												@if($package->description)
													@foreach(explode("\n", $package->description) as $feature)
														@if(trim($feature))
															<li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ trim($feature) }}</li>
														@endif
													@endforeach
												@endif
											</ul>
										</div>

										<div class="card-footer bg-white text-center">
											<a href="{{ route('advertise.create', $package->id) }}"
											   class="btn btn-primary btn-lg w-100 @if($package->recommended) btn-lg @endif">
												Get Started
											</a>
										</div>
									</div>
								</div>
							@empty
								<div class="col-md-12">
									<div class="alert alert-info text-center">
										<h4>No advertising packages available at the moment</h4>
										<p>Please check back later or contact us for custom packages.</p>
									</div>
								</div>
							@endforelse
						</div>

						<div class="row mt-5">
							<div class="col-md-12">
								<div class="card">
									<div class="card-body">
										<h3 class="text-center mb-4">Why Advertise With Us?</h3>
										<div class="row">
											<div class="col-md-3 text-center mb-3">
												<i class="fas fa-users fa-3x text-primary mb-3"></i>
												<h5>Large Audience</h5>
												<p>Reach thousands of active job seekers daily</p>
											</div>
											<div class="col-md-3 text-center mb-3">
												<i class="fas fa-bullseye fa-3x text-primary mb-3"></i>
												<h5>Targeted Reach</h5>
												<p>Target specific countries, states, or cities</p>
											</div>
											<div class="col-md-3 text-center mb-3">
												<i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
												<h5>Track Performance</h5>
												<p>Monitor impressions and clicks in real-time</p>
											</div>
											<div class="col-md-3 text-center mb-3">
												<i class="fas fa-dollar-sign fa-3x text-primary mb-3"></i>
												<h5>Affordable Pricing</h5>
												<p>Flexible packages to fit your budget</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<style>
		.pricing-card {
			border: 2px solid #e9ecef;
			transition: all 0.3s ease;
			position: relative;
		}
		.pricing-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
		}
		.pricing-card.border-primary {
			border-width: 3px;
		}
		.ribbon {
			position: absolute;
			top: 15px;
			right: -5px;
			z-index: 1;
		}
		.ribbon .badge {
			font-size: 0.9rem;
			padding: 0.5rem 1rem;
		}
	</style>
@endsection
