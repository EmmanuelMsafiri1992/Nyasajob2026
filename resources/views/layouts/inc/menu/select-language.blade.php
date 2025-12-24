@php
	$countries ??= collect();
	$showCountryFlagNextLang = (config('settings.localization.show_country_flag') == 'in_next_lang');

	$showCountrySpokenLang = config('settings.localization.show_country_spoken_languages');
	$showCountrySpokenLang = str_starts_with($showCountrySpokenLang, 'active');
	$supportedLanguages = $showCountrySpokenLang ? getCountrySpokenLanguages() : getSupportedLanguages();

	$supportedLanguagesExist = (is_array($supportedLanguages) && count($supportedLanguages) > 1);
	$isLangOrCountryCanBeSelected = ($supportedLanguagesExist || $showCountryFlagNextLang);

	// Check if the Multi-Countries selection is enabled
	$multiCountryIsEnabled = false;
	$multiCountryLabel = '';
	if ($showCountryFlagNextLang) {
		if (!empty(config('country.code'))) {
			if ($countries->count() > 1) {
				$multiCountryIsEnabled = true;
			}
		}
	}

	$countryName = config('country.name');
	$countryFlag32Url = config('country.flag32_url');

	$countryFlagImg = $showCountryFlagNextLang
		? '<img class="flag-icon" src="' . $countryFlag32Url . '" alt="' . $countryName . '">'
		: null;
@endphp
@if ($isLangOrCountryCanBeSelected)
	{{-- Language Selector - Opens Modal --}}
	<li class="nav-item lang-menu d-md-block d-none">
		<a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#selectLanguage" title="{{ t('Select a Language') }}">
			@if (!empty($countryFlagImg))
				<span>
					{!! $countryFlagImg !!}
					{{ strtoupper(config('app.locale')) }}
				</span>
			@else
				<span><i class="bi bi-globe2"></i> {{ strtoupper(config('app.locale')) }}</span>
			@endif
		</a>
	</li>

	{{-- Country Selector Button (if enabled next to language) --}}
	@if ($showCountryFlagNextLang && $multiCountryIsEnabled)
		<li class="nav-item d-md-block d-none">
			<a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#selectCountry" title="{{ t('select_country') }}">
				<i class="fa-regular fa-map"></i>
			</a>
		</li>
	@endif
@endif
