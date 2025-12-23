<?php

namespace App\Helpers;

use App\Models\ProductAdvertisement;
use Illuminate\Support\Facades\Cache;

class ProductAd
{
	/**
	 * Get advertisements for display based on targeting
	 *
	 * @param string|null $countryCode
	 * @param string|null $stateCode
	 * @param int|null $cityId
	 * @param int $limit
	 * @param bool $firstPositionOnly
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function getAds(
		?string $countryCode = null,
		?string $stateCode = null,
		?int $cityId = null,
		int $limit = 5,
		bool $firstPositionOnly = false
	) {
		// Get current country code from config if not provided
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}

		// Build cache key
		$cacheKey = 'product_ads_' . ($countryCode ?? 'all') . '_' . ($stateCode ?? 'none') . '_' . ($cityId ?? 'none') . '_' . $limit . '_' . ($firstPositionOnly ? 'first' : 'all');

		// Cache for 5 minutes
		return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($countryCode, $stateCode, $cityId, $limit, $firstPositionOnly) {
			$query = ProductAdvertisement::with(['subscription.package', 'targeting'])
				->activeAds();

			// Filter by first position if requested
			if ($firstPositionOnly) {
				$query->whereHas('subscription.package', function ($q) {
					$q->where('first_position', 1);
				});
			}

			// Apply targeting filters (city -> state -> country priority)
			if ($cityId) {
				$query->where(function ($q) use ($cityId, $stateCode, $countryCode) {
					$q->forCity($cityId)
						->orWhere(function ($sq) use ($stateCode, $countryCode) {
							if ($stateCode) {
								$sq->forState($stateCode);
							}
							if ($countryCode) {
								$sq->orWhere(function ($cq) use ($countryCode) {
									$cq->forCountry($countryCode);
								});
							}
						});
				});
			} elseif ($stateCode) {
				$query->where(function ($q) use ($stateCode, $countryCode) {
					$q->forState($stateCode);
					if ($countryCode) {
						$q->orWhere(function ($cq) use ($countryCode) {
							$cq->forCountry($countryCode);
						});
					}
				});
			} elseif ($countryCode) {
				$query->forCountry($countryCode);
			}

			// Check if ads have reached their limits
			$query->where(function ($q) {
				$q->whereDoesntHave('subscription.package', function ($subQ) {
					$subQ->whereNotNull('impressions_limit');
				})
				->orWhereHas('subscription.package', function ($subQ) {
					$subQ->whereRaw('product_advertisements.impressions < ad_packages.impressions_limit');
				});
			});

			// Order by first position first, then randomly
			$query->orderByRaw('
				CASE
					WHEN EXISTS (
						SELECT 1 FROM ad_subscriptions AS subs
						INNER JOIN ad_packages AS pkgs ON subs.ad_package_id = pkgs.id
						WHERE subs.id = product_advertisements.ad_subscription_id
						AND pkgs.first_position = 1
					) THEN 0
					ELSE 1
				END
			')
			->inRandomOrder();

			return $query->limit($limit)->get();
		});
	}

	/**
	 * Record an impression for an advertisement
	 *
	 * @param int $adId
	 * @return bool
	 */
	public static function recordImpression(int $adId): bool
	{
		try {
			$ad = ProductAdvertisement::find($adId);
			if ($ad && $ad->isActive() && !$ad->hasReachedLimits()) {
				$ad->recordImpression();
				return true;
			}
		} catch (\Exception $e) {
			// Log error silently
		}
		return false;
	}

	/**
	 * Record a click for an advertisement
	 *
	 * @param int $adId
	 * @return bool
	 */
	public static function recordClick(int $adId): bool
	{
		try {
			$ad = ProductAdvertisement::find($adId);
			if ($ad && $ad->isActive()) {
				$ad->recordClick();
				return true;
			}
		} catch (\Exception $e) {
			// Log error silently
		}
		return false;
	}

	/**
	 * Clear ads cache
	 *
	 * @return void
	 */
	public static function clearCache(): void
	{
		Cache::flush();
	}

	/**
	 * Render ad HTML
	 *
	 * @param ProductAdvertisement $ad
	 * @param string $size (small, medium, large)
	 * @return string
	 */
	public static function render(ProductAdvertisement $ad, string $size = 'medium'): string
	{
		$sizeClasses = [
			'small' => 'ad-small',
			'medium' => 'ad-medium',
			'large' => 'ad-large',
		];

		$sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
		$clickUrl = url('ad/click/' . $ad->id);
		$targetUrl = $ad->url ?? '#';

		$html = '<div class="product-ad ' . $sizeClass . '" data-ad-id="' . $ad->id . '">';
		$html .= '<a href="' . $clickUrl . '" target="_blank" rel="noopener noreferrer sponsored" onclick="window.open(\'' . e($targetUrl) . '\', \'_blank\'); return true;">';

		if ($ad->image_path) {
			$html .= '<img src="' . e($ad->image_path) . '" alt="' . e($ad->title) . '" class="ad-image">';
		}

		$html .= '<div class="ad-content">';
		$html .= '<h4 class="ad-title">' . e($ad->title) . '</h4>';

		if ($ad->description) {
			$html .= '<p class="ad-description">' . e($ad->description) . '</p>';
		}

		$html .= '</div>';
		$html .= '</a>';
		$html .= '<small class="ad-label">Sponsored</small>';
		$html .= '</div>';

		// Add impression tracking
		$html .= '<script>
			(function() {
				var adId = ' . $ad->id . ';
				var impressionUrl = "' . url('ad/impression/' . $ad->id) . '";
				fetch(impressionUrl, { method: "POST", headers: { "X-CSRF-TOKEN": "' . csrf_token() . '" } });
			})();
		</script>';

		return $html;
	}
}
