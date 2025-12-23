<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature  The feature to check access for
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $feature = null)
    {
        $user = $request->user();

        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this feature.');
        }

        // If no feature is specified, just check if user has any active subscription
        if (!$feature) {
            if (!$user->hasActiveSubscription()) {
                return redirect()->route('subscriptions.index')
                    ->with('error', 'This feature requires an active subscription. Please subscribe to continue.');
            }
            return $next($request);
        }

        // Check if user has access to specific feature
        if (!$user->hasFeature($feature)) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'This feature is not available in your current plan. Please upgrade your subscription.');
        }

        return $next($request);
    }
}
