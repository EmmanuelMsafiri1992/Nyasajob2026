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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="title-2 mb-0">
                                <strong><i class="fa-solid fa-sliders"></i> Job Preferences</strong>
                            </h2>
                            <a href="{{ route('account.premium.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-arrow-left"></i> Back
                            </a>
                        </div>

                        {{-- Profile Completion --}}
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Profile Completion</span>
                                    <span id="completionPercent">{{ $preferences->profileCompletion }}%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div id="completionBar" class="progress-bar bg-success" role="progressbar" style="width: {{ $preferences->profileCompletion }}%"></div>
                                </div>
                                <small class="text-muted">Complete your profile to get better job matches</small>
                            </div>
                        </div>

                        <form id="preferencesForm" action="{{ route('account.premium.update-preferences') }}" method="POST">
                            @csrf

                            {{-- Job Preferences Section --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-briefcase me-2"></i> Job Preferences</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Desired Job Title</label>
                                            <input type="text" name="desired_job_title" class="form-control"
                                                   value="{{ old('desired_job_title', $preferences->desired_job_title) }}"
                                                   placeholder="e.g., Software Developer, Marketing Manager">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Job Keywords</label>
                                            <input type="text" name="job_keywords" class="form-control"
                                                   value="{{ old('job_keywords', $preferences->job_keywords) }}"
                                                   placeholder="e.g., PHP, Laravel, Remote, Fintech">
                                            <small class="text-muted">Separate keywords with commas</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Preferred Categories</label>
                                            <select name="preferred_categories[]" class="form-select" multiple>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ in_array($category->id, $preferences->preferred_categories ?? []) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Preferred Job Types</label>
                                            <select name="preferred_job_types[]" class="form-select" multiple>
                                                @foreach ($jobTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ in_array($type->id, $preferences->preferred_job_types ?? []) ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input type="hidden" name="remote_only" value="0">
                                        <input type="checkbox" class="form-check-input" name="remote_only" id="remoteOnly" value="1"
                                               {{ $preferences->remote_only ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remoteOnly">Only show remote jobs</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Salary Expectations --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-money-bill-wave me-2"></i> Salary Expectations</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Minimum Salary</label>
                                            <input type="number" name="min_salary" class="form-control"
                                                   value="{{ old('min_salary', $preferences->min_salary) }}"
                                                   placeholder="e.g., 1000">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Maximum Salary</label>
                                            <input type="number" name="max_salary" class="form-control"
                                                   value="{{ old('max_salary', $preferences->max_salary) }}"
                                                   placeholder="e.g., 5000">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Currency</label>
                                            <select name="salary_currency" class="form-select">
                                                <option value="USD" {{ $preferences->salary_currency == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ $preferences->salary_currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ $preferences->salary_currency == 'GBP' ? 'selected' : '' }}>GBP</option>
                                                <option value="ZAR" {{ $preferences->salary_currency == 'ZAR' ? 'selected' : '' }}>ZAR</option>
                                                <option value="NGN" {{ $preferences->salary_currency == 'NGN' ? 'selected' : '' }}>NGN</option>
                                                <option value="KES" {{ $preferences->salary_currency == 'KES' ? 'selected' : '' }}>KES</option>
                                                <option value="MWK" {{ $preferences->salary_currency == 'MWK' ? 'selected' : '' }}>MWK</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Per</label>
                                            <select name="salary_period" class="form-select">
                                                <option value="monthly" {{ $preferences->salary_period == 'monthly' ? 'selected' : '' }}>Month</option>
                                                <option value="yearly" {{ $preferences->salary_period == 'yearly' ? 'selected' : '' }}>Year</option>
                                                <option value="weekly" {{ $preferences->salary_period == 'weekly' ? 'selected' : '' }}>Week</option>
                                                <option value="daily" {{ $preferences->salary_period == 'daily' ? 'selected' : '' }}>Day</option>
                                                <option value="hourly" {{ $preferences->salary_period == 'hourly' ? 'selected' : '' }}>Hour</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Urgency & Availability --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-clock me-2"></i> Urgency & Availability</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">How urgently do you need a job?</label>
                                            <select name="urgency_level" class="form-select">
                                                @foreach ($urgencyLevels as $key => $label)
                                                    <option value="{{ $key }}" {{ $preferences->urgency_level == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Available From</label>
                                            <input type="date" name="available_from" class="form-control"
                                                   value="{{ old('available_from', $preferences->available_from?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Availability Notes</label>
                                        <textarea name="availability_notes" class="form-control" rows="2"
                                                  placeholder="e.g., Available for immediate start, currently serving notice period...">{{ old('availability_notes', $preferences->availability_notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Experience & Qualifications --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-graduation-cap me-2"></i> Experience & Qualifications</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Experience Level</label>
                                            <select name="experience_level" class="form-select">
                                                @foreach ($experienceLevels as $key => $label)
                                                    <option value="{{ $key }}" {{ $preferences->experience_level == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Years of Experience</label>
                                            <input type="number" name="years_of_experience" class="form-control"
                                                   value="{{ old('years_of_experience', $preferences->years_of_experience) }}"
                                                   min="0" max="50">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Key Skills</label>
                                        <textarea name="key_skills" class="form-control" rows="2"
                                                  placeholder="e.g., PHP, Laravel, MySQL, JavaScript, Project Management...">{{ old('key_skills', $preferences->key_skills) }}</textarea>
                                        <small class="text-muted">Separate skills with commas</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Qualifications & Certifications</label>
                                        <textarea name="qualifications" class="form-control" rows="3"
                                                  placeholder="e.g., Bachelor's in Computer Science, AWS Certified, PMP...">{{ old('qualifications', $preferences->qualifications) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Languages</label>
                                        <input type="text" name="languages" class="form-control"
                                               value="{{ old('languages', $preferences->languages) }}"
                                               placeholder="e.g., English (Fluent), Chichewa (Native), French (Basic)">
                                    </div>
                                </div>
                            </div>

                            {{-- Career Profile --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i> Career Profile</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Professional Summary</label>
                                        <textarea name="cv_summary" class="form-control" rows="4"
                                                  placeholder="Briefly describe your professional background and what makes you a great candidate...">{{ old('cv_summary', $preferences->cv_summary) }}</textarea>
                                        <small class="text-muted">This helps us provide better CV tips</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Career Goals</label>
                                        <textarea name="career_goals" class="form-control" rows="3"
                                                  placeholder="What are your short-term and long-term career goals?">{{ old('career_goals', $preferences->career_goals) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Email Alerts --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-bell me-2"></i> Email Alerts</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input type="hidden" name="email_alerts" value="0">
                                        <input type="checkbox" class="form-check-input" name="email_alerts" id="emailAlerts" value="1"
                                               {{ $preferences->email_alerts ? 'checked' : '' }}>
                                        <label class="form-check-label" for="emailAlerts">Send me email alerts for matching jobs</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Alert Frequency</label>
                                            <select name="alert_frequency" class="form-select">
                                                @foreach ($alertFrequencies as $key => $label)
                                                    <option value="{{ $key }}" {{ $preferences->alert_frequency == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Max Alerts Per Day</label>
                                            <input type="number" name="max_alerts_per_day" class="form-control"
                                                   value="{{ old('max_alerts_per_day', $preferences->max_alerts_per_day) }}"
                                                   min="1" max="50">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('account.premium.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-1"></i> Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_scripts')
<script>
document.getElementById('preferencesForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...';
});
</script>
@endsection
