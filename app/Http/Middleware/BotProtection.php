<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BotProtection
{
    /**
     * Legitimate search engine bots that should be allowed
     */
    protected $allowedBots = [
        'Googlebot',
        'Googlebot-Image',
        'Googlebot-News',
        'Googlebot-Video',
        'APIs-Google',
        'AdsBot-Google',
        'Mediapartners-Google',
        'bingbot',
        'Bingbot',
        'msnbot',
        'Slurp',              // Yahoo
        'DuckDuckBot',        // DuckDuckGo
        'Baiduspider',        // Baidu
        'YandexBot',          // Yandex
        'Sogou',              // Sogou
        'Exabot',             // Exalead
        'facebot',            // Facebook
        'ia_archiver',        // Alexa
        'LinkedInBot',        // LinkedIn
        'Twitterbot',         // Twitter
        'PinterestBot',       // Pinterest
        'WhatsApp',           // WhatsApp
    ];

    /**
     * Suspicious bot patterns that should be rate-limited aggressively
     */
    protected $suspiciousBots = [
        'scrapy',
        'crawler',
        'spider',
        'scraper',
        'bot',
        'wget',
        'curl',
        'python',
        'java',
        'axios',
        'node',
        'phantom',
        'selenium',
        'headless',
        'httpclient',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userAgent = strtolower($request->header('User-Agent', ''));

        // Allow empty user agents (some legitimate clients)
        if (empty($userAgent)) {
            return $next($request);
        }

        // Check if it's a legitimate search engine bot
        if ($this->isAllowedBot($userAgent)) {
            // Allow search engines with minimal restrictions
            return $next($request);
        }

        // Check for suspicious bot patterns
        if ($this->isSuspiciousBot($userAgent)) {
            // Add a marker to the request for aggressive rate limiting
            $request->attributes->set('is_suspicious_bot', true);
        }

        // Check for missing common browser headers (likely a bot)
        if ($this->isMissingBrowserHeaders($request)) {
            $request->attributes->set('is_suspicious_bot', true);
        }

        return $next($request);
    }

    /**
     * Check if the user agent is a legitimate bot
     *
     * @param string $userAgent
     * @return bool
     */
    protected function isAllowedBot($userAgent)
    {
        foreach ($this->allowedBots as $bot) {
            if (stripos($userAgent, strtolower($bot)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user agent matches suspicious bot patterns
     *
     * @param string $userAgent
     * @return bool
     */
    protected function isSuspiciousBot($userAgent)
    {
        // Don't flag as suspicious if it's an allowed bot
        if ($this->isAllowedBot($userAgent)) {
            return false;
        }

        foreach ($this->suspiciousBots as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if request is missing common browser headers
     *
     * @param Request $request
     * @return bool
     */
    protected function isMissingBrowserHeaders(Request $request)
    {
        // Don't check for allowed bots
        $userAgent = strtolower($request->header('User-Agent', ''));
        if ($this->isAllowedBot($userAgent)) {
            return false;
        }

        // Check for Accept-Language header (most browsers send this)
        $hasAcceptLanguage = $request->hasHeader('Accept-Language');

        // Check for Accept-Encoding header (most browsers send this)
        $hasAcceptEncoding = $request->hasHeader('Accept-Encoding');

        // If missing both common headers, likely a simple bot
        if (!$hasAcceptLanguage && !$hasAcceptEncoding) {
            return true;
        }

        return false;
    }
}
