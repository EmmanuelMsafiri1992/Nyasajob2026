{{-- Unsubscribe Error Page --}}
@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<div class="card mt-5 mb-5">
						<div class="card-header bg-danger text-white">
							<h4 class="mb-0">
								<i class="fas fa-exclamation-triangle"></i> Unsubscribe Error
							</h4>
						</div>
						<div class="card-body text-center">
							<div class="mb-4 mt-3">
								<i class="fas fa-exclamation-circle fa-5x text-danger"></i>
							</div>

							<h4 class="mb-3">Oops! Something Went Wrong</h4>

							<div class="alert alert-danger">
								<p><strong>Error:</strong> {{ $error ?? 'Unable to process your unsubscribe request.' }}</p>
								<p class="mb-0">The unsubscribe link may be invalid or expired.</p>
							</div>

							<div class="alert alert-info">
								<h6><i class="fas fa-question-circle"></i> Need Help?</h6>
								<p>If you're trying to unsubscribe from job notifications, you can:</p>
								<ul class="text-start">
									<li>Log into your account and visit Notification Settings</li>
									<li>Contact our support team for assistance</li>
									<li>Check your email for a recent job notification and use that unsubscribe link</li>
								</ul>
							</div>

							<div class="mt-4">
								<a href="{{ url('/') }}" class="btn btn-primary btn-lg">
									<i class="fas fa-home"></i> Go to Homepage
								</a>
								<a href="{{ \App\Helpers\UrlGen::login() }}" class="btn btn-outline-primary btn-lg ms-2">
									<i class="fas fa-sign-in-alt"></i> Login to Account
								</a>
							</div>

							<hr class="mt-4 mb-3">

							<p class="text-muted small">
								<i class="fas fa-envelope"></i> For support, contact us at <strong>{{ config('settings.app.email') }}</strong>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
