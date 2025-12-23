<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\FrontController;
use App\Models\AdPackage;
use App\Models\AdSubscription;
use App\Models\ProductAdvertisement;
use App\Models\AdTargeting;
use App\Models\Country;
use App\Models\SubAdmin1;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class AdvertiseController extends FrontController
{
	/**
	 * Show available ad packages
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		// Cache ad packages for 1 hour (3600 seconds)
		$packages = Cache::remember('ad_packages_active', 3600, function () {
			return AdPackage::where('active', 1)
				->orderBy('recommended', 'DESC')
				->orderBy('price', 'ASC')
				->get();
		});

		// If local currency filtering is enabled and packages found, filter by currency
		if (config('settings.geo_location.local_currency_packages_activation') && $packages->count() > 0) {
			$countryCurrency = config('country.currency');
			if ($countryCurrency) {
				$filteredPackages = $packages->where('currency_code', $countryCurrency);
				// Only use filtered packages if we found some, otherwise show all
				if ($filteredPackages->count() > 0) {
					$packages = $filteredPackages;
				}
			}
		}

		// SEO
		$title = 'Advertise With Us - Product Advertising Packages';
		$description = 'Choose from our advertising packages to promote your products to thousands of job seekers.';

		MetaTag::set('title', $title);
		MetaTag::set('description', $description);

		return view('advertise.index', compact('packages'));
	}

	/**
	 * Show ad creation form
	 *
	 * @param int $packageId
	 * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
	 */
	public function create($packageId)
	{
		// Check if user is logged in
		if (!Auth::check()) {
			return redirect(\App\Helpers\UrlGen::login())
				->with(['message' => 'Please login to create an advertisement.']);
		}

		// Get package (bypass scopes to ensure we find it)
		$package = AdPackage::withoutGlobalScopes()->where('active', 1)->findOrFail($packageId);

		// Get countries for targeting (bypass scopes and select only needed columns)
		$targetCountries = Country::withoutGlobalScopes()->select('code', 'name')->where('active', 1)->orderBy('name', 'ASC')->get();

		// SEO
		$title = 'Create Advertisement - ' . $package->name;
		$description = 'Create your product advertisement';

		MetaTag::set('title', $title);
		MetaTag::set('description', $description);

		return view('advertise.create', compact('package', 'targetCountries'));
	}

	/**
	 * Store ad and proceed to payment
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(Request $request)
	{
		// Validate
		$validated = $request->validate([
			'package_id' => 'required|exists:ad_packages,id',
			'title' => 'required|string|max:200',
			'description' => 'nullable|string|max:1000',
			'url' => 'required|url|max:500',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'targeting_type' => 'required|in:country,state,city',
			'target_countries' => 'required_if:targeting_type,country|array',
			'target_countries.*' => 'exists:countries,code',
			'target_states' => 'required_if:targeting_type,state|array',
			'target_cities' => 'required_if:targeting_type,city|array',
		]);

		// Get package
		$package = AdPackage::withoutGlobalScopes()->findOrFail($request->package_id);

		// Handle image upload
		$imagePath = null;
		if ($request->hasFile('image')) {
			$imagePath = $request->file('image')->store('advertisements', 'public');
		}

		// Create advertisement (pending status)
		$ad = ProductAdvertisement::create([
			'user_id' => Auth::id(),
			'title' => $request->title,
			'description' => $request->description,
			'url' => $request->url,
			'image_path' => $imagePath,
			'status' => 'pending',
			'active' => 0,
		]);

		// Store targeting preferences in session for after payment
		session([
			'ad_targeting' => [
				'ad_id' => $ad->id,
				'targeting_type' => $request->targeting_type,
				'targets' => $request->input('target_' . $request->targeting_type . 's', []),
			]
		]);

		// Redirect to payment
		return redirect()->route('advertise.payment', ['adId' => $ad->id, 'packageId' => $package->id])
			->with(['success' => 'Advertisement created. Please complete payment to activate.']);
	}

	/**
	 * Show payment page
	 *
	 * @param int $adId
	 * @param int $packageId
	 * @return \Illuminate\Contracts\View\View
	 */
	public function payment($adId, $packageId)
	{
		// Check if user is logged in
		if (!Auth::check()) {
			return redirect(\App\Helpers\UrlGen::login());
		}

		$ad = ProductAdvertisement::withoutGlobalScopes()->where('user_id', Auth::id())->findOrFail($adId);
		$package = AdPackage::withoutGlobalScopes()->findOrFail($packageId);

		// Get PayPal payment method
		$paymentMethod = \App\Models\PaymentMethod::where('name', 'paypal')
			->where('active', 1)
			->first();

		if (!$paymentMethod) {
			return redirect()->back()
				->with(['error' => 'PayPal payment is not available at the moment.']);
		}

		$title = 'Complete Payment';
		$description = 'Complete your payment to activate your advertisement';

		MetaTag::set('title', $title);
		MetaTag::set('description', $description);

		return view('advertise.payment', compact('ad', 'package', 'paymentMethod'));
	}

	/**
	 * Process payment callback
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function paymentCallback(Request $request)
	{
		// This will be handled by PayPal plugin
		// For now, simulate success for manual activation by admin

		$adId = $request->input('ad_id');
		$packageId = $request->input('package_id');

		$ad = ProductAdvertisement::withoutGlobalScopes()->where('user_id', Auth::id())->findOrFail($adId);
		$package = AdPackage::withoutGlobalScopes()->findOrFail($packageId);

		// Create subscription record
		$subscription = AdSubscription::create([
			'user_id' => Auth::id(),
			'ad_package_id' => $package->id,
			'payment_method_id' => 1, // Will be updated by payment plugin
			'transaction_id' => $request->input('transaction_id', 'pending'),
			'amount' => $package->price,
			'currency_code' => $package->currency_code,
			'status' => 'pending',
			'starts_at' => now(),
			'expires_at' => now()->addDays($package->duration_days ?? 30),
			'active' => 0, // Admin will activate
		]);

		// Link ad to subscription
		$ad->update([
			'ad_subscription_id' => $subscription->id,
			'starts_at' => $subscription->starts_at,
			'expires_at' => $subscription->expires_at,
		]);

		// Save targeting from session
		$targeting = session('ad_targeting');
		if ($targeting && $targeting['ad_id'] == $adId) {
			foreach ($targeting['targets'] as $targetCode) {
				AdTargeting::create([
					'product_advertisement_id' => $ad->id,
					'target_type' => $targeting['targeting_type'],
					'target_code' => $targetCode,
				]);
			}
			session()->forget('ad_targeting');
		}

		return redirect()->route('advertise.my-ads')
			->with(['success' => 'Payment received! Your ad is pending admin approval.']);
	}

	/**
	 * Show user's advertisements dashboard
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function myAds()
	{
		// Check if user is logged in
		if (!Auth::check()) {
			return redirect(\App\Helpers\UrlGen::login());
		}

		// Get user's ads with subscriptions (bypass scopes to show all statuses)
		$ads = ProductAdvertisement::withoutGlobalScopes()
			->where('user_id', Auth::id())
			->with(['subscription.package', 'targeting'])
			->orderBy('created_at', 'DESC')
			->paginate(10);

		$title = 'My Advertisements';
		$description = 'Manage your product advertisements';

		MetaTag::set('title', $title);
		MetaTag::set('description', $description);

		return view('advertise.my-ads', compact('ads'));
	}

	/**
	 * Pause an advertisement
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function pause($id)
	{
		$ad = ProductAdvertisement::withoutGlobalScopes()->where('user_id', Auth::id())->findOrFail($id);

		$ad->update(['status' => 'paused']);

		return redirect()->back()
			->with(['success' => 'Advertisement paused successfully.']);
	}

	/**
	 * Resume an advertisement
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function resume($id)
	{
		$ad = ProductAdvertisement::withoutGlobalScopes()->where('user_id', Auth::id())->findOrFail($id);

		// Only resume if admin approved
		if ($ad->active) {
			$ad->update(['status' => 'active']);
			return redirect()->back()
				->with(['success' => 'Advertisement resumed successfully.']);
		}

		return redirect()->back()
			->with(['error' => 'Advertisement must be approved by admin first.']);
	}
}
