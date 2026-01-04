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
    protected ?string $planId;
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
        $this->planId = $this->settings['paypal_plan_id'] ?? null;
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
                'paypal_plan_id' => '',
                'trial_days' => 0,
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
        return !empty($this->settings['enabled']) && !empty($this->planId);
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
     * Create a subscription plan in PayPal (one-time setup)
     */
    public function createPlan(): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            // First, create the product
            $productResponse = Http::withToken($token)
                ->post("{$this->baseUrl}/v1/catalogs/products", [
                    'name' => 'NyasaJob Premium',
                    'description' => 'Premium job seeker subscription with job matching, CV tips, and interview prep',
                    'type' => 'SERVICE',
                    'category' => 'SOFTWARE',
                ]);

            if (!$productResponse->successful()) {
                Log::error('PayPal product creation failed', ['response' => $productResponse->json()]);
                return null;
            }

            $productId = $productResponse->json('id');

            // Then create the plan
            $planResponse = Http::withToken($token)
                ->post("{$this->baseUrl}/v1/billing/plans", [
                    'product_id' => $productId,
                    'name' => 'Job Seeker Premium Monthly',
                    'description' => 'Monthly premium subscription for job seekers - includes job matching, CV tips, and interview preparation',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit' => 'MONTH',
                                'interval_count' => 1,
                            ],
                            'tenure_type' => 'REGULAR',
                            'sequence' => 1,
                            'total_cycles' => 0, // Unlimited
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value' => '5.00',
                                    'currency_code' => 'USD',
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee' => [
                            'value' => '0',
                            'currency_code' => 'USD',
                        ],
                        'setup_fee_failure_action' => 'CANCEL',
                        'payment_failure_threshold' => 3,
                    ],
                ]);

            if ($planResponse->successful()) {
                return $planResponse->json();
            }

            Log::error('PayPal plan creation failed', ['response' => $planResponse->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal plan exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a subscription for a user
     */
    public function createSubscription(User $user, string $returnUrl, string $cancelUrl): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        if (empty($this->planId)) {
            Log::error('PayPal Premium Plan ID not configured');
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v1/billing/subscriptions", [
                    'plan_id' => $this->planId,
                    'subscriber' => [
                        'name' => [
                            'given_name' => $user->name,
                        ],
                        'email_address' => $user->email,
                    ],
                    'application_context' => [
                        'brand_name' => 'NyasaJob',
                        'locale' => 'en-US',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'payment_method' => [
                            'payer_selected' => 'PAYPAL',
                            'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        ],
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal subscription creation failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('PayPal subscription exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get subscription details from PayPal
     */
    public function getSubscriptionDetails(string $subscriptionId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/v1/billing/subscriptions/{$subscriptionId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal get subscription exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId, string $reason = 'User requested cancellation'): bool
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v1/billing/subscriptions/{$subscriptionId}/cancel", [
                    'reason' => $reason,
                ]);

            return $response->status() === 204;
        } catch (\Exception $e) {
            Log::error('PayPal cancel subscription exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Activate subscription after successful PayPal payment
     */
    public function activateSubscription(User $user, string $paypalSubscriptionId): ?PremiumSubscription
    {
        $paypalDetails = $this->getSubscriptionDetails($paypalSubscriptionId);

        if (!$paypalDetails || data_get($paypalDetails, 'status') !== 'ACTIVE') {
            Log::warning('PayPal subscription not active', ['details' => $paypalDetails]);
            return null;
        }

        // Create or update subscription record
        $subscription = PremiumSubscription::updateOrCreate(
            ['user_id' => $user->id, 'status' => PremiumSubscription::STATUS_PENDING],
            [
                'plan_type' => PremiumSubscription::PLAN_JOB_SEEKER_PREMIUM,
                'amount' => 5.00,
                'currency' => 'USD',
                'paypal_subscription_id' => $paypalSubscriptionId,
                'paypal_payer_id' => data_get($paypalDetails, 'subscriber.payer_id'),
                'paypal_payer_email' => data_get($paypalDetails, 'subscriber.email_address'),
                'status' => PremiumSubscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'auto_renew' => true,
                'metadata' => $paypalDetails,
            ]
        );

        // Create job seeker preferences if not exists
        JobSeekerPreference::firstOrCreate(['user_id' => $user->id]);

        return $subscription;
    }

    /**
     * Handle PayPal webhook for subscription events
     */
    public function handleWebhook(array $payload): bool
    {
        $eventType = data_get($payload, 'event_type');
        $resource = data_get($payload, 'resource');

        Log::info('PayPal webhook received', ['event' => $eventType]);

        switch ($eventType) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                return $this->handleSubscriptionActivated($resource);

            case 'BILLING.SUBSCRIPTION.CANCELLED':
                return $this->handleSubscriptionCancelled($resource);

            case 'BILLING.SUBSCRIPTION.EXPIRED':
                return $this->handleSubscriptionExpired($resource);

            case 'BILLING.SUBSCRIPTION.SUSPENDED':
                return $this->handleSubscriptionSuspended($resource);

            case 'PAYMENT.SALE.COMPLETED':
                return $this->handlePaymentCompleted($resource);

            default:
                Log::info('Unhandled PayPal webhook event', ['type' => $eventType]);
                return true;
        }
    }

    protected function handleSubscriptionActivated(array $resource): bool
    {
        $subscriptionId = data_get($resource, 'id');
        $subscription = PremiumSubscription::where('paypal_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->activate();
            return true;
        }

        return false;
    }

    protected function handleSubscriptionCancelled(array $resource): bool
    {
        $subscriptionId = data_get($resource, 'id');
        $subscription = PremiumSubscription::where('paypal_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->cancel('Cancelled via PayPal');
            return true;
        }

        return false;
    }

    protected function handleSubscriptionExpired(array $resource): bool
    {
        $subscriptionId = data_get($resource, 'id');
        $subscription = PremiumSubscription::where('paypal_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->markExpired();
            return true;
        }

        return false;
    }

    protected function handleSubscriptionSuspended(array $resource): bool
    {
        $subscriptionId = data_get($resource, 'id');
        $subscription = PremiumSubscription::where('paypal_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->update(['status' => PremiumSubscription::STATUS_SUSPENDED]);
            return true;
        }

        return false;
    }

    protected function handlePaymentCompleted(array $resource): bool
    {
        $subscriptionId = data_get($resource, 'billing_agreement_id');
        $subscription = PremiumSubscription::where('paypal_subscription_id', $subscriptionId)->first();

        if ($subscription && $subscription->isActive()) {
            $subscription->renew();
            return true;
        }

        return false;
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
            'total_revenue' => PremiumSubscription::where('status', PremiumSubscription::STATUS_ACTIVE)->sum('amount'),
            'monthly_revenue' => PremiumSubscription::where('status', PremiumSubscription::STATUS_ACTIVE)
                ->whereMonth('starts_at', now()->month)
                ->sum('amount'),
        ];
    }
}
