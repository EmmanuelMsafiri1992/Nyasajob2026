@php
	$bcTab ??= [];
	$admin ??= null;
	$city ??= null;

	$adminType = config('country.admin_type', 0);
	$relAdminType = (in_array($adminType, ['1', '2'])) ? $adminType : 1;
	$adminCode = data_get($city, 'subadmin' . $relAdminType . '_code') ?? data_get($admin, 'code') ?? 0;

	// Search base URL
	$searchWithoutQuery = \App\Helpers\UrlGen::searchWithoutQuery();
	$filterBy = request()->query('filterBy');
	if (!empty($filterBy)) {
		$searchWithoutQuery .=  (str_contains($searchWithoutQuery, '?')) ? '&' : '?';
		$searchWithoutQuery .= 'filterBy=' . $filterBy;
	}

	// Build breadcrumb structured data
	$breadcrumbItems = [
		['name' => 'Home', 'url' => url('/')],
		['name' => config('country.name'), 'url' => $searchWithoutQuery],
	];
	if (is_array($bcTab) && count($bcTab) > 0) {
		foreach($bcTab as $value) {
			$breadcrumbItems[] = [
				'name' => strip_tags($value->get('name')),
				'url' => $value->get('url') ?? request()->url(),
			];
		}
	}
@endphp
{{-- BreadcrumbList Structured Data for SEO --}}
<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "BreadcrumbList",
	"itemListElement": [
		@foreach($breadcrumbItems as $index => $item)
		{
			"@type": "ListItem",
			"position": {{ $index + 1 }},
			"name": "{{ $item['name'] }}",
			"item": "{{ $item['url'] }}"
		}@if(!$loop->last),@endif
		@endforeach
	]
}
</script>
<div class="container">
	<nav aria-label="breadcrumb" role="navigation" class="search-breadcrumb">
		<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
			<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="{{ url('/') }}" itemprop="item"><span itemprop="name"><i class="fa-solid fa-house"></i></span></a>
				<meta itemprop="position" content="1" />
			</li>
			<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="{{ $searchWithoutQuery }}" itemprop="item">
					<span itemprop="name">{{ config('country.name') }}</span>
				</a>
				<meta itemprop="position" content="2" />
			</li>
			@if (is_array($bcTab) && count($bcTab) > 0)
				@foreach($bcTab as $key => $value)
					@if ($value->has('position') && $value->get('position') > count($bcTab)+1)
						<li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
							<span itemprop="name">{!! $value->get('name') !!}</span>
							<meta itemprop="position" content="{{ $key + 3 }}" />
							&nbsp;
							@if (!empty($adminCode))
								<a href="#browseLocations" data-bs-toggle="modal" data-admin-code="{{ $adminCode }}" data-city-id="{{ data_get($city, 'id', 0) }}">
									<span class="caret"></span>
								</a>
							@endif
						</li>
					@else
						<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
							<a href="{{ $value->get('url') }}" itemprop="item"><span itemprop="name">{!! $value->get('name') !!}</span></a>
							<meta itemprop="position" content="{{ $key + 3 }}" />
						</li>
					@endif
				@endforeach
			@endif
		</ol>
	</nav>
</div>
