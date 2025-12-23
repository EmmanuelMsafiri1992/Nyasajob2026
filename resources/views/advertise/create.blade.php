@extends('layouts.master')

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					{{-- Package Summary Sidebar --}}
					<div class="card mb-4 sticky-top" style="top: 80px;">
						<div class="card-header bg-primary text-white">
							<h5 class="mb-0">Selected Package</h5>
						</div>
						<div class="card-body">
							<h4>{{ $package->name }}</h4>
							@if($package->short_name)
								<p class="text-muted small">{{ $package->short_name }}</p>
							@endif

							<hr>

							<h3 class="text-primary">{{ $package->currency_code }} {{ number_format($package->price, 2) }}</h3>

							<ul class="list-unstyled mt-3 small">
								@if($package->duration_days)
									<li><i class="fas fa-check text-success"></i> {{ $package->duration_days }} days</li>
								@endif
								@if($package->first_position)
									<li><i class="fas fa-star text-warning"></i> First position</li>
								@endif
								@if($package->impressions_limit)
									<li><i class="fas fa-eye text-info"></i> {{ number_format($package->impressions_limit) }} impressions</li>
								@endif
							</ul>
						</div>
					</div>
				</div>

				<div class="col-md-9">
					<h1 class="title-1 mb-4">Create Your Advertisement</h1>

					@if($errors->any())
						<div class="alert alert-danger alert-dismissible fade show">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							<ul class="mb-0">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form action="{{ route('advertise.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="package_id" value="{{ $package->id }}">

						<div class="card mb-4">
							<div class="card-header">
								<h5 class="mb-0">Advertisement Details</h5>
							</div>
							<div class="card-body">
								{{-- Title --}}
								<div class="mb-3">
									<label for="title" class="form-label">Advertisement Title <span class="text-danger">*</span></label>
									<input type="text"
										   class="form-control @error('title') is-invalid @enderror"
										   id="title"
										   name="title"
										   value="{{ old('title') }}"
										   maxlength="200"
										   required>
									<small class="form-text text-muted">Max 200 characters</small>
									@error('title')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								{{-- Description --}}
								<div class="mb-3">
									<label for="description" class="form-label">Description</label>
									<textarea class="form-control @error('description') is-invalid @enderror"
											  id="description"
											  name="description"
											  rows="4"
											  maxlength="1000">{{ old('description') }}</textarea>
									<small class="form-text text-muted">Max 1000 characters (optional)</small>
									@error('description')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								{{-- Product URL --}}
								<div class="mb-3">
									<label for="url" class="form-label">Product/Landing Page URL <span class="text-danger">*</span></label>
									<input type="url"
										   class="form-control @error('url') is-invalid @enderror"
										   id="url"
										   name="url"
										   value="{{ old('url') }}"
										   placeholder="https://example.com/your-product"
										   required>
									<small class="form-text text-muted">Where users will be redirected when they click your ad</small>
									@error('url')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								{{-- Image Upload --}}
								<div class="mb-3">
									<label for="image" class="form-label">Advertisement Image</label>
									<input type="file"
										   class="form-control @error('image') is-invalid @enderror"
										   id="image"
										   name="image"
										   accept="image/jpeg,image/png,image/jpg,image/gif">
									<small class="form-text text-muted">Recommended: 800x400px, Max 2MB (JPG, PNG, GIF)</small>
									@error('image')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="card mb-4">
							<div class="card-header">
								<h5 class="mb-0">Geographic Targeting</h5>
							</div>
							<div class="card-body">
								{{-- Targeting Type --}}
								<div class="mb-3">
									<label class="form-label">Select Targeting Level <span class="text-danger">*</span></label>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="targeting_type" id="target_country" value="country" checked>
										<label class="form-check-label" for="target_country">
											<strong>Country Level</strong> - Show ads to entire country
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="targeting_type" id="target_state" value="state">
										<label class="form-check-label" for="target_state">
											<strong>State/Region Level</strong> - Target specific states
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="targeting_type" id="target_city" value="city">
										<label class="form-check-label" for="target_city">
											<strong>City Level</strong> - Target specific cities
										</label>
									</div>
								</div>

								{{-- Country Selection --}}
								<div class="mb-3" id="country_selection">
									<label class="form-label">Select Countries <span class="text-danger">*</span></label>
									<select class="form-select" name="target_countries[]" multiple size="10">
										@foreach($targetCountries as $country)
											<option value="{{ $country->code }}">{{ $country->name }}</option>
										@endforeach
									</select>
									<small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select multiple countries</small>
								</div>

								{{-- State Selection (Hidden by default) --}}
								<div class="mb-3 d-none" id="state_selection">
									<label class="form-label">Select States <span class="text-danger">*</span></label>
									<input type="text" class="form-control" placeholder="Select country first to load states">
									<small class="form-text text-muted">First select a country, then choose states</small>
								</div>

								{{-- City Selection (Hidden by default) --}}
								<div class="mb-3 d-none" id="city_selection">
									<label class="form-label">Select Cities <span class="text-danger">*</span></label>
									<input type="text" class="form-control" placeholder="Select country first to load cities">
									<small class="form-text text-muted">First select a country, then choose cities</small>
								</div>
							</div>
						</div>

						<div class="card mb-4">
							<div class="card-body">
								<div class="form-check mb-3">
									<input class="form-check-input" type="checkbox" id="terms" required>
									<label class="form-check-label" for="terms">
										I agree that my advertisement content complies with the site's terms and conditions <span class="text-danger">*</span>
									</label>
								</div>

								<div class="d-grid gap-2">
									<button type="submit" class="btn btn-primary btn-lg">
										<i class="fas fa-arrow-right"></i> Proceed to Payment
									</button>
									<a href="{{ route('advertise.index') }}" class="btn btn-outline-secondary">
										<i class="fas fa-arrow-left"></i> Back to Packages
									</a>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const targetingRadios = document.querySelectorAll('input[name="targeting_type"]');
			const countrySelection = document.getElementById('country_selection');
			const stateSelection = document.getElementById('state_selection');
			const citySelection = document.getElementById('city_selection');

			targetingRadios.forEach(radio => {
				radio.addEventListener('change', function() {
					// Hide all selections
					countrySelection.classList.add('d-none');
					stateSelection.classList.add('d-none');
					citySelection.classList.add('d-none');

					// Show relevant selection
					if (this.value === 'country') {
						countrySelection.classList.remove('d-none');
					} else if (this.value === 'state') {
						stateSelection.classList.remove('d-none');
					} else if (this.value === 'city') {
						citySelection.classList.remove('d-none');
					}
				});
			});
		});
	</script>
@endsection
