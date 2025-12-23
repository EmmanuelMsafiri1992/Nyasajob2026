@php
	$post ??= [];
	// Only output structured data for verified posts
	if (!isVerifiedPost($post)) {
		return;
	}

	// Helper function to safely convert dates to ISO8601 format
	$toIso = function($date) {
		if (empty($date)) return null;
		if ($date instanceof \Carbon\Carbon) return $date->toIso8601String();
		return \Carbon\Carbon::parse($date)->toIso8601String();
	};

	// Build the structured data according to Google's JobPosting schema
	$structuredData = [
		'@context' => 'https://schema.org/',
		'@type' => 'JobPosting',
		'title' => data_get($post, 'title'),
		'description' => strip_tags(data_get($post, 'description', '')),
		'datePosted' => $toIso(data_get($post, 'created_at')),
		'validThrough' => data_get($post, 'archived_at')
			? $toIso(data_get($post, 'archived_at'))
			: $toIso(\Carbon\Carbon::parse(data_get($post, 'created_at'))->addMonths(3)),
		'hiringOrganization' => [
			'@type' => 'Organization',
			'name' => data_get($post, 'company_name', config('app.name')),
		],
		'jobLocation' => [
			'@type' => 'Place',
			'address' => [
				'@type' => 'PostalAddress',
				'addressLocality' => data_get($post, 'city.name'),
				'addressRegion' => data_get($post, 'city.subadmin1.name'),
				'addressCountry' => config('country.code'),
			]
		],
	];

	// Add employment type if available
	if (!empty(data_get($post, 'postType.name'))) {
		$employmentType = strtoupper(str_replace(' ', '_', data_get($post, 'postType.name')));
		// Map common job types to schema.org values
		$typeMapping = [
			'FULL_TIME' => 'FULL_TIME',
			'PART_TIME' => 'PART_TIME',
			'CONTRACT' => 'CONTRACTOR',
			'TEMPORARY' => 'TEMPORARY',
			'INTERN' => 'INTERN',
			'VOLUNTEER' => 'VOLUNTEER',
			'PER_DIEM' => 'PER_DIEM',
			'OTHER' => 'OTHER',
		];
		foreach ($typeMapping as $key => $value) {
			if (str_contains($employmentType, $key)) {
				$structuredData['employmentType'] = $value;
				break;
			}
		}
	}

	// Add salary information if available
	if (data_get($post, 'salary_min') > 0 || data_get($post, 'salary_max') > 0) {
		$baseSalary = [
			'@type' => 'MoneyAmount',
			'currency' => config('country.currency'),
		];

		if (data_get($post, 'salary_min') > 0 && data_get($post, 'salary_max') > 0) {
			$baseSalary['value'] = [
				'@type' => 'QuantitativeValue',
				'minValue' => data_get($post, 'salary_min'),
				'maxValue' => data_get($post, 'salary_max'),
			];
		} elseif (data_get($post, 'salary_min') > 0) {
			$baseSalary['value'] = [
				'@type' => 'QuantitativeValue',
				'value' => data_get($post, 'salary_min'),
			];
		} else {
			$baseSalary['value'] = [
				'@type' => 'QuantitativeValue',
				'value' => data_get($post, 'salary_max'),
			];
		}

		// Add unit text (per hour, per month, etc.)
		if (!empty(data_get($post, 'salaryType.name'))) {
			$unitText = strtoupper(str_replace(' ', '_', data_get($post, 'salaryType.name')));
			$baseSalary['value']['unitText'] = $unitText;
		}

		$structuredData['baseSalary'] = $baseSalary;
	}

	// Add company logo if available
	if (!empty(data_get($post, 'company.logo_url'))) {
		$structuredData['hiringOrganization']['logo'] = data_get($post, 'company.logo_url');
	}

	// Add company URL if available
	if (!empty(data_get($post, 'company_id'))) {
		$structuredData['hiringOrganization']['sameAs'] = \App\Helpers\UrlGen::company(null, data_get($post, 'company_id'));
	}

	// Add identifier
	$structuredData['identifier'] = [
		'@type' => 'PropertyValue',
		'name' => config('app.name'),
		'value' => hashId(data_get($post, 'id'), false, false),
	];
@endphp
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
