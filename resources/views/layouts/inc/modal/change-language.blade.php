{{-- Modal Change Language --}}
<?php $supportedLanguages = getSupportedLanguages(); ?>
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
						<div class="col mb-1 cat-list">
							<?php
								$langFlag = (
									config('settings.app.show_languages_flags')
									&& isset($lang, $lang['flag'])
									&& is_string($lang['flag'])
									&& !empty(trim($lang['flag']))
								)
									? '<i class="' . $lang['flag'] . '"></i>&nbsp;'
									: '<i class="bi bi-translate"></i>&nbsp;';
								$isActive = (strtolower($langCode) == strtolower(config('app.locale'))) ? ' fw-bold text-primary' : '';
							?>
							<a href="{{ url('locale/' . $langCode) }}"
							   class="d-block{{ $isActive }}"
							   rel="alternate"
							   hreflang="{{ $langCode }}"
							   title="{{ $lang['name'] }}"
							   data-bs-toggle="tooltip"
							   data-bs-custom-class="modal-tooltip">
								{!! $langFlag !!}{{ str($lang['native'])->limit(21) }}
							</a>
						</div>
					@endforeach

				</div>
			</div>

		</div>
	</div>
</div>
@endif
