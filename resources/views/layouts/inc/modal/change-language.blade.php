{{-- Modal Change Language --}}
@php
	$supportedLanguages = getSupportedLanguages();
	$showLanguagesFlags = config('settings.localization.show_languages_flags');
@endphp
@if (is_array($supportedLanguages) && count($supportedLanguages) > 1)
<div class="modal fade modalHasList" id="selectLanguage" tabindex="-1" role="dialog" aria-labelledby="selectLanguageLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">

			<div class="modal-header px-3">
				<h4 class="modal-title uppercase fw-bold" id="selectLanguageLabel">
					<i class="bi bi-globe2"></i> {{ t('Select a Language') }}
				</h4>

				<button type="button" class="close" data-bs-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2">

					@foreach($supportedLanguages as $langCode => $lang)
						@php
							$langFlag = $lang['flag'] ?? '';
							$langFlagCountry = str_replace('flag-icon-', '', $langFlag);
							$isActive = (strtolower($langCode) == strtolower(config('app.locale')));
							$activeClass = $isActive ? ' fw-bold text-primary' : '';
							$langNative = $lang['native'] ?? $lang['name'] ?? $langCode;
							$langNameLimited = str($langNative)->limit(21)->toString();
						@endphp
						<div class="col mb-2 cat-list">
							@if ($showLanguagesFlags && !empty($langFlagCountry))
								<img src="{{ getCountryFlagUrl($langFlagCountry) }}"
								     alt="{{ $langNative }}"
								     style="margin-bottom: 4px; margin-right: 5px; width: 20px; height: 15px; object-fit: cover;">
							@else
								<i class="bi bi-translate" style="margin-right: 5px;"></i>
							@endif
							<a href="{{ url('locale/' . $langCode) }}"
							   class="{{ $activeClass }}"
							   rel="alternate"
							   hreflang="{{ $langCode }}"
							   title="{{ $lang['name'] ?? $langNative }}"
							   data-bs-toggle="tooltip"
							   data-bs-custom-class="modal-tooltip">
								{{ $langNameLimited }}
								@if ($isActive)
									<i class="fa-solid fa-check text-success ms-1"></i>
								@endif
							</a>
						</div>
					@endforeach

				</div>
			</div>

		</div>
	</div>
</div>
@endif
