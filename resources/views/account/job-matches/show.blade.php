{{--
 * Nyasajob - Job Board Web Application
 * Job Match Detail View
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
							<i class="fas fa-check-circle"></i> {{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				@endif

				@if (session('error'))
					<div class="col-xl-12">
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="fas fa-exclamation-circle"></i> {{ session('error') }}
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

						{{-- Header --}}
						<div class="d-flex justify-content-between align-items-center mb-4">
							<div>
								<a href="{{ route('job-matches.index') }}" class="btn btn-sm btn-outline-secondary">
									<i class="fas fa-arrow-left"></i> Back to Matches
								</a>
							</div>
							<div>
								<span class="badge bg-{{ $match->match_color }} rounded-pill" style="font-size: 1.5rem;">
									{{ $match->match_percentage }}% Match
								</span>
							</div>
						</div>

						{{-- Job Details Card --}}
						<div class="card mb-4">
							<div class="card-header bg-primary text-white">
								<h3 class="mb-0">{{ $match->post->title ?? 'Job Post' }}</h3>
							</div>
							<div class="card-body">
								<div class="row mb-3">
									<div class="col-md-6">
										<p><strong><i class="fas fa-building"></i> Company:</strong> {{ $match->post->company_name ?? 'N/A' }}</p>
										<p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong> {{ $match->post->city->name ?? 'N/A' }}</p>
										<p><strong><i class="fas fa-tag"></i> Category:</strong> {{ $match->post->category->name ?? 'N/A' }}</p>
									</div>
									<div class="col-md-6">
										@if($match->post->salary_min || $match->post->salary_max)
											<p><strong><i class="fas fa-dollar-sign"></i> Salary:</strong>
												{{ $match->post->salary_min ? '$' . number_format($match->post->salary_min) : '' }}
												{{ $match->post->salary_min && $match->post->salary_max ? '-' : '' }}
												{{ $match->post->salary_max ? '$' . number_format($match->post->salary_max) : '' }}
											</p>
										@endif
										<p><strong><i class="fas fa-briefcase"></i> Employment Type:</strong> {{ $match->post->employment_type ?? 'N/A' }}</p>
										<p><strong><i class="fas fa-clock"></i> Posted:</strong> {{ $match->post->created_at->diffForHumans() }}</p>
									</div>
								</div>

								<hr>

								<h5>Job Description</h5>
								<div class="job-description">
									{!! $match->post->description ?? 'No description available' !!}
								</div>

								<div class="mt-3">
									<a href="{{ url('posts/' . $match->post->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
										<i class="fas fa-external-link-alt"></i> View Full Job Posting
									</a>
								</div>
							</div>
						</div>

						{{-- Match Analysis Card --}}
						<div class="card mb-4">
							<div class="card-header bg-info text-white">
								<h4 class="mb-0">Why This Job Matches You</h4>
							</div>
							<div class="card-body">
								@php
									$details = is_string($match->match_details) ? json_decode($match->match_details, true) : $match->match_details;
								@endphp

								{{-- Category Match --}}
								@if(isset($details['category']))
									<div class="mb-3">
										<h6>
											<i class="fas fa-{{ $details['category']['matched'] ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
											Category Match
											<span class="badge bg-secondary">{{ $details['category']['score'] }}/30 points</span>
										</h6>
										<p class="text-muted">{{ $details['category']['reason'] ?? 'N/A' }}</p>
									</div>
								@endif

								{{-- Skills Match --}}
								@if(isset($details['skills']))
									<div class="mb-3">
										<h6>
											<i class="fas fa-check-circle text-success"></i>
											Skills Match
											<span class="badge bg-secondary">{{ $details['skills']['score'] }}/40 points</span>
										</h6>
										@if(!empty($details['skills']['matched_skills']))
											<p class="mb-1"><strong>Matched Skills:</strong></p>
											<div class="mb-2">
												@foreach($details['skills']['matched_skills'] as $skill)
													<span class="badge bg-success me-1 mb-1">{{ $skill }}</span>
												@endforeach
											</div>
										@endif
										<p class="text-muted">{{ $details['skills']['reason'] ?? 'N/A' }}</p>
									</div>
								@endif

								{{-- Salary Match --}}
								@if(isset($details['salary']))
									<div class="mb-3">
										<h6>
											<i class="fas fa-{{ $details['salary']['matched'] ? 'check-circle text-success' : 'info-circle text-info' }}"></i>
											Salary Match
											<span class="badge bg-secondary">{{ $details['salary']['score'] }}/20 points</span>
										</h6>
										<p class="text-muted">{{ $details['salary']['reason'] ?? 'N/A' }}</p>
									</div>
								@endif

								{{-- Location Match --}}
								@if(isset($details['location']))
									<div class="mb-3">
										<h6>
											<i class="fas fa-{{ $details['location']['matched'] ? 'check-circle text-success' : 'info-circle text-info' }}"></i>
											Location Match
											<span class="badge bg-secondary">{{ $details['location']['score'] }}/10 points</span>
										</h6>
										<p class="text-muted">{{ $details['location']['reason'] ?? 'N/A' }}</p>
									</div>
								@endif

								<hr>
								<h5>Overall Match Quality: <span class="text-{{ $match->match_color }}">{{ $match->match_quality }}</span></h5>
								<div class="progress" style="height: 30px;">
									<div class="progress-bar bg-{{ $match->match_color }}" role="progressbar"
										style="width: {{ $match->match_percentage }}%;"
										aria-valuenow="{{ $match->match_percentage }}" aria-valuemin="0" aria-valuemax="100">
										{{ $match->match_percentage }}%
									</div>
								</div>
							</div>
						</div>

						{{-- Application Status Card --}}
						<div class="card mb-4">
							<div class="card-header bg-{{ $match->applied ? 'success' : 'warning' }} text-white">
								<h4 class="mb-0">
									@if($match->applied)
										<i class="fas fa-check-circle"></i> Application Status
									@else
										<i class="fas fa-paper-plane"></i> Ready to Apply?
									@endif
								</h4>
							</div>
							<div class="card-body">
								@if($match->applied)
									{{-- Already Applied --}}
									<div class="alert alert-success">
										<h5 class="alert-heading">
											@if($match->status == 'auto_applied')
												<i class="fas fa-robot"></i> Auto-Applied by System
											@else
												<i class="fas fa-check"></i> Application Submitted
											@endif
										</h5>
										<p><strong>Applied on:</strong> {{ $match->applied_at->format('M d, Y \a\t H:i A') }}</p>
										@if($match->resume)
											<p><strong>Resume:</strong> {{ $match->resume->name }}</p>
										@endif
										@if($match->cover_letter)
											<hr>
											<p><strong>Cover Letter:</strong></p>
											<div class="border p-3 bg-light">
												{!! nl2br(e($match->cover_letter)) !!}
											</div>
										@endif
									</div>
								@else
									{{-- Application Form --}}
									@if($resumes->isEmpty())
										<div class="alert alert-warning">
											<p>You need to upload a resume before applying to jobs.</p>
											<a href="{{ url('account/resumes/create') }}" class="btn btn-primary">
												<i class="fas fa-upload"></i> Upload Resume
											</a>
										</div>
									@else
										<form method="POST" action="{{ route('job-matches.apply', $match->id) }}">
											@csrf

											{{-- Resume Selection --}}
											<div class="mb-3">
												<label class="form-label"><strong>Select Resume <span class="text-danger">*</span></strong></label>
												<select name="resume_id" class="form-control" required>
													<option value="">-- Choose Resume --</option>
													@foreach($resumes as $resume)
														<option value="{{ $resume->id }}">{{ $resume->name }}</option>
													@endforeach
												</select>
												@error('resume_id')
													<span class="text-danger">{{ $message }}</span>
												@enderror
											</div>

											{{-- Cover Letter --}}
											<div class="mb-3">
												<label class="form-label"><strong>Cover Letter (Optional)</strong></label>
												<textarea name="cover_letter" class="form-control" rows="8"
													placeholder="Write your cover letter here...">{{ old('cover_letter') }}</textarea>
												<small class="text-muted">Leave blank to use your default template.</small>
												@error('cover_letter')
													<span class="text-danger">{{ $message }}</span>
												@enderror
											</div>

											{{-- Action Buttons --}}
											<div class="d-grid gap-2">
												<button type="submit" class="btn btn-primary btn-lg">
													<i class="fas fa-paper-plane"></i> Submit Application
												</button>
												<div class="row">
													<div class="col-6">
														<form action="{{ route('job-matches.approve', $match->id) }}" method="POST">
															@csrf
															<button type="submit" class="btn btn-outline-success w-100">
																<i class="fas fa-thumbs-up"></i> Save for Later
															</button>
														</form>
													</div>
													<div class="col-6">
														<form action="{{ route('job-matches.reject', $match->id) }}" method="POST"
															onsubmit="return confirm('Are you sure you want to reject this match?')">
															@csrf
															<button type="submit" class="btn btn-outline-danger w-100">
																<i class="fas fa-times"></i> Not Interested
															</button>
														</form>
													</div>
												</div>
											</div>
										</form>
									@endif
								@endif
							</div>
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
		.job-description {
			line-height: 1.8;
			font-size: 1rem;
		}
		.card {
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		.progress {
			font-size: 1rem;
			font-weight: bold;
		}
	</style>
@endsection
