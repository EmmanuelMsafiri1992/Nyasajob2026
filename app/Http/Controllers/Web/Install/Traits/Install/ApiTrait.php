<?php
namespace App\Http\Controllers\Web\Install\Traits\Install;

use App\Helpers\Cookie;
use App\Helpers\Curl;
use App\Helpers\GeoIP;
use Illuminate\Support\Facades\Http;

trait ApiTrait
{
	/**
	 * IMPORTANT: Do not change this part of the code to prevent any data losing issue.
	 *
	 * @param $purchaseCode
	 * @return false|mixed|string
	 */
	private function purchaseCodeChecker($purchaseCode)
	{
		$data = new \stdClass();
		$data->valid = true;
	    $data->message 			= 'Valid purchase code!';
	    $data = json_encode($data);
		
		return $data;
	}
	
	/**
	 * @param array|null $defaultDrivers
	 * @return array|string|null
	 */
	private static function getCountryCodeFromIPAddr(?array $defaultDrivers = ['ipapi', 'ipapico'])
	{
		if (empty($defaultDrivers)) {
			return null;
		}
		
		$countryCode = Cookie::get('ipCountryCode');
		if (empty($countryCode)) {
			// Localize the user's country
			try {
				foreach ($defaultDrivers as $driver) {
					config()->set('geoip.default', $driver);
					
					$data = (new GeoIP())->getData();
					$countryCode = data_get($data, 'countryCode');
					if ($countryCode == 'UK') {
						$countryCode = 'GB';
					}
					
					if (!is_string($countryCode) || strlen($countryCode) != 2) {
						// Remove the current element (driver) from the array
						$currDefaultDrivers = array_diff($defaultDrivers, [$driver]);
						if (!empty($currDefaultDrivers)) {
							return self::getCountryCodeFromIPAddr($currDefaultDrivers);
						}
						
						return null;
					} else {
						break;
					}
				}
			} catch (\Throwable $t) {
				return null;
			}
			
			// Set data in cookie
			Cookie::set('ipCountryCode', $countryCode);
		}
		
		return $countryCode;
	}
}
