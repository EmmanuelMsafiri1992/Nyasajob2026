{{-- Unsubscribe from Job Notifications Page --}}
@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<div class="card mt-5 mb-5">
						<div class="card-header">
							<h4 class="mb-0">
								<i class="fas fa-bell-slash"></i> Unsubscribe from Job Notifications
							</h4>
						</div>
						<div class="card-body">
							<div class="text-center mb-4">
								<i class="fas fa-envelope-open-text fa-4x text-warning mb-3"></i>
								<h5>Are you sure you want to unsubscribe?</h5>
							</div>

							<div class="alert alert-info">
								<p><strong>Hello {{ $user->name }}!</strong></p>
								<p>You are about to unsubscribe from job notification emails.</p>
								<p>This means you will <strong>no longer receive email alerts</strong> when new jobs are posted in <strong>{{ $user->country->name ?? 'your country' }}</strong>.</p>
							</div>

							<div class="alert alert-warning">
								<h6><i class="fas fa-info-circle"></i> What you'll miss:</h6>
								<ul class="mb-0">
									<li>Email notifications for new job postings in your country</li>
									<li>Early access to fresh job opportunities</li>
									<li>Competitive advantage in the job market</li>
								</ul>
							</div>

							<form method="POST" action="{{ route('notifications.process-unsubscribe', $encodedEmail) }}">
								@csrf
								<div class="text-center mt-4">
									<button type="submit" class="btn btn-danger btn-lg">
										<i class="fas fa-bell-slash"></i> Yes, Unsubscribe Me
									</button>
									<a href="{{ url('/') }}" class="btn btn-success btn-lg ml-2">
										<i class="fas fa-check"></i> No, Keep Me Subscribed
									</a>
								</div>
							</form>

							<hr class="mt-4 mb-4">

							<div class="text-muted small">
								<p><strong>Prefer to customize your notifications instead?</strong></p>
								<p>If you want to control how often you receive notifications, please <a href="{{ \App\Helpers\UrlGen::login() }}">login to your account</a> and visit <a href="{{ route('notifications.settings') }}">Notification Settings</a>.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
