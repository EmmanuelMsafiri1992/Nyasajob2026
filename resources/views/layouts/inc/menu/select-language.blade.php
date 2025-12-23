<?php $supportedLanguages = getSupportedLanguages(); ?>
@if (is_array($supportedLanguages) && count($supportedLanguages) > 1)
	{{-- Language Selector (Desktop) --}}
	<li class="nav-item lang-menu d-none d-md-block">
		<a href="#selectLanguage" class="nav-link" data-bs-toggle="modal" data-bs-target="#selectLanguage">
			<span><i class="bi bi-globe2"></i></span>
		</a>
	</li>
@endif