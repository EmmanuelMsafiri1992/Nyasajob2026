<?php
/**
 * Nyasajob - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Base\ApiResponseTrait;
use App\Http\Controllers\Web\Install\Traits\Update\CleanUpTrait;
use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class InstallationChecker
{
	use CleanUpTrait, ApiResponseTrait;
	
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @param $guard
	 * @return \Illuminate\Http\RedirectResponse|mixed
	 */
	public function handle(Request $request, Closure $next, $guard = null)
	{
		if (isFromApi()) {
			return $this->handleApi($request, $next);
		} else {
			return $this->handleWeb($request, $next, $guard);
		}
	}
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
	private function handleApi(Request $request, Closure $next)
	{
		// Since the Admin panel doesn't call the API, skip requests from there to allow admins to log in to into it.
		if (request()->hasHeader('X-WEB-REQUEST-URL')) {
			if (isFromAdminPanel(request()->header('X-WEB-REQUEST-URL'))) {
				return $next($request);
			}
		}
		
		if (!$this->alreadyInstalled()) {
			$message = 'The application is not installed. ';
			$message .= 'Please install it by visiting the URL "' . url('install') . '" from a web browser.';
			
			$data = [
				'success' => false,
				'message' => $message,
				'extra'   => ['error' => ['type' => 'install']],
			];
			
			return $this->apiResponse($data, 401);
		}
		
		if (updateIsAvailable()) {
			$message = 'Your application needs to be upgraded. ';
			$message .= 'To achieve this, visit the URL "' . url('upgrade') . '" in a web browser and follow the steps.';
			
			$data = [
				'success' => false,
				'message' => $message,
				'extra'   => ['error' => ['type' => 'upgrade']],
			];
			
			return $this->apiResponse($data, 401);
		}
		
		return $next($request);
	}
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @param $guard
	 * @return \Illuminate\Http\RedirectResponse|mixed
	 */
	private function handleWeb(Request $request, Closure $next, $guard = null)
	{
		if (request()->segment(1) == 'install') {
			// Check if installation is processing
			$InstallInProgress = (
				!empty(session('databaseImported'))
				|| !empty(session('cronJobs'))
				|| !empty(session('installFinished'))
			);
			
			if ($this->alreadyInstalled() && !$InstallInProgress) {
				return redirect()->to('/');
			}
		} else {
			// Check if an update is available
			if (updateIsAvailable()) {
				if (auth()->check()) {
					$aclTableNames = config('permission.table_names');
					if (isset($aclTableNames['permissions'])) {
						if (Schema::hasTable($aclTableNames['permissions'])) {
							if (auth()->guard($guard)->user()->can(Permission::getStaffPermissions()) && !isDemoDomain()) {
								return redirect()->to(getRawBaseUrl() . '/upgrade');
							}
						}
					}
				} else {
					// Clear all the cache (TMP)
					$this->clearCache();
				}
			}
			
			// Check if the website is installed
			if (!$this->alreadyInstalled()) {
				return redirect()->to(getRawBaseUrl() . '/install');
			}
		}

		return $next($request);
	}

	/**
	 * If application is already installed.
	 *
	 * @return bool|\Illuminate\Http\RedirectResponse
	 */
	private function alreadyInstalled()
	{
		// Check if installation has just finished
		if (session('installFinished') == 1) {
			// Write file
			File::put(storage_path('installed'), '');

			session()->forget('installFinished');
			session()->flush();

			// Redirect to the homepage after installation
			return redirect()->to('/');
		}

		// Check if the app is installed
		return appIsInstalled();
	}
}
