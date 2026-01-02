@extends('admin.layouts.master')

@section('header')
	<div class="row page-titles">
		<div class="col-md-5 col-12 align-self-center">
			<h3 class="mb-0">
				{{ $title ?? 'RSS Feed Dashboard' }}
			</h3>
		</div>
		<div class="col-md-7 col-12 align-self-center d-none d-md-flex justify-content-end">
			<ol class="breadcrumb mb-0 p-0 bg-transparent">
				<li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ config('app.name') }}</a></li>
				<li class="breadcrumb-item active">RSS Feeds</li>
			</ol>
		</div>
	</div>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		{{-- Stats Boxes --}}
		<div class="row">
			{{-- Total Sources --}}
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="d-flex flex-row">
							<div class="round round-lg text-white d-inline-block text-center rounded-circle bg-info">
								<i class="ti-rss"></i>
							</div>
							<div class="ms-2 align-self-center">
								<h3 class="mb-0 font-weight-bold">{{ $totalSources }}</h3>
								<span class="text-muted">Feed Sources</span>
							</div>
						</div>
						<div class="mt-2">
							<span class="badge bg-success">{{ $activeSources }} Active</span>
							<span class="badge bg-warning">{{ $pausedSources }} Paused</span>
							<span class="badge bg-danger">{{ $failedSources }} Failed</span>
						</div>
					</div>
				</div>
			</div>

			{{-- Pending Items --}}
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="d-flex flex-row">
							<div class="round round-lg text-white d-inline-block text-center rounded-circle bg-warning">
								<i class="ti-time"></i>
							</div>
							<div class="ms-2 align-self-center">
								<h3 class="mb-0 font-weight-bold">{{ $pendingItems }}</h3>
								<span class="text-muted">Pending Review</span>
							</div>
						</div>
						<div class="mt-2">
							<a href="{{ admin_url('job-feeds/staged?status=pending') }}" class="btn btn-sm btn-outline-warning">
								Review Now
							</a>
						</div>
					</div>
				</div>
			</div>

			{{-- Approved Items --}}
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="d-flex flex-row">
							<div class="round round-lg text-white d-inline-block text-center rounded-circle bg-primary">
								<i class="ti-check-box"></i>
							</div>
							<div class="ms-2 align-self-center">
								<h3 class="mb-0 font-weight-bold">{{ $approvedItems }}</h3>
								<span class="text-muted">Approved</span>
							</div>
						</div>
						<div class="mt-2">
							<a href="{{ admin_url('job-feeds/staged?status=approved') }}" class="btn btn-sm btn-outline-primary">
								View All
							</a>
						</div>
					</div>
				</div>
			</div>

			{{-- Imported Items --}}
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="d-flex flex-row">
							<div class="round round-lg text-white d-inline-block text-center rounded-circle bg-success">
								<i class="ti-check"></i>
							</div>
							<div class="ms-2 align-self-center">
								<h3 class="mb-0 font-weight-bold">{{ $importedItems }}</h3>
								<span class="text-muted">Imported</span>
							</div>
						</div>
						<div class="mt-2">
							<span class="text-success">+{{ $importedToday }} today</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			{{-- Fetch Activity Chart --}}
			<div class="col-lg-8">
				<div class="card">
					<div class="card-header bg-info">
						<h4 class="mb-0 text-white">Fetch Activity (Last 7 Days)</h4>
					</div>
					<div class="card-body">
						<canvas id="fetchActivityChart" height="100"></canvas>
					</div>
				</div>
			</div>

			{{-- Sources by Country --}}
			<div class="col-lg-4">
				<div class="card">
					<div class="card-header bg-primary">
						<h4 class="mb-0 text-white">Sources by Country</h4>
					</div>
					<div class="card-body">
						@if($sourcesByCountry->count() > 0)
							<ul class="list-group list-group-flush">
								@foreach($sourcesByCountry as $item)
									<li class="list-group-item d-flex justify-content-between align-items-center">
										{{ $item->country_code ?? 'Global' }}
										<span class="badge bg-primary rounded-pill">{{ $item->count }}</span>
									</li>
								@endforeach
							</ul>
						@else
							<p class="text-muted text-center">No sources configured yet</p>
						@endif
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			{{-- Recent Logs --}}
			<div class="col-lg-8">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="mb-0">Recent Fetch Logs</h4>
						<a href="{{ admin_url('job-feeds/logs') }}" class="btn btn-sm btn-outline-secondary">View All</a>
					</div>
					<div class="card-body">
						@if($recentLogs->count() > 0)
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Source</th>
											<th>Status</th>
											<th>Found</th>
											<th>New</th>
											<th>Duration</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										@foreach($recentLogs as $log)
											<tr>
												<td>{{ $log->feedSource?->name ?? 'Unknown' }}</td>
												<td>{!! $log->status_badge_html !!}</td>
												<td>{{ $log->items_found }}</td>
												<td>{{ $log->items_new }}</td>
												<td>{{ $log->duration_formatted }}</td>
												<td>{{ $log->created_at->diffForHumans() }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@else
							<p class="text-muted text-center">No fetch logs yet. Add a feed source and run a test fetch.</p>
						@endif
					</div>
				</div>
			</div>

			{{-- Top Sources --}}
			<div class="col-lg-4">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="mb-0">Top Sources</h4>
						<a href="{{ admin_url('job-feeds/sources') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
					</div>
					<div class="card-body">
						@if($topSources->count() > 0)
							<ul class="list-group list-group-flush">
								@foreach($topSources as $source)
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<div>
											<strong>{{ $source->name }}</strong>
											<br>
											<small class="text-muted">{{ $source->total_jobs_imported }} imported</small>
										</div>
										{!! $source->status_badge_html !!}
									</li>
								@endforeach
							</ul>
						@else
							<p class="text-muted text-center">No sources configured yet</p>
							<div class="text-center">
								<a href="{{ admin_url('job-feeds/sources/create') }}" class="btn btn-primary">
									Add First Source
								</a>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>

		{{-- Recent Errors --}}
		@if($recentErrors->count() > 0)
			<div class="row">
				<div class="col-12">
					<div class="card border-danger">
						<div class="card-header bg-danger text-white">
							<h4 class="mb-0">Recent Errors</h4>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Source</th>
											<th>Error</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										@foreach($recentErrors as $error)
											<tr>
												<td>{{ $error->feedSource?->name ?? 'Unknown' }}</td>
												<td>{{ Str::limit($error->error_message, 100) }}</td>
												<td>{{ $error->created_at->diffForHumans() }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif

		{{-- Quick Actions --}}
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4 class="mb-0">Quick Actions</h4>
					</div>
					<div class="card-body">
						<a href="{{ admin_url('job-feeds/sources/create') }}" class="btn btn-primary me-2">
							<i class="la la-plus"></i> Add Feed Source
						</a>
						<a href="{{ admin_url('job-feeds/sources') }}" class="btn btn-outline-secondary me-2">
							<i class="la la-rss"></i> Manage Sources
						</a>
						<a href="{{ admin_url('job-feeds/staged') }}" class="btn btn-outline-secondary me-2">
							<i class="la la-list"></i> Staged Jobs
						</a>
						<a href="{{ admin_url('job-feeds/logs') }}" class="btn btn-outline-secondary">
							<i class="la la-history"></i> Fetch Logs
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
		.round {
			width: 54px;
			height: 54px;
			line-height: 54px;
		}
		.round i {
			font-size: 24px;
		}
	</style>
@endsection

@section('after_scripts')
	<script src="{{ asset('assets/plugins/chartjs/2.7.2/Chart.js') }}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var ctx = document.getElementById('fetchActivityChart').getContext('2d');
			var fetchActivityData = @json($fetchActivity);

			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: fetchActivityData.map(d => d.date),
					datasets: [{
						label: 'Jobs Fetched',
						data: fetchActivityData.map(d => d.fetched),
						backgroundColor: 'rgba(54, 162, 235, 0.5)',
						borderColor: 'rgba(54, 162, 235, 1)',
						borderWidth: 1
					}, {
						label: 'Jobs Imported',
						data: fetchActivityData.map(d => d.imported),
						backgroundColor: 'rgba(75, 192, 192, 0.5)',
						borderColor: 'rgba(75, 192, 192, 1)',
						borderWidth: 1
					}]
				},
				options: {
					responsive: true,
					scales: {
						y: {
							beginAtZero: true
						}
					}
				}
			});
		});
	</script>
@endsection
