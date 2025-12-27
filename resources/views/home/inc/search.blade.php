@php
	$sectionOptions = $getSearchFormOp ?? [];
	$sectionData ??= [];

	// Get Search Form Options
	$enableFormAreaCustomization = data_get($sectionOptions, 'enable_extended_form_area') ?? '0';
	$hideTitles = data_get($sectionOptions, 'hide_titles') ?? '0';

	$headerTitle = data_get($sectionOptions, 'title_' . config('app.locale'));
	$headerTitle = (!empty($headerTitle)) ? replaceGlobalPatterns($headerTitle) : null;

	$headerSubTitle = data_get($sectionOptions, 'sub_title_' . config('app.locale'));
	$headerSubTitle = (!empty($headerSubTitle)) ? replaceGlobalPatterns($headerSubTitle) : null;

	$parallax = data_get($sectionOptions, 'parallax') ?? '0';
	$hideForm = data_get($sectionOptions, 'hide_form') ?? '0';
	$displayStatesSearchTip = config('settings.listings_list.display_states_search_tip');

	$hideOnMobile = (data_get($sectionOptions, 'hide_on_mobile') == '1') ? ' hidden-sm' : '';

	// Hero Slider Configuration with Images
	$heroSlides = [
		[
			'title' => 'Find Your Dream Job',
			'subtitle' => 'Discover thousands of job opportunities across Malawi and beyond',
			'image' => 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
			'overlay' => 'rgba(102, 126, 234, 0.7)',
		],
		[
			'title' => 'Build Your Career',
			'subtitle' => 'Connect with top employers and take the next step in your professional journey',
			'image' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
			'overlay' => 'rgba(245, 87, 108, 0.7)',
		],
		[
			'title' => 'Hire Top Talent',
			'subtitle' => 'Post jobs and find the perfect candidates for your organization',
			'image' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
			'overlay' => 'rgba(79, 172, 254, 0.7)',
		],
		[
			'title' => 'Simple, Fast & Efficient',
			'subtitle' => 'The easiest way to find a job from any country',
			'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
			'overlay' => 'rgba(67, 233, 123, 0.7)',
		],
	];
@endphp
@if (isset($firstSection) && !$firstSection)
	<div class="p-0 mt-lg-4 mt-md-3 mt-3"></div>
@endif

@php
	$parallax = ($parallax == '1') ? ' parallax' : '';
@endphp
<div class="intro hero-slider-section{{ $hideOnMobile }}{{ $parallax }}">
		{{-- Hero Slider --}}
		<div class="hero-slider" id="heroSlider">
			@foreach($heroSlides as $index => $slide)
				<div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" style="background-image: url('{{ $slide['image'] }}');">
					<div class="hero-slide-overlay" style="background: {{ $slide['overlay'] }};"></div>
				</div>
			@endforeach
		</div>

		{{-- Slider Navigation Dots --}}
		<div class="hero-slider-dots">
			@foreach($heroSlides as $index => $slide)
				<span class="hero-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></span>
			@endforeach
		</div>

		
		<div class="container text-center hero-content">

			@if ($hideTitles != '1')
				<div class="hero-text-slider">
					@foreach($heroSlides as $index => $slide)
						<div class="hero-text-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
							<h1 class="intro-title animated fadeInDown">
								{{ $slide['title'] }}
							</h1>
							<p class="sub animateme fittext3 animated fadeIn">
								{{ $slide['subtitle'] }}
							</p>
						</div>
					@endforeach
				</div>
			@endif

			@if ($hideForm != '1')
				<form id="search" name="search" action="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}" method="GET">
					<div class="row search-row animated fadeInUp">
						
						<div class="col-md-5 col-sm-12 search-col relative mb-1 mb-xxl-0 mb-xl-0 mb-lg-0 mb-md-0">
							<div class="search-col-inner">
								<i class="fa-solid {{ (config('lang.direction')=='rtl') ? 'fa-angles-left' : 'fa-angles-right' }} icon-append"></i>
								<div class="search-col-input">
									<input class="form-control has-icon" name="q" placeholder="{{ t('what') }}" type="text" value="">
								</div>
							</div>
						</div>
						
						<input type="hidden" id="lSearch" name="l" value="">
						
						<div class="col-md-5 col-sm-12 search-col relative locationicon mb-1 mb-xxl-0 mb-xl-0 mb-lg-0 mb-md-0">
							<div class="search-col-inner">
								<i class="fa-solid fa-location-dot icon-append"></i>
								<div class="search-col-input">
									@if ($displayStatesSearchTip)
										<input class="form-control locinput input-rel searchtag-input has-icon"
											   id="locSearch"
											   name="location"
											   placeholder="{{ t('where') }}"
											   type="text"
											   value=""
											   data-bs-placement="top"
											   data-bs-toggle="tooltipHover"
											   title="{{ t('Enter a city name OR a state name with the prefix', ['prefix' => t('area')]) . t('State Name') }}"
											   spellcheck=false
											   autocomplete="off"
											   autocapitalize="off"
											   tabindex="1"
										>
									@else
										<input class="form-control locinput input-rel searchtag-input has-icon"
											   id="locSearch"
											   name="location"
											   placeholder="{{ t('where') }}"
											   type="text"
											   value=""
											   spellcheck=false
											   autocomplete="off"
											   autocapitalize="off"
											   tabindex="1"
										>
									@endif
								</div>
							</div>
						</div>
						
						<div class="col-md-2 col-sm-12 search-col">
							<div class="search-btn-border bg-primary">
								<button class="btn btn-primary btn-search btn-block btn-gradient">
									<i class="fa-solid fa-magnifying-glass"></i> <strong>{{ t('find') }}</strong>
								</button>
							</div>
						</div>

					</div>
				</form>

				{{-- Browse Listings Button --}}
				<div class="browse-listings-wrapper mt-4">
					<a href="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}" class="btn btn-browse-listings">
						<i class="fa-solid fa-briefcase"></i> Browse All Jobs
					</a>
					<a href="{{ url('companies') }}" class="btn btn-browse-companies">
						<i class="fa-solid fa-building"></i> Browse Companies
					</a>
				</div>
			@endif

		</div>
	</div>

@push('after_scripts_stack')
<script src="{{ url()->asset('dist/public/hero-slider.js') }}?v={{ time() }}"></script>
@endpush
