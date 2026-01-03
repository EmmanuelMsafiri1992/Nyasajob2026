<?php

namespace App\Services\RssFeed;

use App\Models\ApiCredential;
use Illuminate\Support\Facades\Cache;

class ApiCredentialsService
{
    protected array $cachedCredentials = [];

    /**
     * Get credentials for a provider.
     * Checks database first, falls back to config.
     */
    public function getCredentials(string $provider): ?array
    {
        // Check local cache first
        if (isset($this->cachedCredentials[$provider])) {
            return $this->cachedCredentials[$provider];
        }

        // Try database (with short cache)
        $cacheKey = "api_credentials.{$provider}";
        $credentials = Cache::remember($cacheKey, 300, function () use ($provider) {
            $credential = ApiCredential::active()->forProvider($provider)->first();

            if ($credential && $credential->hasCredentials()) {
                return [
                    'model' => $credential->id,
                    'credentials' => $credential->credentials,
                    'base_url' => $credential->api_base_url,
                    'rate_limit_per_minute' => $credential->rate_limit_per_minute,
                    'daily_quota' => $credential->daily_quota,
                ];
            }

            return null;
        });

        // Fall back to config if no database credentials
        if (!$credentials) {
            $credentials = $this->getConfigCredentials($provider);
        }

        $this->cachedCredentials[$provider] = $credentials;

        return $credentials;
    }

    /**
     * Get credentials from config as fallback.
     */
    protected function getConfigCredentials(string $provider): ?array
    {
        $config = config("services.{$provider}");

        if (!$config) {
            return null;
        }

        switch ($provider) {
            case 'adzuna':
                if (empty($config['app_id']) || empty($config['app_key'])) {
                    return null;
                }
                return [
                    'model' => null,
                    'credentials' => [
                        'app_id' => $config['app_id'],
                        'app_key' => $config['app_key'],
                    ],
                    'base_url' => $config['base_url'] ?? null,
                ];

            case 'jooble':
                if (empty($config['api_key'])) {
                    return null;
                }
                return [
                    'model' => null,
                    'credentials' => [
                        'api_key' => $config['api_key'],
                    ],
                    'base_url' => $config['base_url'] ?? null,
                ];

            case 'careerjet':
                if (empty($config['affiliate_id'])) {
                    return null;
                }
                return [
                    'model' => null,
                    'credentials' => [
                        'affiliate_id' => $config['affiliate_id'],
                    ],
                    'base_url' => $config['base_url'] ?? null,
                ];

            default:
                return null;
        }
    }

    /**
     * Get a specific credential value for a provider.
     */
    public function get(string $provider, string $key, $default = null)
    {
        $credentials = $this->getCredentials($provider);

        if (!$credentials) {
            return $default;
        }

        return $credentials['credentials'][$key] ?? $default;
    }

    /**
     * Check if a provider has valid credentials.
     */
    public function hasCredentials(string $provider): bool
    {
        $credentials = $this->getCredentials($provider);
        return $credentials !== null && !empty($credentials['credentials']);
    }

    /**
     * Track API usage for a provider.
     */
    public function trackUsage(string $provider): void
    {
        $credentials = $this->getCredentials($provider);

        if ($credentials && $credentials['model']) {
            $credential = ApiCredential::find($credentials['model']);
            if ($credential) {
                $credential->incrementRequestCount();
            }
        }
    }

    /**
     * Mark provider as verified (successful API call).
     */
    public function markVerified(string $provider): void
    {
        $credentials = $this->getCredentials($provider);

        if ($credentials && $credentials['model']) {
            $credential = ApiCredential::find($credentials['model']);
            if ($credential) {
                $credential->markAsVerified();
            }
        }

        // Clear cache to refresh
        Cache::forget("api_credentials.{$provider}");
    }

    /**
     * Clear credentials cache.
     */
    public function clearCache(?string $provider = null): void
    {
        if ($provider) {
            Cache::forget("api_credentials.{$provider}");
            unset($this->cachedCredentials[$provider]);
        } else {
            foreach (array_keys(ApiCredential::PROVIDERS) as $p) {
                Cache::forget("api_credentials.{$p}");
            }
            $this->cachedCredentials = [];
        }
    }
}
