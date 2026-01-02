@php
	$countries ??= collect();
	
	// Search parameters
	$queryString = request()->getQueryString();
	$queryString = !empty($queryString) ? '?' . $queryString : '';
	
	$showCountryFlagNextLogo = (config('settings.localization.show_country_flag') == 'in_next_logo');
	
	// Check if the Multi-Countries selection is enabled
	$multiCountryIsEnabled = false;
	$multiCountryLabel = '';
	if ($showCountryFlagNextLogo) {
		if (!empty(config('country.code'))) {
			if ($countries->count() > 1) {
				$multiCountryIsEnabled = true;
				$multiCountryLabel = 'title="' . t('select_country') . '"';
			}
		}
	}
	
	// Country
	$countryName = config('country.name');
	$countryFlag24Url = config('country.flag24_url');
	$countryFlag32Url = config('country.flag32_url');
	
	// Logo
	$logoFactoryUrl = config('larapen.media.logo-factory');
	$logoDarkUrl = config('settings.app.logo_dark_url', $logoFactoryUrl);
	$logoLightUrl = config('settings.app.logo_light_url', $logoFactoryUrl);
	$logoAlt = strtolower(config('settings.app.name'));
	$logoWidth = (int)config('settings.upload.img_resize_logo_width', 454);
	$logoHeight = (int)config('settings.upload.img_resize_logo_height', 80);
	
	// Logo Label
	$logoLabel = '';
	if ($multiCountryIsEnabled) {
		$logoLabel = config('settings.app.name') . (!empty($countryName) ? ' ' . $countryName : '');
	}
	
	// User Menu
	$authUser = auth()->check() ? auth()->user() : null;
	$userMenu ??= collect();

	// Navbar Style
	$navbarStyle = config('settings.style.navbar_style', 'default');
	$navbarClasses = match($navbarStyle) {
		'dark' => 'navbar-dark bg-dark',
		'transparent' => 'navbar-light bg-transparent',
		'gradient' => 'navbar-dark bg-gradient-primary',
		'minimal' => 'navbar-light bg-white border-0',
		default => 'navbar-light bg-light',
	};
