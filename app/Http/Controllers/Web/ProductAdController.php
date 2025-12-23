<?php

namespace App\Http\Controllers\Web;

use App\Helpers\ProductAd;
use App\Http\Controllers\Web\Public\FrontController;
use App\Models\ProductAdvertisement;
use Illuminate\Http\Request;

class ProductAdController extends FrontController
{
	/**
	 * Record ad click and redirect to product URL
	 *
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function click($id)
	{
		$ad = ProductAdvertisement::find($id);

		if (!$ad || !$ad->isActive()) {
			return redirect()->to('/');
		}

		// Record the click
		ProductAd::recordClick($id);

		// Redirect to the product URL
		if ($ad->url) {
			return redirect()->away($ad->url);
		}

		return redirect()->to('/');
	}

	/**
	 * Record ad impression
	 *
	 * @param int $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function impression($id)
	{
		$success = ProductAd::recordImpression($id);

		return response()->json(['success' => $success]);
	}
}
