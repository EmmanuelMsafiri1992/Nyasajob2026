@php
	$autoAdvertising ??= [];

	// Check if there's actual advertising content
	$hasAdContent = !empty(data_get($autoAdvertising, 'tracking_code_large'));
@endphp
@if (!empty($autoAdvertising) && $hasAdContent)
	<div class="row d-flex justify-content-center m-0 p-0">
		<div class="col-12 text-center m-0 p-0">
			{!! data_get($autoAdvertising, 'tracking_code_large') !!}
		</div>
	</div>
@endif
