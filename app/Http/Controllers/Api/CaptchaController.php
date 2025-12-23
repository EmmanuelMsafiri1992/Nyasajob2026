<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @group Captcha
 */
class CaptchaController extends Controller
{
	/**
	 * Get CAPTCHA
	 *
	 * Calling this endpoint is mandatory if the captcha is enabled in the Admin panel.
	 * Return a JSON data with an 'img' item that contains the captcha image to show and a 'key' item that contains the generated key to send for validation.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCaptcha()
	{
		// Call API endpoint
		$endpoint = '/captcha/api/' . config('settings.security.captcha');
		$data = makeApiRequest('get', $endpoint, [], [], false, false);
		
		return $data;
	}
}
