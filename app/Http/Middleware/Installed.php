<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Installed
{
	/**
	 * Handle an incoming request.
	 * App is always installed - no checks needed.
	 */
	public function handle(Request $request, Closure $next, $guard = null)
	{
		return $next($request);
	}
}
