<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * The path to the "home" route for your application.
	 *
	 * This is used by Laravel authentication to redirect users after login.
	 *
	 * @var string
	 */
	public const HOME = '/home';
	
	/**
	 * The controller namespace for the application.
	 *
	 * When present, controller route declarations will automatically be prefixed with this namespace.
	 *
	 * @var string|null
	 */
	// protected $namespace = 'App\Http\Controllers';
	
	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->configureRateLimiting();
		
		$this->routes(function () {
			$namespace = 'App\Http\Controllers\Api';
			Route::namespace($namespace)
				->prefix('api')
				->middleware('api')
				->group(base_path('routes/api.php'));
			
			Route::namespace($this->namespace)
				->middleware('web')
				->group(base_path('routes/web.php'));
		});
	}
	
	/**
	 * Configure the rate limiters for the application.
	 *
	 * @return void
	 */
	protected function configureRateLimiting()
	{
		// More Info: https://laravel.com/docs/9.x/routing#rate-limiting
		
		// API rate limit
		RateLimiter::for('api', function (Request $request) {
			// Exception for local and demo environments
			if (isLocalEnv() || isDemoEnv()) {
				return isLocalEnv()
					? Limit::none()
					: (
					$request->user()
						? Limit::perMinute(90)->by($request->user()->id)
						: Limit::perMinute(60)->by($request->ip())
					);
			}
			
			// Limits access to the routes associated with it to:
			// - (For logged users): 1200 requests per minute by user ID
			// - (For guests): 600 requests per minute by IP address
			return $request->user()
				? Limit::perMinute(1200)->by($request->user()->id)
				: Limit::perMinute(600)->by($request->ip());
		});
		
		// Global rate limit (Not used)
		RateLimiter::for('global', function (Request $request) {
			// Limits access to the routes associated with it to:
			// - 1000 requests per minute
			return Limit::perMinute(1000);
		});

		// Web pages rate limit - protects job listings, search, etc.
		RateLimiter::for('web', function (Request $request) {
			// Check if marked as suspicious bot by BotProtection middleware
			$isSuspiciousBot = $request->attributes->get('is_suspicious_bot', false);

			if ($isSuspiciousBot) {
				// Aggressive rate limiting for suspicious bots:
				// 20 requests per minute (1 request every 3 seconds)
				return Limit::perMinute(20)->by($request->ip())
					->response(function (Request $request, array $headers) {
						return response('Too many requests. Please slow down.', 429, $headers);
					});
			}

			// Normal users and legitimate search engines:
			// 120 requests per minute (2 requests per second)
			return $request->user()
				? Limit::perMinute(150)->by($request->user()->id)
				: Limit::perMinute(120)->by($request->ip());
		});

		// Search/filter pages - more restrictive
		RateLimiter::for('search', function (Request $request) {
			$isSuspiciousBot = $request->attributes->get('is_suspicious_bot', false);

			if ($isSuspiciousBot) {
				// Very aggressive for suspicious bots on search:
				// 10 requests per minute
				return Limit::perMinute(10)->by($request->ip())
					->response(function (Request $request, array $headers) {
						return response('Too many search requests. Please slow down.', 429, $headers);
					});
			}

			// Normal users: 60 requests per minute for search
			return $request->user()
				? Limit::perMinute(80)->by($request->user()->id)
				: Limit::perMinute(60)->by($request->ip());
		});
	}
}
