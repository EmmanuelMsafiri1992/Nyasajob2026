{{--
 * Nyasajob - Job Board Web Application
 * Subscription Management
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
						<div class="mb-4">
							<h2 class="title-2">
								<i class="fas fa-crown text-warning me-2"></i>
								Subscription Plans
							</h2>
							<p class="text-muted mb-0">Choose the plan that fits your needs</p>
						</div>

						{{-- Current Subscription Status --}}
						@if($currentSubscription)
							<div class="alert alert-info border-0 shadow-sm mb-4">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h6 class="mb-1">
											<i class="fas fa-check-circle me-1"></i>
											<strong>Current Plan:</strong> {{ $currentSubscription->plan->name }}
										</h6>
										<small>
											Expires on: {{ $currentSubscription->ends_at->format('M d, Y') }}
											@if($currentSubscription->ends_at->isPast())
												<span class="badge bg-danger ms-2">Expired</span>
											@elseif($currentSubscription->ends_at->diffInDays() <= 7)
												<span class="badge bg-warning ms-2">Expiring Soon</span>
											@endif
										</small>
									</div>
									<form action="{{ route('subscriptions.cancel') }}" method="POST"
										onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
										@csrf
										<button type="submit" class="btn btn-sm btn-outline-danger">
											<i class="fas fa-times me-1"></i> Cancel Subscription
										</button>
									</form>
								</div>
							</div>
						@endif

						{{-- Pricing Cards --}}
						<div class="row g-3 justify-content-center">
							@foreach($plans as $plan)
								<div class="col-sm-6 col-lg-6 col-xl-3">
									<div class="pricing-card {{ $plan->is_popular ? 'popular' : '' }}">
										@if($plan->is_popular)
											<div class="popular-badge">
												<i class="fas fa-star me-1"></i> Popular
											</div>
										@endif

										<div class="plan-header">
											<h4 class="plan-name">{{ $plan->name }}</h4>
											<div class="plan-price">
												<span class="currency">$</span>
												<span class="amount">{{ number_format($plan->price, 0) }}</span>
												<span class="period">/{{ $plan->interval_label }}</span>
											</div>
											<p class="plan-description">{{ $plan->description }}</p>
										</div>

										<div class="plan-features">
											<ul>
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

										<div class="plan-action">
											@if($currentSubscription && $currentSubscription->plan->id == $plan->id)
												<button class="btn btn-secondary w-100" disabled>
													<i class="fas fa-check me-1"></i> Current
												</button>
											@elseif($plan->price == 0)
												<button class="btn btn-outline-primary w-100" disabled>
													Free Plan
												</button>
											@else
												<a href="{{ route('subscriptions.checkout', $plan->slug) }}"
													class="btn btn-primary w-100">
													<i class="fas fa-crown me-1"></i> Subscribe
												</a>
											@endif
										</div>
									</div>
								</div>
							@endforeach
						</div>

						{{-- Features Comparison --}}
						<div class="mt-5">
							<h4 class="mb-3"><i class="fas fa-list-check me-2"></i>Feature Comparison</h4>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>Feature</th>
											@foreach($plans as $plan)
												<th class="text-center">{{ $plan->name }}</th>
											@endforeach
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Browse Jobs</td>
											@foreach($plans as $plan)
												<td class="text-center">
													<i class="fas fa-check text-success"></i>
												</td>
											@endforeach
										</tr>
										<tr>
											<td>Manual Applications</td>
											@foreach($plans as $plan)
												<td class="text-center">
													<i class="fas fa-check text-success"></i>
												</td>
											@endforeach
										</tr>
										<tr>
											<td>Job Preferences & Matching</td>
											@foreach($plans as $plan)
												<td class="text-center">
													@if(in_array('job_preferences', $plan->features ?? []) || in_array('job_matches', $plan->features ?? []))
														<i class="fas fa-check text-success"></i>
													@else
														<i class="fas fa-times text-danger"></i>
													@endif
												</td>
											@endforeach
										</tr>
										<tr>
											<td>CV Builder</td>
											@foreach($plans as $plan)
												<td class="text-center">
													@if(in_array('cv_builder', $plan->features ?? []))
														<i class="fas fa-check text-success"></i>
													@else
														<i class="fas fa-times text-danger"></i>
													@endif
												</td>
											@endforeach
										</tr>
										<tr>
											<td>Auto-Apply</td>
											@foreach($plans as $plan)
												<td class="text-center">
													@if(in_array('auto_apply', $plan->features ?? []))
														<i class="fas fa-check text-success"></i>
													@else
														<i class="fas fa-times text-danger"></i>
													@endif
												</td>
											@endforeach
										</tr>
										<tr>
											<td>Course Enrollment</td>
											@foreach($plans as $plan)
												<td class="text-center">
													@if(in_array('course_enrollment', $plan->features ?? []))
														<i class="fas fa-check text-success"></i>
													@else
														<i class="fas fa-times text-danger"></i>
													@endif
												</td>
											@endforeach
										</tr>
									</tbody>
								</table>
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
/* Pricing Cards Container */
.pricing-card {
	background: #fff;
	border: 2px solid #e9ecef;
	border-radius: 12px;
	padding: 1.5rem;
	transition: all 0.3s ease;
	position: relative;
	height: 100%;
	display: flex;
	flex-direction: column;
	min-height: 500px;
}

.pricing-card:hover {
	border-color: #186dde;
	box-shadow: 0 6px 20px rgba(24, 109, 222, 0.12);
	transform: translateY(-3px);
}

.pricing-card.popular {
	border-color: #186dde;
	border-width: 3px;
	box-shadow: 0 4px 16px rgba(24, 109, 222, 0.15);
}

.popular-badge {
	position: absolute;
	top: -10px;
	right: 15px;
	background: linear-gradient(135deg, #186dde 0%, #1557b0 100%);
	color: #fff;
	padding: 0.3rem 0.75rem;
	border-radius: 20px;
	font-size: 0.7rem;
	font-weight: 600;
	letter-spacing: 0.5px;
	box-shadow: 0 2px 8px rgba(24, 109, 222, 0.3);
}

/* Plan Header */
.plan-header {
	text-align: center;
	margin-bottom: 1.25rem;
	padding-bottom: 1.25rem;
	border-bottom: 2px solid #f0f0f0;
}

.plan-name {
	font-size: 1.1rem;
	font-weight: 700;
	color: #2c3e50;
	margin-bottom: 0.75rem;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.plan-price {
	display: flex;
	align-items: flex-start;
	justify-content: center;
	margin-bottom: 0.5rem;
	line-height: 1;
}

.plan-price .currency {
	font-size: 1rem;
	font-weight: 600;
	color: #6c757d;
	margin-top: 0.4rem;
	margin-right: 0.1rem;
}

.plan-price .amount {
	font-size: 2.5rem;
	font-weight: 700;
	color: #186dde;
	line-height: 1;
}

.plan-price .period {
	font-size: 0.75rem;
	color: #6c757d;
	margin-top: 1.75rem;
	margin-left: 0.15rem;
}

.plan-description {
	font-size: 0.8rem;
	color: #6c757d;
	margin: 0;
	line-height: 1.4;
	min-height: 40px;
}

/* Plan Features */
.plan-features {
	flex: 1;
	margin-bottom: 1.25rem;
	overflow-y: auto;
	max-height: 250px;
}

.plan-features ul {
	list-style: none;
	padding: 0;
	margin: 0;
}

.plan-features li {
	padding: 0.4rem 0;
	font-size: 0.8rem;
	color: #2c3e50;
	line-height: 1.4;
	display: flex;
	align-items: flex-start;
}

.plan-features li i {
	margin-top: 0.15rem;
	flex-shrink: 0;
}

/* Plan Action */
.plan-action {
	margin-top: auto;
	padding-top: 1rem;
}

.plan-action .btn {
	font-size: 0.85rem;
	padding: 0.7rem 0.75rem;
	font-weight: 600;
	letter-spacing: 0.3px;
	white-space: nowrap;
}

.plan-action .btn i {
	font-size: 0.8rem;
}

/* Feature Comparison Table */
.table-responsive {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
	border-radius: 8px;
	overflow: hidden;
}

.table {
	margin-bottom: 0;
	font-size: 0.875rem;
}

.table thead th {
	background: #f8f9fa;
	font-weight: 600;
	border-bottom: 2px solid #dee2e6;
	padding: 1rem;
	font-size: 0.875rem;
}

.table tbody td {
	padding: 0.875rem;
	vertical-align: middle;
}

.table tbody tr:hover {
	background-color: #f8f9fa;
}

/* Page Title */
.title-2 {
	font-size: 1.5rem;
	font-weight: 700;
	color: #2c3e50;
}

/* Alert Styling */
.alert {
	font-size: 0.875rem;
}

.alert h6 {
	font-size: 0.95rem;
	margin-bottom: 0.5rem;
}

/* Responsive Adjustments */
@media (max-width: 1199px) {
	.pricing-card {
		min-height: 550px;
	}

	.plan-price .amount {
		font-size: 2.25rem;
	}
}

@media (max-width: 991px) {
	.pricing-card {
		min-height: auto;
		margin-bottom: 1rem;
	}

	.plan-features {
		max-height: none;
	}
}

@media (max-width: 575px) {
	.pricing-card {
		padding: 1.25rem;
	}

	.plan-name {
		font-size: 1rem;
	}

	.plan-price .amount {
		font-size: 2rem;
	}

	.popular-badge {
		font-size: 0.65rem;
		padding: 0.25rem 0.6rem;
	}
}
</style>
@endsection
