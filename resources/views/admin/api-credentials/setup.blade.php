@extends('admin.layouts.master')

@section('header')
    <div class="row page-titles">
        <div class="col-md-6 col-12 align-self-center">
            <h2 class="mb-0">
                <i class="fa-solid fa-key"></i> {{ $title ?? 'API Credentials Setup' }}
            </h2>
        </div>
        <div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
            <ol class="breadcrumb mb-0 p-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ admin_url('api-credentials') }}">API Credentials</a></li>
                <li class="breadcrumb-item active d-flex align-items-center">Setup</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card rounded mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fa-regular fa-circle-question"></i> About API Credentials
                    </h5>
                    <p class="card-text">
                        Configure your API credentials for job feed aggregation services.
                        These credentials are stored securely with encryption and used to fetch job listings from external APIs.
                    </p>
                    <p class="card-text text-muted">
                        <i class="fa-solid fa-shield-halved"></i>
                        All API keys are encrypted before storage and never displayed in plain text after saving.
                    </p>
                </div>
            </div>

            <form action="{{ admin_url('api-credentials/setup') }}" method="POST">
                @csrf

                @foreach($credentials as $provider => $data)
                    @php
                        $credential = $data['model'];
                        $config = $data['config'];
                        $existingCreds = $credential->credentials ?? [];
                    @endphp

                    <div class="card rounded mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-plug"></i>
                                {{ $config['name'] }}
                            </h5>
                            <div>
                                @if($credential->exists && $credential->hasCredentials())
                                    @if($credential->last_verified_at && $credential->last_verified_at->diffInHours() < 24)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-info">Configured</span>
                                    @endif
                                @else
                                    <span class="badge bg-warning">Not Configured</span>
                                @endif

                                @if($credential->exists)
                                    <a href="{{ admin_url('api-credentials/' . $credential->id . '/test') }}"
                                       class="btn btn-sm btn-outline-info ms-2"
                                       title="Test API Connection">
                                        <i class="fa-solid fa-flask"></i> Test
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="credentials[{{ $provider }}][provider]" value="{{ $provider }}">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="active_{{ $provider }}"
                                               name="credentials[{{ $provider }}][is_active]"
                                               value="1"
                                               {{ $credential->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active_{{ $provider }}">
                                            Enable this API
                                        </label>
                                    </div>
                                </div>

                                @foreach($config['fields'] as $field => $fieldConfig)
                                    <div class="col-md-6 mb-3">
                                        <label for="{{ $provider }}_{{ $field }}" class="form-label">
                                            {{ $fieldConfig['label'] }}
                                            @if($fieldConfig['required'])
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="input-group">
                                            <input type="{{ $fieldConfig['type'] === 'password' ? 'password' : 'text' }}"
                                                   class="form-control"
                                                   id="{{ $provider }}_{{ $field }}"
                                                   name="credentials[{{ $provider }}][{{ $field }}]"
                                                   placeholder="{{ !empty($existingCreds[$field]) ? '••••••••••••' : 'Enter ' . $fieldConfig['label'] }}"
                                                   {{ !empty($existingCreds[$field]) ? '' : ($fieldConfig['required'] ? 'required' : '') }}>
                                            @if($fieldConfig['type'] === 'password')
                                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                                        data-target="{{ $provider }}_{{ $field }}">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                        @if(!empty($existingCreds[$field]))
                                            <small class="text-muted">
                                                <i class="fa-solid fa-check-circle text-success"></i>
                                                Already configured. Leave empty to keep current value.
                                            </small>
                                        @endif
                                    </div>
                                @endforeach

                                <div class="col-md-3 mb-3">
                                    <label for="{{ $provider }}_daily_quota" class="form-label">
                                        Daily Quota
                                    </label>
                                    <input type="number" class="form-control"
                                           id="{{ $provider }}_daily_quota"
                                           name="credentials[{{ $provider }}][daily_quota]"
                                           value="{{ $credential->daily_quota }}"
                                           placeholder="Unlimited">
                                    <small class="text-muted">Max API calls per day</small>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="{{ $provider }}_rate_limit" class="form-label">
                                        Rate Limit (per min)
                                    </label>
                                    <input type="number" class="form-control"
                                           id="{{ $provider }}_rate_limit"
                                           name="credentials[{{ $provider }}][rate_limit_per_minute]"
                                           value="{{ $credential->rate_limit_per_minute }}"
                                           placeholder="Unlimited">
                                    <small class="text-muted">Max calls per minute</small>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <a href="{{ $config['docs_url'] }}" target="_blank" class="text-muted small">
                                        <i class="fa-solid fa-external-link-alt"></i>
                                        Get API credentials from {{ $config['name'] }}
                                    </a>
                                </div>
                            </div>

                            @if($credential->exists && $credential->last_used_at)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            <i class="fa-regular fa-clock"></i>
                                            Last used: {{ $credential->last_used_at->diffForHumans() }}
                                            @if($credential->daily_quota)
                                                | Today: {{ $credential->requests_today }} / {{ $credential->daily_quota }} requests
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="card rounded">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-save"></i> Save All Credentials
                        </button>
                        <a href="{{ admin_url('api-credentials') }}" class="btn btn-secondary btn-lg ms-2">
                            <i class="fa-solid fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('after_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
@endsection
