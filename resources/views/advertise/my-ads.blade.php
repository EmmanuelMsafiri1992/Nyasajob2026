@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1 class="title-1 mb-4">My Advertisements</h1>

					@if(session('success'))
						<div class="alert alert-success alert-dismissible fade show">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							{!! session('success') !!}
						</div>
					@endif

					@if(session('error'))
						<div class="alert alert-danger alert-dismissible fade show">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							{{ session('error') }}
						</div>
					@endif

					<div class="mb-3">
						<a href="{{ route('advertise.index') }}" class="btn btn-primary">
							<i class="fas fa-plus"></i> Create New Advertisement
						</a>
					</div>

					@forelse($ads as $ad)
						<div class="card mb-3">
							<div class="card-body">
								<div class="row">
									<div class="col-md-2">
										@if($ad->image_path)
											<img src="{{ $ad->image_path }}" class="img-fluid rounded" alt="{{ $ad->title }}">
										@else
											<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px;">
												<i class="fas fa-image fa-3x text-muted"></i>
											</div>
										@endif
									</div>

									<div class="col-md-7">
										<h4 class="mb-2">{{ $ad->title }}</h4>

										@if($ad->description)
											<p class="text-muted mb-2">{{ Str::limit($ad->description, 150) }}</p>
										@endif

										<div class="mb-2">
											<span class="badge
												@if($ad->status == 'active') bg-success
												@elseif($ad->status == 'pending') bg-warning
												@elseif($ad->status == 'paused') bg-secondary
												@elseif($ad->status == 'expired') bg-dark
												@else bg-danger
												@endif
											">{{ ucfirst($ad->status) }}</span>

											@if($ad->subscription && $ad->subscription->package)
												<span class="badge bg-info">{{ $ad->subscription->package->name }}</span>
											@endif
										</div>

										@if($ad->url)
											<p class="mb-1 small">
												<i class="fas fa-link"></i> <a href="{{ $ad->url }}" target="_blank">{{ $ad->url }}</a>
											</p>
										@endif

										<div class="small text-muted">
											<i class="fas fa-calendar"></i> Created: {{ $ad->created_at->format('M d, Y') }}
											@if($ad->starts_at && $ad->expires_at)
												<br>
												<i class="fas fa-clock"></i> Active: {{ $ad->starts_at->format('M d, Y') }} - {{ $ad->expires_at->format('M d, Y') }}
											@endif
										</div>
									</div>

									<div class="col-md-3 text-end">
										{{-- Statistics --}}
										<div class="mb-3">
											<div class="mb-2">
												<i class="fas fa-eye text-info"></i>
												<strong>{{ number_format($ad->impressions) }}</strong>
												<small class="text-muted">views</small>
											</div>
											<div class="mb-2">
												<i class="fas fa-mouse-pointer text-primary"></i>
												<strong>{{ number_format($ad->clicks) }}</strong>
												<small class="text-muted">clicks</small>
											</div>
											@if($ad->impressions > 0)
												<div class="small text-muted">
													CTR: {{ number_format(($ad->clicks / $ad->impressions) * 100, 2) }}%
												</div>
											@endif
										</div>

										{{-- Actions --}}
										<div class="btn-group-vertical w-100" role="group">
											@if($ad->status == 'active')
												<form action="{{ route('advertise.pause', $ad->id) }}" method="POST" class="d-inline">
													@csrf
													<button type="submit" class="btn btn-sm btn-warning w-100">
														<i class="fas fa-pause"></i> Pause
													</button>
												</form>
											@elseif($ad->status == 'paused')
												<form action="{{ route('advertise.resume', $ad->id) }}" method="POST" class="d-inline">
													@csrf
													<button type="submit" class="btn btn-sm btn-success w-100">
														<i class="fas fa-play"></i> Resume
													</button>
												</form>
											@endif
										</div>

										{{-- Targeting Info --}}
										@if($ad->targeting && $ad->targeting->count() > 0)
											<div class="mt-3 small">
												<strong>Targeting:</strong><br>
												@foreach($ad->targeting as $target)
													<span class="badge bg-secondary">{{ $target->target_code }}</span>
												@endforeach
											</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					@empty
						<div class="card">
							<div class="card-body text-center py-5">
								<i class="fas fa-ad fa-4x text-muted mb-3"></i>
								<h4>No advertisements yet</h4>
								<p class="text-muted">Create your first advertisement to get started!</p>
								<a href="{{ route('advertise.index') }}" class="btn btn-primary">
									<i class="fas fa-plus"></i> Create Advertisement
								</a>
							</div>
						</div>
					@endforelse

					{{-- Pagination --}}
					@if($ads->hasPages())
						<div class="mt-4">
							{{ $ads->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
