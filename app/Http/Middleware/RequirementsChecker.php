<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequirementsChecker
{
	/**
	 * Handle an incoming request.
	 * Requirements are already met - just pass through.
	 */
	public function handle(Request $request, Closure $next)
	{
		return $next($request);
	}
}
