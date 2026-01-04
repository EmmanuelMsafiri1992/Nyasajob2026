<?php

namespace App\Services;

use App\Models\PremiumSubscription;
use App\Models\JobSeekerPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PremiumSubscriptionService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;
    protected array $settings;

    public function __construct()
    {
        // Use existing .env PayPal credentials
        $this->clientId = env('PAYPAL_CLIENT_ID', '');
        $this->clientSecret = env('PAYPAL_CLIENT_SECRET', '');
        $this->baseUrl = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // Get premium settings from database (admin panel configurable)
        $this->settings = $this->getSettings();
    }

    /**
     * Get premium subscription settings from database
     */
    protected function getSettings(): array
    {
        return Cache::remember('premium_subscription_settings', 3600, function () {
            $setting = \App\Models\Setting::where('key', 'premium_subscription')->first();
            if ($setting && $setting->value) {
                return is_array($setting->value) ? $setting->value : json_decode($setting->value, true) ?? [];
            }
            return [
                'enabled' => true,
                'price' => '5.00',
                'currency' => 'USD',
                'duration_days' => 30,
                'terms_required' => true,
                'non_refundable' => true,
            ];
        });
    }

    /**
     * Check if premium subscriptions are enabled
     */
    public function isEnabled(): bool
    {
        return !empty($this->settings['enabled']) && !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Get subscription price
     */
    public function getPrice(): float
    {
        return (float) ($this->settings['price'] ?? 5.00);
    }

    /**
     * Get subscription currency
     */
    public function getCurrency(): string
    {
        return $this->settings['currency'] ?? 'USD';
    }

    /**
     * Get subscription duration in days
     */
    public function getDurationDays(): int
    {
        return (int) ($this->settings['duration_days'] ?? 30);
    }

    /**
     * Get PayPal access token
     */
    protected function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('PayPal token error', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a one-time PayPal order for premium subscription
     */
    public function createOrder(User $user, string $returnUrl, string $cancelUrl): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            Log::error('PayPal: Could not get access token');
            return null;
        }

        $price = number_format($this->getPrice(), 2, '.', '');
        $currency = $this->getCurrency();

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => 'premium_' . $user->id . '_' . time(),
                            'description' => 'NyasaJob Premium - ' . $this->getDurationDays() . ' Days Access',
                            'amount' => [
                                'currency_code' => $currency,
                                'value' => $price,
                            ],
                        ],
                    ],
                    'payment_source' => [
                        'paypal' => [
                            'experience_context' => [
                                'brand_name' => 'NyasaJob',
                                'locale' => 'en-US',
                                'shipping_preference' => 'NO_SHIPPING',
                                'user_action' => 'PAY_NOW',
                                'return_url' => $returnUrl,
                                'cancel_url' => $cancelUrl,
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal order creation failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal order exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Capture payment after user approves
     */
    public function captureOrder(string $orderId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $response = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", []);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal capture failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal capture exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get order details from PayPal
     */
    public function getOrderDetails(string $orderId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal get order exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Activate subscription after successful payment
     */
    public function activateSubscription(User $user, string $orderId, array $captureData): ?PremiumSubscription
    {
        $status = data_get($captureData, 'status');

        if ($status !== 'COMPLETED') {
            Log::warning('PayPal payment not completed', ['status' => $status, 'data' => $captureData]);
            return null;
        }

        // Get payer info
        $payerId = data_get($captureData, 'payer.payer_id');
        $payerEmail = data_get($captureData, 'payer.email_address');
        $transactionId = data_get($captureData, 'purchase_units.0.payments.captures.0.id');

        // Find pending subscription or create new one
        $subscription = PremiumSubscription::where('user_id', $user->id)
            ->where('status', PremiumSubscription::STATUS_PENDING)
            ->first();

        if ($subscription) {
            $subscription->update([
                'paypal_subscription_id' => $orderId, // Using order ID as reference
                'paypal_payer_id' => $payerId,
                'paypal_payer_email' => $payerEmail,
                'status' => PremiumSubscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'expires_at' => now()->addDays($this->getDurationDays()),
                'auto_renew' => false, // One-time payment, no auto-renew
                'metadata' => $captureData,
            ]);
        } else {
            $subscription = PremiumSubscription::create([
                'user_id' => $user->id,
                'plan_type' => PremiumSubscription::PLAN_JOB_SEEKER_PREMIUM,
                'amount' => $this->getPrice(),
                'currency' => $this->getCurrency(),
                'paypal_subscription_id' => $orderId,
                'paypal_payer_id' => $payerId,
                'paypal_payer_email' => $payerEmail,
                'status' => PremiumSubscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'expires_at' => now()->addDays($this->getDurationDays()),
                'auto_renew' => false,
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
                'metadata' => $captureData,
            ]);
        }

        // Create job seeker preferences if not exists
        JobSeekerPreference::firstOrCreate(['user_id' => $user->id]);

        return $subscription;
    }

    /**
     * Check and expire overdue subscriptions
     */
    public function expireOverdueSubscriptions(): int
    {
        $expired = PremiumSubscription::where('status', PremiumSubscription::STATUS_ACTIVE)
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $subscription->markExpired();
        }

        return $expired->count();
    }

    /**
     * Get subscription statistics for admin
     */
    public function getStatistics(): array
    {
        return [
            'total' => PremiumSubscription::count(),
            'active' => PremiumSubscription::active()->count(),
            'cancelled' => PremiumSubscription::where('status', PremiumSubscription::STATUS_CANCELLED)->count(),
            'expired' => PremiumSubscription::where('status', PremiumSubscription::STATUS_EXPIRED)->count(),
            'pending' => PremiumSubscription::where('status', PremiumSubscription::STATUS_PENDING)->count(),
            'expiring_soon' => PremiumSubscription::expiringSoon()->count(),
            'total_revenue' => PremiumSubscription::whereIn('status', [
                PremiumSubscription::STATUS_ACTIVE,
                PremiumSubscription::STATUS_EXPIRED,
                PremiumSubscription::STATUS_CANCELLED
            ])->sum('amount'),
            'monthly_revenue' => PremiumSubscription::whereIn('status', [
                PremiumSubscription::STATUS_ACTIVE,
                PremiumSubscription::STATUS_EXPIRED,
                PremiumSubscription::STATUS_CANCELLED
            ])->whereMonth('created_at', now()->month)->sum('amount'),
        ];
    }
}
