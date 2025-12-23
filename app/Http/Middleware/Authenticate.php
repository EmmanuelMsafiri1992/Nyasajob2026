<?php
namespace App\Http\Middleware;

use App\Helpers\UrlGen;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param $request
	 * @return string|void|null
	 */
	protected function redirectTo($request)
	{
		if (!$request->expectsJson()) {
			if (isAdminPanel()) {
				return admin_uri('login');
			} else {
				return UrlGen::loginPath();
			}
		}
	}
}
