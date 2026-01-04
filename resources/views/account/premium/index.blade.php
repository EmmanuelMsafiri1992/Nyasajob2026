@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 page-sidebar">
                    @include('account.inc.sidebar')
                </div>

                <div class="col-md-9 page-content">
                    @include('flash::message')

                    <div class="inner-box">
                        <h2 class="title-2">
                            <strong><i class="fa-solid fa-crown"></i> Premium Subscription</strong>
                        </h2>

                        @if ($hasPremium)
                            {{-- Active Subscription --}}
                            <div class="alert alert-success">
                                <h5><i class="fa-solid fa-check-circle"></i> Your Premium Subscription is Active</h5>
                                <p class="mb-0">
                                    Valid until: <strong>{{ $subscription->expires_at->format('F d, Y') }}</strong>
                                    ({{ $subscription->daysRemaining() }} days remaining)
                                </p>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-body">
                                            <i class="fa-solid fa-bullseye fa-2x text-primary mb-2"></i>
                                            <h6>Job Matches</h6>
                                            <a href="{{ route('account.premium.job-matches') }}" class="btn btn-primary btn-sm">Find Matches</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-body">
                                            <i class="fa-solid fa-sliders fa-2x text-info mb-2"></i>
                                            <h6>Job Preferences</h6>
                                            <a href="{{ route('account.premium.preferences') }}" class="btn btn-info btn-sm">Update</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-body">
                                            <i class="fa-solid fa-file-alt fa-2x text-success mb-2"></i>
                                            <h6>CV Tips</h6>
                                            <a href="{{ route('account.premium.cv-tips') }}" class="btn btn-success btn-sm">View Tips</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-body">
                                            <i class="fa-solid fa-user-tie fa-2x text-warning mb-2"></i>
                                            <h6>Interview Prep</h6>
                                            <a href="{{ route('account.premium.interview-prep') }}" class="btn btn-warning btn-sm">Prepare</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Subscription Details --}}
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Subscription Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Plan:</strong></td>
                                            <td>Job Seeker Premium</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td>${{ number_format($subscription->amount, 2) }}/month</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge {{ $subscription->statusBadgeClass }}">{{ $subscription->statusLabel }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Started:</strong></td>
                                            <td>{{ $subscription->starts_at?->format('F d, Y') ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expires:</strong></td>
                                            <td>{{ $subscription->expires_at?->format('F d, Y') ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Auto-Renew:</strong></td>
                                            <td>{{ $subscription->auto_renew ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    </table>

                                    @if ($subscription->isActive())
                                        <hr>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                            Cancel Subscription
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if ($preferences)
                                <div class="card mt-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Your Job Preferences</h5>
                                        <a href="{{ route('account.premium.preferences') }}" class="btn btn-sm btn-primary">Edit</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $preferences->profileCompletion }}%">
                                                Profile {{ $preferences->profileCompletion }}% Complete
                                            </div>
                                        </div>
                                        @if ($preferences->desired_job_title)
                                            <p><strong>Looking for:</strong> {{ $preferences->desired_job_title }}</p>
                                        @endif
                                        @if ($preferences->urgency_level)
                                            <p><strong>Urgency:</strong> {{ $preferences->urgencyLabel }}</p>
                                        @endif
                                        @if ($preferences->experience_level)
                                            <p><strong>Experience:</strong> {{ $preferences->experienceLabel }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- No Subscription --}}
                            <div class="text-center py-5">
                                <i class="fa-solid fa-crown fa-4x text-warning mb-3"></i>
                                <h3>Unlock Premium Features</h3>
                                <p class="text-muted">Get personalized job matches, CV tips, and interview preparation for just $5/month</p>
                                <a href="{{ route('account.premium.subscribe') }}" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-rocket"></i> Subscribe Now - $5/month
                                </a>
                            </div>

                            <div class="row mt-5">
                                <div class="col-12">
                                    <h4>What You Get:</h4>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <i class="fa-solid fa-check text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Exact Job Matching</h6>
                                            <p class="text-muted small">Tell us your preferences and we'll find jobs that match exactly what you're looking for.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <i class="fa-solid fa-check text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>CV Refinement Tips</h6>
                                            <p class="text-muted small">Get personalized suggestions to improve your CV and stand out to employers.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <i class="fa-solid fa-check text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Interview Preparation</h6>
                                            <p class="text-muted small">Access tips and guidance based on the type of job and company you're applying to.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <i class="fa-solid fa-check text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Email Alerts</h6>
                                            <p class="text-muted small">Get notified instantly when new jobs match your preferences.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($subscriptionHistory->isNotEmpty())
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Subscription History</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Plan</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subscriptionHistory as $sub)
                                                <tr>
                                                    <td>{{ $sub->created_at->format('M d, Y') }}</td>
                                                    <td>Job Seeker Premium</td>
                                                    <td>${{ number_format($sub->amount, 2) }}</td>
                                                    <td><span class="badge {{ $sub->statusBadgeClass }}">{{ $sub->statusLabel }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Modal --}}
    @if ($hasPremium)
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('account.premium.cancel-subscription') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Cancel Subscription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <strong>Note:</strong> Your subscription fee is non-refundable. You will retain access until {{ $subscription->expires_at->format('F d, Y') }}.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason for cancellation (optional)</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Tell us why you're cancelling..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Subscription</button>
                            <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
