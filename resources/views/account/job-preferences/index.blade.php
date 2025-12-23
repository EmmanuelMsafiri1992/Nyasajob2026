{{--
 * Nyasajob - Job Board Web Application
 * Job Matching & Auto-Apply Preferences
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
							<i class="fas fa-check-circle me-2"></i>{{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				@endif

				@if (session('error'))
					<div class="col-xl-12">
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
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

						{{-- Page Header --}}
						<div class="preferences-header mb-4">
							<div class="d-flex align-items-center justify-content-between flex-wrap">
								<div>
									<h2 class="title-2 mb-2">
										<i class="fas fa-sliders-h text-primary me-2"></i>
										{{ t('Job Matching Preferences') }}
									</h2>
									<p class="text-muted mb-0">Configure your job search preferences in 3 easy steps</p>
								</div>
								@if($preference->exists)
									<div class="mt-3 mt-md-0">
										<a href="{{ route('job-matches.index') }}" class="btn btn-sm btn-outline-primary">
											<i class="fas fa-bullseye me-1"></i> View Matches
										</a>
									</div>
								@endif
							</div>
						</div>

						{{-- Step Progress Indicator --}}
						<div class="step-progress-wrapper mb-4">
							<div class="step-progress">
								<div class="step-item active" data-step="1">
									<div class="step-circle">
										<span class="step-number">1</span>
										<i class="fas fa-check step-check"></i>
									</div>
									<div class="step-label">
										<strong>Job Preferences</strong>
										<small>Categories & Skills</small>
									</div>
								</div>
								<div class="step-line"></div>
								<div class="step-item" data-step="2">
									<div class="step-circle">
										<span class="step-number">2</span>
										<i class="fas fa-check step-check"></i>
									</div>
									<div class="step-label">
										<strong>Auto-Apply</strong>
										<small>Settings & Limits</small>
									</div>
								</div>
								<div class="step-line"></div>
								<div class="step-item" data-step="3">
									<div class="step-circle">
										<span class="step-number">3</span>
										<i class="fas fa-check step-check"></i>
									</div>
									<div class="step-label">
										<strong>Materials</strong>
										<small>Resume & Cover Letter</small>
									</div>
								</div>
							</div>
						</div>

						<form method="POST" action="{{ $preference->exists ? route('job-preferences.update') : route('job-preferences.store') }}" class="preferences-form" id="preferencesForm">
							{!! csrf_field() !!}
							@if($preference->exists)
								@method('PUT')
							@endif

							{{-- Step 1: Job Preferences --}}
							<div class="form-step active" data-step="1">
								<div class="preference-section mb-4">
								<div class="section-header">
									<h5 class="section-title">
										<span class="icon-wrapper bg-primary-soft">
											<i class="fas fa-briefcase text-primary"></i>
										</span>
										Job Preferences
									</h5>
									<p class="section-subtitle">Define what kind of jobs you're looking for</p>
								</div>
								<div class="section-content">

									{{-- Preferred Categories --}}
									<div class="form-group-modern mb-4">
										<label class="form-label-modern">
											Preferred Job Categories <span class="text-danger">*</span>
											<span class="label-hint">Select multiple categories</span>
										</label>
										<select name="preferred_categories[]" class="form-control form-control-modern select2" multiple required>
											@foreach($categories as $category)
												<option value="{{ $category->id }}"
													{{ in_array($category->id, old('preferred_categories', $preference->preferred_categories ?? [])) ? 'selected' : '' }}>
													{{ $category->name }}
												</option>
											@endforeach
										</select>
										@error('preferred_categories')
											<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
										@enderror
									</div>

									{{-- Skills --}}
									<div class="form-group-modern mb-4">
										<label class="form-label-modern">
											Your Skills
											<span class="label-hint">Comma-separated list of your skills</span>
										</label>
										<textarea name="skills" class="form-control form-control-modern" rows="3" placeholder="e.g., PHP, Laravel, MySQL, JavaScript, Vue.js, Project Management, Team Leadership">{{ old('skills', $preference->skills) }}</textarea>
										<small class="form-text text-muted mt-2">
											<i class="fas fa-lightbulb me-1"></i>These skills will be matched against job requirements
										</small>
										@error('skills')
											<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
										@enderror
									</div>

									{{-- Qualifications --}}
									<div class="form-group-modern mb-4">
										<label class="form-label-modern">
											Qualifications & Experience
											<span class="label-hint">Your education and work experience</span>
										</label>
										<textarea name="qualifications" class="form-control form-control-modern" rows="3" placeholder="e.g., Bachelor's in Computer Science, 5+ years experience, AWS Certified Solutions Architect">{{ old('qualifications', $preference->qualifications) }}</textarea>
										@error('qualifications')
											<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
										@enderror
									</div>

									{{-- Salary Range --}}
									<div class="row">
										<div class="col-md-6">
											<div class="form-group-modern mb-4 mb-md-0">
												<label class="form-label-modern">
													Minimum Salary
													<span class="label-hint">Expected minimum</span>
												</label>
												<div class="input-group">
													<span class="input-group-text bg-light"><i class="fas fa-dollar-sign"></i></span>
													<input type="number" name="min_salary" class="form-control form-control-modern" placeholder="e.g., 50000"
														value="{{ old('min_salary', $preference->min_salary) }}" step="1000">
												</div>
												@error('min_salary')
													<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
												@enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group-modern mb-4 mb-md-0">
												<label class="form-label-modern">
													Maximum Salary
													<span class="label-hint">Expected maximum</span>
												</label>
												<div class="input-group">
													<span class="input-group-text bg-light"><i class="fas fa-dollar-sign"></i></span>
													<input type="number" name="max_salary" class="form-control form-control-modern" placeholder="e.g., 80000"
														value="{{ old('max_salary', $preference->max_salary) }}" step="1000">
												</div>
												@error('max_salary')
													<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
												@enderror
											</div>
										</div>
									</div>

									{{-- Remote Work --}}
									<div class="form-group-modern mt-4">
										<div class="custom-checkbox-modern">
											<input class="form-check-input-modern" type="checkbox" name="remote_work" id="remote_work" value="1"
												{{ old('remote_work', $preference->remote_work) ? 'checked' : '' }}>
											<label class="form-check-label-modern" for="remote_work">
												<span class="checkbox-icon"><i class="fas fa-home"></i></span>
												<span class="checkbox-text">
													<strong>I prefer remote/work-from-home opportunities</strong>
													<small>Filter jobs that offer remote work options</small>
												</span>
											</label>
										</div>
									</div>

								</div>
								</div>

								{{-- Step Navigation --}}
								<div class="step-navigation">
									<button type="button" class="btn btn-secondary btn-lg" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
										<i class="fas fa-arrow-left me-2"></i> Previous
									</button>
									<button type="button" class="btn btn-primary btn-lg" id="nextBtn" onclick="changeStep(1)">
										Next <i class="fas fa-arrow-right ms-2"></i>
									</button>
								</div>
							</div>

							{{-- Step 2: Auto-Apply Settings --}}
							<div class="form-step" data-step="2">
								<div class="preference-section mb-4">
								<div class="section-header">
									<h5 class="section-title">
										<span class="icon-wrapper bg-success-soft">
											<i class="fas fa-bolt text-success"></i>
										</span>
										Auto-Apply Settings
									</h5>
									<p class="section-subtitle">Let us apply to jobs automatically on your behalf</p>
								</div>
								<div class="section-content">

									{{-- Auto-Apply Enabled --}}
									<div class="form-group-modern mb-4">
										<div class="custom-switch-modern">
											<input class="form-check-input-modern" type="checkbox" name="auto_apply_enabled" id="auto_apply_enabled" value="1"
												{{ old('auto_apply_enabled', $preference->auto_apply_enabled) ? 'checked' : '' }}>
											<label class="form-check-label-modern" for="auto_apply_enabled">
												<span class="switch-icon"><i class="fas fa-magic"></i></span>
												<span class="switch-text">
													<strong>Enable Auto-Apply</strong>
													<small>Automatically submit applications to matching jobs based on your urgency level</small>
												</span>
											</label>
										</div>
									</div>

									{{-- Urgency Level --}}
									<div class="form-group-modern mb-4">
										<label class="form-label-modern">
											Job Search Urgency <span class="text-danger">*</span>
											<span class="label-hint">How actively are you looking?</span>
										</label>
										<div class="urgency-options">
											@foreach($urgencyLevels as $key => $level)
												<div class="urgency-option">
													<input class="form-check-input" type="radio" name="urgency_level" id="urgency_{{ $key }}"
														value="{{ $key }}" {{ old('urgency_level', $preference->urgency_level ?? 'not_urgent') == $key ? 'checked' : '' }} required>
													<label class="urgency-label" for="urgency_{{ $key }}">
														<div class="urgency-content">
															<strong class="urgency-title">{{ $level['label'] }}</strong>
															<small class="urgency-desc">{{ $level['description'] }}</small>
														</div>
														<div class="urgency-indicator">
															<i class="fas fa-check-circle"></i>
														</div>
													</label>
												</div>
											@endforeach
										</div>
										@error('urgency_level')
											<div class="invalid-feedback d-block mt-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
										@enderror
									</div>

									<div class="row">
										{{-- Daily Application Limit --}}
										<div class="col-md-6">
											<div class="form-group-modern mb-4 mb-md-0">
												<label class="form-label-modern">
													Daily Application Limit
													<span class="label-hint">Max auto-applications per day</span>
												</label>
												<div class="input-group">
													<span class="input-group-text bg-light"><i class="fas fa-calendar-day"></i></span>
													<input type="number" name="max_applications_per_day" class="form-control form-control-modern"
														value="{{ old('max_applications_per_day', $preference->max_applications_per_day ?? 5) }}" min="0" max="50">
													<span class="input-group-text bg-light">per day</span>
												</div>
												<small class="form-text text-muted mt-2">Maximum: 50 applications per day</small>
												@error('max_applications_per_day')
													<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
												@enderror
											</div>
										</div>

										{{-- Minimum Match Percentage --}}
										<div class="col-md-6">
											<div class="form-group-modern">
												<label class="form-label-modern">
													Minimum Match Percentage
													<span class="label-hint">Only high-quality matches</span>
												</label>
												<div class="input-group">
													<span class="input-group-text bg-light"><i class="fas fa-percentage"></i></span>
													<input type="number" name="min_match_percentage" class="form-control form-control-modern"
														value="{{ old('min_match_percentage', $preference->min_match_percentage ?? 60) }}" min="40" max="100">
													<span class="input-group-text bg-light">%</span>
												</div>
												<small class="form-text text-muted mt-2">Minimum: 40% match required</small>
												@error('min_match_percentage')
													<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
												@enderror
											</div>
										</div>
									</div>

								</div>
								</div>

								{{-- Step Navigation --}}
								<div class="step-navigation">
									<button type="button" class="btn btn-secondary btn-lg" onclick="changeStep(-1)">
										<i class="fas fa-arrow-left me-2"></i> Previous
									</button>
									<button type="button" class="btn btn-primary btn-lg" onclick="changeStep(1)">
										Next <i class="fas fa-arrow-right ms-2"></i>
									</button>
								</div>
							</div>

							{{-- Step 3: Application Materials --}}
							<div class="form-step" data-step="3">
								<div class="preference-section mb-4">
								<div class="section-header">
									<h5 class="section-title">
										<span class="icon-wrapper bg-info-soft">
											<i class="fas fa-file-alt text-info"></i>
										</span>
										Application Materials
									</h5>
									<p class="section-subtitle">Choose which resume and cover letter to use</p>
								</div>
								<div class="section-content">

									{{-- Default Resume --}}
									<div class="form-group-modern mb-4">
										<label class="form-label-modern">
											Default Resume
											<span class="label-hint">Used for all auto-applications</span>
										</label>
										<select name="default_resume_id" class="form-control form-control-modern">
											<option value="">-- Select a resume --</option>
											@foreach($resumes as $resume)
												<option value="{{ $resume->id }}"
													{{ old('default_resume_id', $preference->default_resume_id) == $resume->id ? 'selected' : '' }}>
													{{ $resume->name }}
												</option>
											@endforeach
										</select>
										@if($resumes->isEmpty())
											<div class="alert alert-warning border-0 mt-3 py-2">
												<small>
													<i class="fas fa-exclamation-triangle me-1"></i>
													You don't have any resumes yet.
													<a href="{{ url('account/resumes/create') }}" class="alert-link">Create your first resume</a>
												</small>
											</div>
										@endif
										@error('default_resume_id')
											<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
										@enderror
									</div>

									{{-- Cover Letter Template --}}
									<div class="form-group-modern">
										<label class="form-label-modern">
											Cover Letter Template
											<span class="label-hint">Personalize your applications (optional)</span>
										</label>
										<textarea name="cover_letter_template" class="form-control form-control-modern" rows="6"
											placeholder="Dear Hiring Manager,&#10;&#10;I am excited to apply for the {job_title} position at {company}. With my background and skills, I believe I would be a great fit for this role...&#10;&#10;Best regards,&#10;{name}">{{ old('cover_letter_template', $preference->cover_letter_template) }}</textarea>
										<small class="form-text text-muted mt-2">
											<i class="fas fa-tags me-1"></i>
											<strong>Available placeholders:</strong>
											<code>{name}</code>, <code>{job_title}</code>, <code>{company}</code>, <code>{email}</code>
										</small>
										@error('cover_letter_template')
											<div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
										@enderror
									</div>

								</div>
								</div>

								{{-- Step Navigation --}}
								<div class="step-navigation">
									<button type="button" class="btn btn-secondary btn-lg" onclick="changeStep(-1)">
										<i class="fas fa-arrow-left me-2"></i> Previous
									</button>
									<button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
										<i class="fas fa-save me-2"></i>
										{{ $preference->exists ? 'Update Preferences' : 'Save Preferences' }}
									</button>
								</div>
							</div>

						</form>

						{{-- Statistics Section --}}
						@if($preference->exists)
							<div class="statistics-section mt-5">
								<h5 class="mb-4">
									<i class="fas fa-chart-line me-2 text-primary"></i>
									Your Statistics
								</h5>
								<div class="row g-3">
									<div class="col-md-4">
										<div class="stat-card">
											<div class="stat-icon bg-success-soft">
												<i class="fas fa-paper-plane text-success"></i>
											</div>
											<div class="stat-content">
												<h3 class="stat-value">{{ $preference->total_auto_applications ?? 0 }}</h3>
												<p class="stat-label">Auto-Applications Sent</p>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="stat-card">
											<div class="stat-icon bg-info-soft">
												<i class="fas fa-clock text-info"></i>
											</div>
											<div class="stat-content">
												<h3 class="stat-value small-text">{{ $preference->last_application_at ? $preference->last_application_at->diffForHumans() : 'Never' }}</h3>
												<p class="stat-label">Last Application</p>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="stat-card">
											<div class="stat-icon bg-warning-soft">
												<i class="fas fa-fire text-warning"></i>
											</div>
											<div class="stat-content">
												<h3 class="stat-value text-capitalize">{{ str_replace('_', ' ', $preference->urgency_level ?? 'Not Set') }}</h3>
												<p class="stat-label">Current Urgency</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endif

					</div>
				</div>
				<!--/.page-content-->

			</div>
		</div>
	</div>
@endsection

@section('after_styles')
<style>
/* Modern Professional Styling */
.preferences-header {
	padding-bottom: 1.5rem;
	border-bottom: 2px solid #f0f0f0;
}

/* Step Progress Indicator */
.step-progress-wrapper {
	background: #fff;
	border-radius: 12px;
	padding: 2rem;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.step-progress {
	display: flex;
	align-items: center;
	justify-content: space-between;
	max-width: 800px;
	margin: 0 auto;
}

.step-item {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.75rem;
	position: relative;
	flex: 1;
}

.step-circle {
	width: 60px;
	height: 60px;
	border-radius: 50%;
	background: #e9ecef;
	border: 3px solid #e9ecef;
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;
	transition: all 0.3s ease;
	z-index: 2;
}

.step-number {
	font-size: 1.25rem;
	font-weight: 700;
	color: #6c757d;
	transition: all 0.3s ease;
}

.step-check {
	font-size: 1.25rem;
	color: #fff;
	display: none;
}

.step-item.active .step-circle {
	background: #186dde;
	border-color: #186dde;
	box-shadow: 0 0 0 4px rgba(24, 109, 222, 0.2);
}

.step-item.active .step-number {
	color: #fff;
}

.step-item.completed .step-circle {
	background: #2ecc71;
	border-color: #2ecc71;
}

.step-item.completed .step-number {
	display: none;
}

.step-item.completed .step-check {
	display: block;
}

.step-label {
	text-align: center;
}

.step-label strong {
	display: block;
	font-size: 0.875rem;
	font-weight: 600;
	color: #2c3e50;
	margin-bottom: 0.25rem;
}

.step-label small {
	display: block;
	font-size: 0.75rem;
	color: #6c757d;
}

.step-line {
	height: 3px;
	background: #e9ecef;
	flex: 1;
	margin: 0 1rem;
	margin-bottom: 50px;
	position: relative;
	overflow: hidden;
}

.step-line::after {
	content: '';
	position: absolute;
	left: 0;
	top: 0;
	height: 100%;
	width: 0%;
	background: #2ecc71;
	transition: width 0.4s ease;
}

.step-item.completed + .step-line::after {
	width: 100%;
}

/* Form Steps */
.form-step {
	display: none;
}

.form-step.active {
	display: block;
	animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
	from {
		opacity: 0;
		transform: translateY(10px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* Step Navigation */
.step-navigation {
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 1rem;
	padding: 2rem 0 1rem;
	margin-top: 2rem;
	border-top: 2px solid #f0f0f0;
}

.step-navigation .btn {
	min-width: 150px;
}

.title-2 {
	font-size: 1.5rem;
	font-weight: 700;
	color: #2c3e50;
	margin-bottom: 0;
}

/* Section Styling */
.preference-section {
	background: #fff;
	border-radius: 12px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
	overflow: hidden;
	transition: box-shadow 0.3s ease;
}

.preference-section:hover {
	box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.section-header {
	padding: 1.5rem 1.75rem;
	background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
	border-bottom: 1px solid #e9ecef;
}

.section-title {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	font-size: 1.1rem;
	font-weight: 600;
	color: #2c3e50;
	margin-bottom: 0.5rem;
}

.icon-wrapper {
	width: 38px;
	height: 38px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 10px;
	font-size: 1rem;
}

.bg-primary-soft {
	background-color: rgba(24, 109, 222, 0.1);
}

.bg-success-soft {
	background-color: rgba(46, 204, 113, 0.1);
}

.bg-info-soft {
	background-color: rgba(24, 109, 222, 0.1);
}

.bg-warning-soft {
	background-color: rgba(255, 191, 75, 0.1);
}

.section-subtitle {
	margin-bottom: 0;
	color: #6c757d;
	font-size: 0.875rem;
	padding-left: 54px;
}

.section-content {
	padding: 1.75rem;
}

/* Modern Form Groups */
.form-group-modern {
	margin-bottom: 1.5rem;
}

.form-label-modern {
	display: block;
	font-weight: 600;
	color: #2c3e50;
	margin-bottom: 0.75rem;
	font-size: 0.875rem;
}

.label-hint {
	display: block;
	font-weight: 400;
	color: #6c757d;
	font-size: 0.75rem;
	margin-top: 0.25rem;
}

.form-control-modern {
	border: 2px solid #e9ecef;
	border-radius: 8px;
	padding: 0.75rem 1rem;
	font-size: 0.875rem;
	transition: all 0.3s ease;
}

.form-control-modern:focus {
	border-color: #186dde;
	box-shadow: 0 0 0 0.2rem rgba(24, 109, 222, 0.1);
}

.input-group-text {
	border: 2px solid #e9ecef;
	border-radius: 8px;
	font-size: 0.875rem;
}

.input-group .form-control-modern {
	border-left: 0;
	border-top-left-radius: 0;
	border-bottom-left-radius: 0;
}

.input-group .form-control-modern + .input-group-text {
	border-left: 0;
}

.input-group .input-group-text:first-child {
	border-right: 0;
}

/* Custom Checkbox/Switch */
.custom-checkbox-modern,
.custom-switch-modern {
	background: #f8f9fa;
	border: 2px solid #e9ecef;
	border-radius: 10px;
	padding: 1.25rem;
	transition: all 0.3s ease;
	cursor: pointer;
}

.custom-checkbox-modern:hover,
.custom-switch-modern:hover {
	background: #fff;
	border-color: #186dde;
	box-shadow: 0 2px 8px rgba(24, 109, 222, 0.1);
}

.custom-checkbox-modern .form-check-input-modern,
.custom-switch-modern .form-check-input-modern {
	width: 1.5rem;
	height: 1.5rem;
	margin-top: 0;
	cursor: pointer;
}

.form-check-label-modern {
	display: flex;
	align-items: center;
	gap: 1rem;
	cursor: pointer;
	width: 100%;
}

.checkbox-icon,
.switch-icon {
	width: 40px;
	height: 40px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(24, 109, 222, 0.1);
	border-radius: 8px;
	font-size: 1.1rem;
	color: #186dde;
}

.checkbox-text,
.switch-text {
	flex: 1;
}

.checkbox-text strong,
.switch-text strong {
	display: block;
	color: #2c3e50;
	font-size: 0.875rem;
	margin-bottom: 0.25rem;
}

.checkbox-text small,
.switch-text small {
	display: block;
	color: #6c757d;
	font-size: 0.75rem;
}

/* Urgency Options */
.urgency-options {
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
}

.urgency-option {
	position: relative;
}

.urgency-option input[type="radio"] {
	position: absolute;
	opacity: 0;
	pointer-events: none;
}

.urgency-label {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 1.25rem 1.5rem;
	background: #f8f9fa;
	border: 2px solid #e9ecef;
	border-radius: 10px;
	cursor: pointer;
	transition: all 0.3s ease;
}

.urgency-label:hover {
	background: #fff;
	border-color: #186dde;
	box-shadow: 0 2px 8px rgba(24, 109, 222, 0.1);
}

.urgency-option input[type="radio"]:checked + .urgency-label {
	background: linear-gradient(135deg, rgba(24, 109, 222, 0.05) 0%, rgba(24, 109, 222, 0.02) 100%);
	border-color: #186dde;
	box-shadow: 0 2px 12px rgba(24, 109, 222, 0.15);
}

.urgency-content {
	flex: 1;
}

.urgency-title {
	display: block;
	color: #2c3e50;
	font-size: 0.875rem;
	font-weight: 600;
	margin-bottom: 0.25rem;
}

.urgency-desc {
	display: block;
	color: #6c757d;
	font-size: 0.75rem;
}

.urgency-indicator {
	width: 32px;
	height: 32px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background: #e9ecef;
	color: transparent;
	transition: all 0.3s ease;
}

.urgency-option input[type="radio"]:checked + .urgency-label .urgency-indicator {
	background: #186dde;
	color: #fff;
}

/* Form Actions */
.form-actions {
	text-align: center;
	padding: 2rem 0;
	border-top: 2px solid #f0f0f0;
	margin-top: 2rem;
}

/* Statistics Cards */
.statistics-section {
	padding-top: 2rem;
	border-top: 2px solid #f0f0f0;
}

.stat-card {
	background: #fff;
	border: 2px solid #e9ecef;
	border-radius: 12px;
	padding: 1.5rem;
	display: flex;
	align-items: center;
	gap: 1.25rem;
	transition: all 0.3s ease;
}

.stat-card:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
	border-color: #186dde;
}

.stat-icon {
	width: 60px;
	height: 60px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 12px;
	font-size: 1.5rem;
	flex-shrink: 0;
}

.stat-content {
	flex: 1;
}

.stat-value {
	font-size: 1.5rem;
	font-weight: 700;
	color: #2c3e50;
	margin-bottom: 0.25rem;
	line-height: 1.2;
}

.stat-value.small-text {
	font-size: 1rem;
}

.stat-label {
	margin: 0;
	color: #6c757d;
	font-size: 0.75rem;
	font-weight: 500;
}

/* Select2 Customization */
.select2-container--default .select2-selection--multiple {
	border: 2px solid #e9ecef;
	border-radius: 8px;
	padding: 0.5rem;
	min-height: 48px;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
	border-color: #186dde;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
	background-color: #186dde;
	border: none;
	border-radius: 6px;
	padding: 0.375rem 0.75rem;
	color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
	.preferences-header .d-flex {
		flex-direction: column;
		align-items: flex-start !important;
	}

	.section-subtitle {
		padding-left: 0;
		margin-top: 0.5rem;
	}

	.step-progress {
		flex-direction: column;
	}

	.step-line {
		width: 3px;
		height: 40px;
		margin: 0.5rem 0;
		margin-bottom: 0;
	}

	.step-line::after {
		width: 100%;
		height: 0%;
	}

	.step-item.completed + .step-line::after {
		height: 100%;
		width: 100%;
	}

	.step-label strong {
		font-size: 0.8rem;
	}

	.step-label small {
		font-size: 0.7rem;
	}

	.step-circle {
		width: 50px;
		height: 50px;
	}

	.step-number {
		font-size: 1.1rem;
	}

	.step-check {
		font-size: 1.1rem;
	}

	.step-navigation {
		flex-direction: column-reverse;
	}

	.step-navigation .btn {
		width: 100%;
	}
}
</style>
@endsection

@section('after_scripts')
	<script>
		let currentStep = 1;
		const totalSteps = 3;

		$(document).ready(function() {
			// Initialize Select2 for categories
			$('.select2').select2({
				placeholder: 'Select job categories...',
				allowClear: true,
				theme: 'default',
				width: '100%'
			});

			// Initialize step state
			showStep(currentStep);
		});

		function showStep(step) {
			// Hide all steps
			$('.form-step').removeClass('active');

			// Show current step
			$(`.form-step[data-step="${step}"]`).addClass('active');

			// Update progress indicator
			$('.step-item').removeClass('active completed');

			// Mark completed steps
			for (let i = 1; i < step; i++) {
				$(`.step-item[data-step="${i}"]`).addClass('completed');
			}

			// Mark current step as active
			$(`.step-item[data-step="${step}"]`).addClass('active');

			// Update button visibility
			updateButtons();

			// Scroll to top
			$('html, body').animate({ scrollTop: $('.step-progress-wrapper').offset().top - 100 }, 300);
		}

		function updateButtons() {
			// Show/hide Previous button
			if (currentStep === 1) {
				$('#prevBtn').hide();
			} else {
				$('#prevBtn').show();
			}
		}

		function changeStep(direction) {
			// Validate current step before proceeding
			if (direction === 1 && !validateStep(currentStep)) {
				return false;
			}

			// Update current step
			currentStep += direction;

			// Ensure step is within bounds
			if (currentStep < 1) currentStep = 1;
			if (currentStep > totalSteps) currentStep = totalSteps;

			// Show the step
			showStep(currentStep);
		}

		function validateStep(step) {
			let isValid = true;
			let errorMessage = '';

			// Step 1: Job Preferences validation
			if (step === 1) {
				const categories = $('select[name="preferred_categories[]"]').val();
				if (!categories || categories.length === 0) {
					isValid = false;
					errorMessage = 'Please select at least one job category.';
					$('select[name="preferred_categories[]"]').addClass('is-invalid');
				} else {
					$('select[name="preferred_categories[]"]').removeClass('is-invalid');
				}
			}

			// Step 2: Auto-Apply Settings validation
			if (step === 2) {
				const urgencyLevel = $('input[name="urgency_level"]:checked').val();
				if (!urgencyLevel) {
					isValid = false;
					errorMessage = 'Please select your job search urgency level.';
				}
			}

			// Show error if validation failed
			if (!isValid && errorMessage) {
				// Show alert
				if (!$('.validation-alert').length) {
					const alertHtml = `
						<div class="alert alert-danger alert-dismissible fade show validation-alert" role="alert">
							<i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					`;
					$(`.form-step[data-step="${step}"]`).prepend(alertHtml);

					// Auto dismiss after 5 seconds
					setTimeout(() => {
						$('.validation-alert').fadeOut(() => {
							$('.validation-alert').remove();
						});
					}, 5000);
				}
			}

			return isValid;
		}

		// Remove validation alert when user makes changes
		$(document).on('change', 'select[name="preferred_categories[]"], input[name="urgency_level"]', function() {
			$('.validation-alert').fadeOut(() => {
				$('.validation-alert').remove();
			});
		});

		// Form submission validation
		$('#preferencesForm').on('submit', function(e) {
			// Validate all steps before submission
			for (let i = 1; i <= totalSteps; i++) {
				if (!validateStep(i)) {
					e.preventDefault();
					currentStep = i;
					showStep(currentStep);
					return false;
				}
			}
		});
	</script>
@endsection
