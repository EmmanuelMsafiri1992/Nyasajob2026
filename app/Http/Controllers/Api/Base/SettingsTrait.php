<?php
namespace App\Http\Controllers\Api\Base;

use App\Helpers\SystemLocale;
use App\Helpers\Cookie;

trait SettingsTrait
{
	public $cacheExpiration = 3600;     // In seconds (e.g. 60 * 60 for 1h)
	public $cookieExpiration = 3600;    // In seconds (e.g. 60 * 60 for 1h)
	
	/**
	 * Set all the front-end settings
	 */
	public function applyFrontSettings(): void
	{
		// Cache Expiration Time
		$this->cacheExpiration = (int)config('settings.optimization.cache_expiration');
		
		// Cookie Expiration Time
		$this->cookieExpiration = (int)config('settings.other.cookie_expiration');
		
		// Set locale for PHP
		SystemLocale::setLocale(config('lang.locale', 'en_US'));
		
		// CSRF Control
		// CSRF - Some JavaScript frameworks, like Angular, do this automatically for you.
		// It is unlikely that you will need to use this value manually.
		Cookie::set('X-XSRF-TOKEN', csrf_token(), $this->cookieExpiration);
	}
}
