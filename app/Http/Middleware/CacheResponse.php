<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request and add caching headers.
     */
    public function handle(Request $request, Closure $next, int $ttl = 3600): Response
    {
        $response = $next($request);

        // Only cache GET requests with successful responses
        if ($request->isMethod('GET') && $response->isSuccessful() && !auth()->check()) {
            $response->header('Cache-Control', 'public, max-age=' . $ttl);
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
            $response->header('Pragma', 'public');

            // Add ETag for conditional requests
            $content = $response->getContent();
            $etag = md5($content);
            $response->header('ETag', $etag);

            // Check if client has cached version
            if ($request->header('If-None-Match') === $etag) {
                return response('', 304);
            }
        }

        return $response;
    }
}
