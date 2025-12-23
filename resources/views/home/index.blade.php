
@extends('layouts.master')

@section('search')
	@parent
@endsection

@section('content')
	<div class="main-container" id="homepage">
		
		@if (session()->has('flash_notification'))
			@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
			<?php $paddingTopExists = true; ?>
			<div class="container">
				<div class="row">
					<div class="col-12">
						@include('flash::message')
					</div>
				</div>
			</div>
		@endif
		
		@if (!empty($sections))
			@foreach($sections as $section)
				<?php
				 $section ??= [];
				$sectionView = data_get($section, 'view');
				$sectionData = (array)data_get($section, 'data');
				?>
				@if (!empty($sectionView) && view()->exists($sectionView))
					@includeFirst(
						[
							config('larapen.core.customizedViewPath') . $sectionView,
							$sectionView
						],
						[
							'sectionData' => $sectionData,
							'firstSection' => $loop->first
						]
					)
				@endif

				{{-- Ad Placement After First Section --}}
				@if ($loop->first && !empty($topAdvertising))
					<div class="container my-4">
						<div class="row">
							<div class="col-12 text-center">
								{!! data_get($topAdvertising, 'tracking_code_large') !!}
							</div>
						</div>
					</div>
				@endif
			@endforeach
		@endif

	</div>
@endsection

@section('after_scripts')
@endsection
