<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = env('PAYPAL_CLIENT_ID', '');
        $this->clientSecret = env('PAYPAL_CLIENT_SECRET', '');
        $this->baseUrl = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Check if PayPal is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Get access token
     */
    public function getAccessToken(): ?string
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
     * Create a checkout order
     */
    public function createOrder(
        float $amount,
        string $currency,
        string $returnUrl,
        string $cancelUrl,
        array $item = [],
        ?string $referenceId = null
    ): ?array {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $referenceId = $referenceId ?? 'order_' . time() . '_' . rand(1000, 9999);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => $referenceId,
                            'description' => $item['description'] ?? 'Purchase',
                            'amount' => [
                                'currency_code' => $currency,
                                'value' => number_format($amount, 2, '.', ''),
                            ],
                        ],
                    ],
                    'payment_source' => [
                        'paypal' => [
                            'experience_context' => [
                                'brand_name' => config('app.name', 'NyasaJob'),
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
     * Capture an order after approval
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
     * Get order details
     */
    public function getOrderDetails(string $orderId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('PayPal get order exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get approval URL from order response
     */
    public static function getApprovalUrl(array $order): ?string
    {
        foreach (data_get($order, 'links', []) as $link) {
            if (in_array(data_get($link, 'rel'), ['approve', 'payer-action'])) {
                return data_get($link, 'href');
            }
        }
        return null;
    }
}