@endphp
<div class="header">
	<nav class="navbar fixed-top navbar-site {{ $navbarClasses }} navbar-expand-md" role="navigation">
		<div class="container">
			
			<div class="navbar-identity p-sm-0">
				{{-- Logo --}}
				<a href="{{ url('/') }}" class="navbar-brand logo logo-title">
					<img src="{{ $logoDarkUrl }}"
						 alt="{{ $logoAlt }}"
						 class="main-logo light-logo"
						 data-bs-placement="bottom"
						 data-bs-toggle="tooltip"
						 title="{!! $logoLabel !!}"
						 style="max-width: {{ $logoWidth }}px; max-height: {{ $logoHeight }}px; width:auto;"
					/>
					<img src="{{ $logoLightUrl }}"
					     alt="{{ $logoAlt }}"
					     class="main-logo dark-logo"
					     data-bs-placement="bottom"
					     data-bs-toggle="tooltip"
					     title="{!! $logoLabel !!}"
					     style="max-width: {{ $logoWidth }}px; max-height: {{ $logoHeight }}px; width:auto;"
					/>
				</a>
				{{-- Toggle Nav (Mobile) --}}
				<button class="navbar-toggler -toggler float-end"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target="#navbarsDefault"
						aria-controls="navbarsDefault"
						aria-expanded="false"
						aria-label="Toggle navigation"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30" focusable="false">
						<title>{{ t('Menu') }}</title>
						<path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"></path>
					</svg>
				</button>
				{{-- Country Flag (Mobile) --}}
				@if ($showCountryFlagNextLogo)
					@if ($multiCountryIsEnabled)
						@if (!empty($countryFlag24Url))
							<button class="flag-menu country-flag d-md-none d-sm-block d-none btn btn-default float-end"
							        href="#selectCountry"
							        data-bs-toggle="modal"
							>
								<img src="{{ $countryFlag24Url }}" alt="{{ $countryName }}" style="float: left;">
								<span class="caret d-none"></span>
							</button>
						@endif
					@endif
				@endif
				{{-- Language Selector (Mobile) --}}
				@php
					$supportedLanguages = getSupportedLanguages();
				@endphp
				@if (is_array($supportedLanguages) && count($supportedLanguages) > 1)
					<button class="btn btn-default d-md-none d-block float-end me-2" data-bs-toggle="modal" data-bs-target="#selectLanguage">
						<i class="bi bi-globe2"></i>
					</button>
				@endif
			</div>
			
			<div class="navbar-collapse collapse" id="navbarsDefault">
				<ul class="nav navbar-nav me-md-auto navbar-left">
					{{-- Country Flag --}}
					@if ($showCountryFlagNextLogo)
						@if (!empty($countryFlag32Url))
							<li class="flag-menu country-flag d-md-block d-sm-none d-none nav-item"
							    data-bs-toggle="tooltip"
							    data-bs-placement="{{ (config('lang.direction') == 'rtl') ? 'bottom' : 'right' }}" {!! $multiCountryLabel !!}
							>
								@if ($multiCountryIsEnabled)
									<a class="nav-link p-0" data-bs-toggle="modal" data-bs-target="#selectCountry">
										<img class="flag-icon mt-1" src="{{ $countryFlag32Url }}" alt="{{ $countryName }}">
										<span class="caret d-lg-block d-md-none d-sm-none d-none float-end mt-3 mx-1"></span>
									</a>
								@else
									<a class="p-0" style="cursor: default;">
										<img class="flag-icon" src="{{ $countryFlag32Url }}" alt="{{ $countryName }}">
									</a>
								@endif
							</li>
						@endif
					@endif
				</ul>
				
				<ul class="nav navbar-nav ms-auto navbar-right">
					{{-- Browse Jobs --}}
					@if (config('settings.header.show_browse_jobs_tab') != '0')
						<li class="nav-item">
							<a href="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}" class="nav-link">
								<i class="fa-solid fa-briefcase"></i> Browse Jobs
							</a>
						</li>
					@endif

					{{-- Courses --}}
					@if (config('settings.header.show_courses_tab') != '0')
						<li class="nav-item">
							<a href="{{ url('/courses') }}" class="nav-link">
								<i class="fa-solid fa-graduation-cap"></i> {{ t('Courses') }}
							</a>
						</li>
					@endif

					{{-- Advertise With Us --}}
					@if (config('settings.header.show_advertise_tab') != '0')
						<li class="nav-item">
							<a href="{{ route('advertise.index') }}" class="nav-link">
								<i class="fa-solid fa-bullhorn"></i> {{ t('Advertise') }}
							</a>
						</li>
					@endif

					@if (empty($authUser))
						<li class="nav-item dropdown no-arrow open-on-hover d-md-block d-sm-none d-none">
							<a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
								<i class="fa-solid fa-user"></i>
								<span>{{ t('log_in') }}</span>
								<i class="fa-solid fa-chevron-down"></i>
							</a>
							<ul id="authDropdownMenu" class="dropdown-menu user-menu shadow-sm">
								<li class="dropdown-item">
									<a href="#quickLogin" class="nav-link" data-bs-toggle="modal"><i class="fa-solid fa-user"></i> {{ t('log_in') }}</a>
								</li>
								<li class="dropdown-item">
									<a href="{{ \App\Helpers\UrlGen::register() }}" class="nav-link"><i class="fa-regular fa-user"></i> {{ t('sign_up') }}</a>
								</li>
							</ul>
						</li>
						<li class="nav-item d-md-none d-sm-block d-block">
							<a href="#quickLogin" class="nav-link" data-bs-toggle="modal"><i class="fa-solid fa-user"></i> {{ t('log_in') }}</a>
						</li>
						<li class="nav-item d-md-none d-sm-block d-block">
							<a href="{{ \App\Helpers\UrlGen::register() }}" class="nav-link"><i class="fa-regular fa-user"></i> {{ t('sign_up') }}</a>
						</li>
					@else
						<li class="nav-item dropdown no-arrow open-on-hover">
							<a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
								<i class="fa-solid fa-circle-user"></i>
								<span>{{ $authUser->name }}</span>
								<span class="badge badge-pill badge-important count-threads-with-new-messages d-lg-inline-block d-md-none">0</span>
								<i class="fa-solid fa-chevron-down"></i>
							</a>
							<ul id="userMenuDropdown" class="dropdown-menu user-menu shadow-sm">
								@if ($userMenu->count() > 0)
									@php
										$menuGroup = '';
										$dividerNeeded = false;
									@endphp
									@foreach($userMenu as $key => $value)
										@continue(!$value['inDropdown'])
										@php
											if ($menuGroup != $value['group']) {
												$menuGroup = $value['group'];
												if (!empty($menuGroup) && !$loop->first) {
													$dividerNeeded = true;
												}
											} else {
												$dividerNeeded = false;
											}
										@endphp
										@if ($dividerNeeded)
											<li class="dropdown-divider"></li>
										@endif
										<li class="dropdown-item{!! (isset($value['isActive']) && $value['isActive']) ? ' active' : '' !!}">
											<a href="{{ $value['url'] }}">
												<i class="{{ $value['icon'] }}"></i> {{ $value['name'] }}
												@if (!empty($value['countVar']) && !empty($value['countCustomClass']))
													<span class="badge badge-pill badge-important{{ $value['countCustomClass'] }}">0</span>
												@endif
											</a>
										</li>
									@endforeach
									{{-- My Advertisements Link --}}
									<li class="dropdown-divider"></li>
									<li class="dropdown-item">
										<a href="{{ url('/account/my-ads') }}">
											<i class="fa-solid fa-ad"></i> {{ t('My Advertisements') }}
										</a>
									</li>
								@endif
							</ul>
						</li>
					@endif
					
					@if (empty($authUser) || (!empty($authUser) && in_array($authUser->user_type_id, [1])))
						@if (config('settings.single.pricing_page_enabled') == '2')
							<li class="nav-item pricing">
								<a href="{{ \App\Helpers\UrlGen::pricing() }}" class="nav-link">
									<i class="fa-solid fa-tags"></i> {{ t('pricing_label') }}
								</a>
							</li>
						@endif
					@endif
					
					@php
						[
							$userCanCreateListing,
							$createListingLinkUrl,
							$createListingLinkAttr
						] = getCreateListingLinkInfo();
					@endphp
					@if ($userCanCreateListing)
						<li class="nav-item postadd">
							<a class="btn btn-block btn-border btn-listing"
							   href="{{ $createListingLinkUrl }}"{!! $createListingLinkAttr !!}
							>
								<i class="fa-regular fa-pen-to-square"></i> {{ t('Create Job') }}
							</a>
						</li>
					@endif
					
					@includeFirst([
						config('larapen.core.customizedViewPath') . 'layouts.inc.menu.select-language',
						'layouts.inc.menu.select-language'
					])
				
				</ul>
			</div>
		</div>
	</nav>
</div>
