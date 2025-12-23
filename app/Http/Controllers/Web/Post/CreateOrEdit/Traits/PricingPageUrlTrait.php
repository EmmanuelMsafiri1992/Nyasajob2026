<?php
namespace App\Http\Controllers\Web\Post\CreateOrEdit\Traits;

use App\Helpers\UrlGen;

trait PricingPageUrlTrait
{
	/**
	 * Check if the Package selection is required and Get the Pricing Page URL
	 *
	 * @param $package
	 * @return string|null
	 */
	public function getPricingPage($package): ?string
	{
		$pricingUrl = null;
		
		// Check if the 'Pricing Page' must be started first, and make redirection to it.
		if (config('settings.single.pricing_page_enabled') == '1') {
			if (empty($package)) {
				$pricingUrl = UrlGen::pricing() . '?from=' . request()->path();
			}
		}
		
		return $pricingUrl;
	}
}
