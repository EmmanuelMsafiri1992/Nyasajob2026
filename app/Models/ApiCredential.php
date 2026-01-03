<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ApiCredential extends Model
{
    use Crud;

    protected $table = 'api_credentials';

    protected $fillable = [
        'provider',
        'name',
        'credentials',
        'api_base_url',
        'is_active',
        'rate_limit_per_minute',
        'daily_quota',
        'requests_today',
        'quota_reset_at',
        'last_used_at',
        'last_verified_at',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rate_limit_per_minute' => 'integer',
        'daily_quota' => 'integer',
        'requests_today' => 'integer',
        'quota_reset_at' => 'datetime',
        'last_used_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'credentials',
    ];

    /**
     * Available API providers and their credential fields
     */
    public const PROVIDERS = [
        'adzuna' => [
            'name' => 'Adzuna',
            'fields' => [
                'app_id' => ['label' => 'App ID', 'type' => 'text', 'required' => true],
                'app_key' => ['label' => 'App Key', 'type' => 'password', 'required' => true],
            ],
            'docs_url' => 'https://developer.adzuna.com/',
        ],
        'jooble' => [
            'name' => 'Jooble',
            'fields' => [
                'api_key' => ['label' => 'API Key', 'type' => 'password', 'required' => true],
            ],
            'docs_url' => 'https://jooble.org/api/about',
        ],
        'careerjet' => [
            'name' => 'Careerjet',
            'fields' => [
                'affiliate_id' => ['label' => 'Affiliate ID', 'type' => 'text', 'required' => true],
            ],
            'docs_url' => 'https://www.careerjet.com/partners/',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get decrypted credentials
     */
    public function getCredentialsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        try {
            return json_decode(Crypt::decryptString($value), true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Encrypt and set credentials
     */
    public function setCredentialsAttribute($value): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->attributes['credentials'] = Crypt::encryptString($value);
    }

    /**
     * Get a specific credential value
     */
    public function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    /**
     * Get provider configuration
     */
    public function getProviderConfig(): ?array
    {
        return self::PROVIDERS[$this->provider] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if credentials are configured
     */
    public function hasCredentials(): bool
    {
        $config = $this->getProviderConfig();
        if (!$config) {
            return false;
        }

        $credentials = $this->credentials;
        foreach ($config['fields'] as $field => $fieldConfig) {
            if ($fieldConfig['required'] && empty($credentials[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if within rate limit
     */
    public function isWithinRateLimit(): bool
    {
        if (!$this->daily_quota) {
            return true;
        }

        // Reset counter if new day
        if ($this->quota_reset_at && $this->quota_reset_at->isPast()) {
            $this->update([
                'requests_today' => 0,
                'quota_reset_at' => now()->endOfDay(),
            ]);
        }

        return $this->requests_today < $this->daily_quota;
    }

    /**
     * Increment request counter
     */
    public function incrementRequestCount(): void
    {
        $this->increment('requests_today');
        $this->update(['last_used_at' => now()]);

        if (!$this->quota_reset_at) {
            $this->update(['quota_reset_at' => now()->endOfDay()]);
        }
    }

    /**
     * Mark as verified (successful API call)
     */
    public function markAsVerified(): void
    {
        $this->update(['last_verified_at' => now()]);
    }

    /**
     * Get credentials for a provider (static helper)
     */
    public static function getForProvider(string $provider): ?self
    {
        return self::active()->forProvider($provider)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL METHODS
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeHtml(): string
    {
        if (!$this->is_active) {
            return '<span class="badge bg-secondary">Inactive</span>';
        }

        if (!$this->hasCredentials()) {
            return '<span class="badge bg-warning">Not Configured</span>';
        }

        if ($this->last_verified_at && $this->last_verified_at->diffInHours() < 24) {
            return '<span class="badge bg-success">Verified</span>';
        }

        return '<span class="badge bg-info">Active</span>';
    }

    public function getProviderNameHtml(): string
    {
        $config = $this->getProviderConfig();
        $name = $config['name'] ?? ucfirst($this->provider);
        $docsUrl = $config['docs_url'] ?? '#';

        return "<a href=\"{$docsUrl}\" target=\"_blank\">{$name}</a>";
    }

    public function getUsageHtml(): string
    {
        if (!$this->daily_quota) {
            return '<span class="text-muted">No limit</span>';
        }

        $percentage = round(($this->requests_today / $this->daily_quota) * 100);
        $color = $percentage < 50 ? 'success' : ($percentage < 80 ? 'warning' : 'danger');

        return "<span class=\"text-{$color}\">{$this->requests_today} / {$this->daily_quota}</span>";
    }

    public function getLastUsedHtml(): string
    {
        if (!$this->last_used_at) {
            return '<span class="text-muted">Never</span>';
        }

        return $this->last_used_at->diffForHumans();
    }

    public function testButton(): string
    {
        $url = admin_url('api-credentials/' . $this->id . '/test');
        return '<a href="' . $url . '" class="btn btn-sm btn-info" title="Test API"><i class="la la-flask"></i> Test</a>';
    }
}
