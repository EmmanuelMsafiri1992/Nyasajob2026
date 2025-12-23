{{--
 * Nyasajob - Job Board Web Application
 * Job Matches List View
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
							{{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				@endif

				@if (session('error'))
					<div class="col-xl-12">
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							{{ session('error') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				@endif

				<div class="col-md-3 page-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
				</div>
				<!--/.page-sidebar-->

				<div class="col-md-9 page-content">
					<div class="inner-box">
						<h2 class="title-2"><i class="fas fa-bullseye"></i> {{ t('Job Matches') }}</h2>

						{{-- Statistics Cards --}}
						<div class="row mb-4">
							<div class="col-md-3">
								<div class="card bg-primary text-white text-center">
									<div class="card-body">
										<h3 class="mb-0">{{ $stats['total'] }}</h3>
										<p class="mb-0"><small>Total Matches</small></p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="card bg-warning text-white text-center">
									<div class="card-body">
										<h3 class="mb-0">{{ $stats['pending'] }}</h3>
										<p class="mb-0"><small>Pending Review</small></p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="card bg-success text-white text-center">
									<div class="card-body">
										<h3 class="mb-0">{{ $stats['auto_applied'] + $stats['manually_applied'] }}</h3>
										<p class="mb-0"><small>Applied</small></p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="card bg-info text-white text-center">
									<div class="card-body">
										<h3 class="mb-0">{{ round($stats['avg_match'] ?? 0) }}%</h3>
										<p class="mb-0"><small>Avg Match</small></p>
									</div>
								</div>
							</div>
						</div>

						{{-- Filters --}}
						<div class="card mb-3">
							<div class="card-body">
								<form method="GET" action="{{ route('job-matches.index') }}" class="row g-3">
									<div class="col-md-3">
										<select name="status" class="form-control">
											<option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
											<option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
											<option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
											<option value="auto_applied" {{ request('status') == 'auto_applied' ? 'selected' : '' }}>Auto Applied</option>
											<option value="manually_applied" {{ request('status') == 'manually_applied' ? 'selected' : '' }}>Manually Applied</option>
											<option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
										</select>
									</div>
									<div class="col-md-3">
										<select name="min_match" class="form-control">
											<option value="">Min Match %</option>
											<option value="90" {{ request('min_match') == 90 ? 'selected' : '' }}>90%+</option>
											<option value="80" {{ request('min_match') == 80 ? 'selected' : '' }}>80%+</option>
											<option value="70" {{ request('min_match') == 70 ? 'selected' : '' }}>70%+</option>
											<option value="60" {{ request('min_match') == 60 ? 'selected' : '' }}>60%+</option>
											<option value="50" {{ request('min_match') == 50 ? 'selected' : '' }}>50%+</option>
										</select>
									</div>
									<div class="col-md-3">
										<select name="applied" class="form-control">
											<option value="">Application Status</option>
											<option value="yes" {{ request('applied') == 'yes' ? 'selected' : '' }}>Applied</option>
											<option value="no" {{ request('applied') == 'no' ? 'selected' : '' }}>Not Applied</option>
										</select>
									</div>
									<div class="col-md-3">
										<button type="submit" class="btn btn-primary">
											<i class="fas fa-filter"></i> Filter
										</button>
										<a href="{{ route('job-matches.index') }}" class="btn btn-secondary">
											<i class="fas fa-undo"></i> Reset
										</a>
									</div>
								</form>
							</div>
						</div>

						{{-- Matches List --}}
						@if($matches->isEmpty())
							<div class="alert alert-info" role="alert">
								<h4 class="alert-heading">No job matches yet!</h4>
								<p>We haven't found any jobs matching your preferences yet. Here's what you can do:</p>
								<ul>
									<li>Make sure you've set up your <a href="{{ route('job-preferences.index') }}">job preferences</a></li>
									<li>Check back later - new jobs are posted regularly</li>
									<li>Adjust your preferences to broaden your search</li>
								</ul>
							</div>
						@else
							<div class="list-group">
								@foreach($matches as $match)
									<div class="list-group-item list-group-item-action mb-3 border rounded">
										<div class="row">
											<div class="col-md-8">
												<div class="d-flex w-100 justify-content-between">
													<h5 class="mb-1">
														<a href="{{ route('job-matches.show', $match->id) }}" class="text-dark">
															{{ $match->post->title ?? 'Job Post' }}
														</a>
													</h5>
													<div>
														<span class="badge bg-{{ $match->match_color }} rounded-pill" style="font-size: 1rem;">
															{{ $match->match_percentage }}% Match
														</span>
													</div>
												</div>
												<p class="mb-1">
													<i class="fas fa-building"></i> {{ $match->post->company_name ?? 'Company' }}
													&nbsp;&nbsp;
													<i class="fas fa-map-marker-alt"></i> {{ $match->post->city->name ?? 'Location' }}
													&nbsp;&nbsp;
													@if($match->post->category)
														<i class="fas fa-tag"></i> {{ $match->post->category->name }}
													@endif
												</p>
												<small class="text-muted">
													<i class="fas fa-clock"></i> Matched {{ $match->created_at->diffForHumans() }}
												</small>

												{{-- Status Badge --}}
												<div class="mt-2">
													@if($match->status == 'pending_review')
														<span class="badge bg-warning">Pending Review</span>
													@elseif($match->status == 'approved')
														<span class="badge bg-info">Approved</span>
													@elseif($match->status == 'auto_applied')
														<span class="badge bg-success">
															<i class="fas fa-robot"></i> Auto Applied
															{{ $match->applied_at ? $match->applied_at->diffForHumans() : '' }}
														</span>
													@elseif($match->status == 'manually_applied')
														<span class="badge bg-success">
															<i class="fas fa-check"></i> Applied
															{{ $match->applied_at ? $match->applied_at->diffForHumans() : '' }}
														</span>
													@elseif($match->status == 'rejected')
														<span class="badge bg-danger">Rejected</span>
													@endif
												</div>

												{{-- Match Details Preview --}}
												@if($match->match_details)
													<div class="mt-2">
														<small class="text-muted">
															@php
																$details = is_string($match->match_details) ? json_decode($match->match_details, true) : $match->match_details;
																$matchedSkills = $details['skills']['matched_skills'] ?? [];
															@endphp
															@if(!empty($matchedSkills))
																<i class="fas fa-check-circle text-success"></i>
																Matched: {{ implode(', ', array_slice($matchedSkills, 0, 3)) }}
																@if(count($matchedSkills) > 3)
																	<span class="badge bg-secondary">+{{ count($matchedSkills) - 3 }} more</span>
																@endif
															@endif
														</small>
													</div>
												@endif
											</div>

											<div class="col-md-4 text-end">
												<div class="d-grid gap-2">
													@if(!$match->applied)
														<a href="{{ route('job-matches.show', $match->id) }}" class="btn btn-primary btn-sm">
															<i class="fas fa-eye"></i> View Details
														</a>
														@if($match->status != 'rejected')
															<form action="{{ route('job-matches.reject', $match->id) }}" method="POST" style="display: inline;">
																@csrf
																<button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Are you sure you want to reject this match?')">
																	<i class="fas fa-times"></i> Not Interested
																</button>
															</form>
														@endif
													@else
														<a href="{{ route('job-matches.show', $match->id) }}" class="btn btn-success btn-sm">
															<i class="fas fa-check-circle"></i> View Application
														</a>
													@endif
												</div>
											</div>
										</div>
									</div>
								@endforeach
							</div>

							{{-- Pagination --}}
							<div class="mt-4">
								{{ $matches->links() }}
							</div>
						@endif

						{{-- Quick Action Button --}}
						<div class="text-center mt-4">
							<a href="{{ route('job-preferences.index') }}" class="btn btn-outline-primary">
								<i class="fas fa-cog"></i> Adjust My Preferences
							</a>
						</div>

					</div>
				</div>
				<!--/.page-content-->

			</div>
		</div>
	</div>
@endsection

@section('after_styles')
	<style>
		.list-group-item {
			transition: all 0.3s ease;
		}
		.list-group-item:hover {
			box-shadow: 0 4px 8px rgba(0,0,0,0.1);
			transform: translateY(-2px);
		}
		.card {
			transition: all 0.3s ease;
		}
		.card:hover {
			transform: translateY(-2px);
		}
	</style>
@endsection
