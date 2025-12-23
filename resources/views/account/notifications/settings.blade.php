{{-- Notification Settings Page --}}
@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('account.inc.sidebar')
				</div>

				<div class="col-md-9">
					<div class="card">
						<div class="card-header">
							<h4 class="mb-0">
								<i class="fas fa-bell"></i> Notification Settings
							</h4>
						</div>
						<div class="card-body">
							@if (session('success'))
								<div class="alert alert-success alert-dismissible fade show" role="alert">
									<i class="fas fa-check-circle"></i> {{ session('success') }}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							@endif

							<div class="mb-4">
								<h5><i class="fas fa-briefcase"></i> Job Notifications</h5>
								<p class="text-muted">Manage how you receive job posting notifications</p>
							</div>

							<form method="POST" action="{{ route('notifications.toggle') }}">
								@csrf

								<div class="card mb-3">
									<div class="card-body">
										<div class="row align-items-center">
											<div class="col-md-8">
												<h6><i class="fas fa-envelope"></i> Email Notifications for New Jobs</h6>
												<p class="text-muted small mb-0">
													Receive email alerts when new jobs are posted in <strong>{{ auth()->user()->country->name ?? 'your country' }}</strong>
												</p>
											</div>
											<div class="col-md-4 text-right">
												<div class="custom-control custom-switch" style="font-size: 1.5rem;">
													<input type="checkbox"
														   class="custom-control-input"
														   id="jobNotificationSwitch"
														   name="enabled"
														   value="1"
														   {{ $user->job_notification_enabled ? 'checked' : '' }}
														   onchange="this.form.submit()">
													<label class="custom-control-label" for="jobNotificationSwitch"></label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>

							<div class="alert {{ $user->job_notification_enabled ? 'alert-success' : 'alert-warning' }}">
								<h6 class="alert-heading">
									<i class="fas fa-info-circle"></i>
									{{ $user->job_notification_enabled ? 'Notifications Enabled' : 'Notifications Disabled' }}
								</h6>
								@if ($user->job_notification_enabled)
									<p class="mb-0">
										✅ You will receive email notifications when new jobs are posted in <strong>{{ auth()->user()->country->name ?? 'your country' }}</strong>.
									</p>
								@else
									<p class="mb-0">
										❌ You will NOT receive job notification emails. Enable notifications above to stay updated on new job opportunities!
									</p>
								@endif
							</div>

							<div class="card bg-light">
								<div class="card-body">
									<h6><i class="fas fa-lightbulb"></i> Why Enable Notifications?</h6>
									<ul class="mb-2">
										<li><strong>Early Access:</strong> Be among the first to know about new job openings</li>
										<li><strong>Targeted Alerts:</strong> Only receive notifications for jobs in your country</li>
										<li><strong>Competitive Edge:</strong> Apply early and increase your chances</li>
										<li><strong>Never Miss Out:</strong> Stay informed about opportunities in your field</li>
									</ul>
									<p class="mb-0 text-muted small">
										<i class="fas fa-shield-alt"></i> We respect your privacy. You can unsubscribe at any time using the link in our emails.
									</p>
								</div>
							</div>

							<hr class="mt-4 mb-4">

							<div class="row">
								<div class="col-md-6">
									<h6><i class="fas fa-cog"></i> Account Information</h6>
									<table class="table table-sm table-borderless">
										<tr>
											<td class="text-muted">Email:</td>
											<td><strong>{{ $user->email }}</strong></td>
										</tr>
										<tr>
											<td class="text-muted">Country:</td>
											<td><strong>{{ $user->country->name ?? 'Not set' }}</strong></td>
										</tr>
										<tr>
											<td class="text-muted">Status:</td>
											<td>
												<span class="badge badge-{{ $user->job_notification_enabled ? 'success' : 'secondary' }}">
													{{ $user->job_notification_enabled ? 'Active' : 'Inactive' }}
												</span>
											</td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									<h6><i class="fas fa-question-circle"></i> Need Help?</h6>
									<p class="text-muted small">
										If you're not receiving notifications even after enabling them:
									</p>
									<ul class="small text-muted">
										<li>Check your spam/junk folder</li>
										<li>Add our email to your contacts</li>
										<li>Verify your email address is correct</li>
										<li>Contact support if issues persist</li>
									</ul>
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
		// Auto-hide success message after 5 seconds
		setTimeout(function() {
			$('.alert-success').fadeOut('slow');
		}, 5000);
	</script>
@endsection
