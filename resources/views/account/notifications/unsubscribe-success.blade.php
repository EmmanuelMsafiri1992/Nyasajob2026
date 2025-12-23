{{-- Unsubscribe Success Page --}}
@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<div class="card mt-5 mb-5">
						<div class="card-header bg-success text-white">
							<h4 class="mb-0">
								<i class="fas fa-check-circle"></i> Unsubscribed Successfully
							</h4>
						</div>
						<div class="card-body text-center">
							<div class="mb-4 mt-3">
								<i class="fas fa-check-circle fa-5x text-success"></i>
							</div>

							<h4 class="mb-3">You've Been Unsubscribed</h4>

							<div class="alert alert-success">
								<p><strong>{{ $user->name }},</strong> you will no longer receive job notification emails.</p>
								<p class="mb-0">Your email has been removed from our job alerts mailing list.</p>
							</div>

							<div class="alert alert-info">
								<h6><i class="fas fa-lightbulb"></i> Changed your mind?</h6>
								<p>You can re-subscribe at any time by:</p>
								<ul class="text-left">
									<li>Logging into your account</li>
									<li>Going to <strong>Notification Settings</strong></li>
									<li>Enabling job notifications again</li>
								</ul>
							</div>

							<div class="mt-4">
								<a href="{{ url('/') }}" class="btn btn-primary btn-lg">
									<i class="fas fa-home"></i> Go to Homepage
								</a>
								@auth
									<a href="{{ route('notifications.settings') }}" class="btn btn-outline-primary btn-lg ml-2">
										<i class="fas fa-cog"></i> Notification Settings
									</a>
								@else
									<a href="{{ \App\Helpers\UrlGen::login() }}" class="btn btn-outline-primary btn-lg ml-2">
										<i class="fas fa-sign-in-alt"></i> Login to Account
									</a>
								@endauth
							</div>

							<hr class="mt-4 mb-3">

							<p class="text-muted small">
								<i class="fas fa-info-circle"></i> You'll still receive important account-related emails (password resets, security alerts, etc.)
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
