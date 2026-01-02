<?php
/*
 * JobClass - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 * Author: BeDigit | https://bedigit.com
 */

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Base currency for all packages (default: USD)
     */
    protected string $baseCurrency = 'USD';

    /**
     * Cache duration in seconds (24 hours)
     */
    protected int $cacheDuration = 86400;

    /**
     * API URL for exchange rates (using free frankfurter.app API)
     */
    protected string $apiUrl = 'https://api.frankfurter.app/latest';

    /**
     * Get the singleton instance
     */
    public static function getInstance(): self
    {
        return app(self::class);
    }

    /**
     * Get exchange rate from base currency to target currency
     *
     * @param string $targetCurrency
     * @param string|null $baseCurrency
     * @return float
     */
    public function getRate(string $targetCurrency, ?string $baseCurrency = null): float
    {
        $baseCurrency = $baseCurrency ?? $this->getBaseCurrency();

        // Same currency, no conversion needed
        if (strtoupper($targetCurrency) === strtoupper($baseCurrency)) {
            return 1.0;
        }

        $rates = $this->getAllRates($baseCurrency);

        return $rates[strtoupper($targetCurrency)] ?? 1.0;
    }

    /**
     * Convert amount from base currency to target currency
     *
     * @param float $amount
     * @param string $targetCurrency
     * @param string|null $baseCurrency
     * @return float
     */
    public function convert(float $amount, string $targetCurrency, ?string $baseCurrency = null): float
    {
        $rate = $this->getRate($targetCurrency, $baseCurrency);

        return round($amount * $rate, 2);
    }

    /**
     * Get all exchange rates from base currency
     *
     * @param string|null $baseCurrency
     * @return array
     */
    public function getAllRates(?string $baseCurrency = null): array
    {
        $baseCurrency = strtoupper($baseCurrency ?? $this->getBaseCurrency());
        $cacheKey = 'exchange_rates_' . $baseCurrency;

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($baseCurrency) {
            return $this->fetchRatesFromApi($baseCurrency);
        });
    }

    /**
     * Fetch exchange rates from API
     *
     * @param string $baseCurrency
     * @return array
     */
    protected function fetchRatesFromApi(string $baseCurrency): array
    {
        try {
            // Try frankfurter.app first (free, no API key)
            $response = Http::timeout(10)->get($this->apiUrl, [
                'from' => $baseCurrency,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];

                // Add some currencies that frankfurter might not have
                // Using approximate fixed rates for currencies not in ECB
                $rates = $this->addMissingCurrencies($rates, $baseCurrency);

                return $rates;
            }
        } catch (\Exception $e) {
            Log::warning('ExchangeRateService: Failed to fetch rates from API', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback to cached/default rates
        return $this->getDefaultRates($baseCurrency);
    }

    /**
     * Add missing currencies with approximate rates
     * (Some African currencies not in ECB data)
     *
     * @param array $rates
     * @param string $baseCurrency
     * @return array
     */
    protected function addMissingCurrencies(array $rates, string $baseCurrency): array
    {
        // Approximate rates for currencies not provided by frankfurter
        // These should be updated periodically or fetched from another source
        $additionalRates = [
            'MWK' => 1725.00,  // Malawian Kwacha
            'ZMW' => 27.50,    // Zambian Kwacha
            'TZS' => 2500.00,  // Tanzanian Shilling
            'UGX' => 3750.00,  // Ugandan Shilling
            'KES' => 153.00,   // Kenyan Shilling
            'NGN' => 1550.00,  // Nigerian Naira
            'GHS' => 15.50,    // Ghanaian Cedi
            'RWF' => 1350.00,  // Rwandan Franc
            'ETB' => 125.00,   // Ethiopian Birr
            'BWP' => 13.50,    // Botswana Pula
            'NAD' => 18.50,    // Namibian Dollar
            'SZL' => 18.50,    // Eswatini Lilangeni
            'LSL' => 18.50,    // Lesotho Loti
            'MZN' => 63.50,    // Mozambican Metical
            'AOA' => 825.00,   // Angolan Kwanza
            'XAF' => 610.00,   // Central African CFA
            'XOF' => 610.00,   // West African CFA
        ];

        // If base currency is USD, add these rates directly
        if ($baseCurrency === 'USD') {
            foreach ($additionalRates as $currency => $rate) {
                if (!isset($rates[$currency])) {
                    $rates[$currency] = $rate;
                }
            }
        } else {
            // Convert through USD if base is different
            $usdRate = $rates['USD'] ?? 1.0;
            foreach ($additionalRates as $currency => $rate) {
                if (!isset($rates[$currency])) {
                    // Convert: baseCurrency -> USD -> targetCurrency
                    $rates[$currency] = $rate / $usdRate;
                }
            }
        }

        return $rates;
    }

    /**
     * Get default/fallback rates when API is unavailable
     *
     * @param string $baseCurrency
     * @return array
     */
    protected function getDefaultRates(string $baseCurrency): array
    {
        // Fallback rates (approximate, for when API is down)
        $usdRates = [
            'EUR' => 0.92,
            'GBP' => 0.79,
            'ZAR' => 18.50,
            'MWK' => 1725.00,
            'KES' => 153.00,
            'NGN' => 1550.00,
            'GHS' => 15.50,
            'ZMW' => 27.50,
            'TZS' => 2500.00,
            'UGX' => 3750.00,
            'INR' => 83.00,
            'CAD' => 1.36,
            'AUD' => 1.53,
        ];

        if ($baseCurrency === 'USD') {
            return $usdRates;
        }

        // Convert if base is not USD
        $baseToUsd = 1 / ($usdRates[$baseCurrency] ?? 1.0);
        $rates = [];
        foreach ($usdRates as $currency => $rate) {
            $rates[$currency] = $rate * $baseToUsd;
        }
        $rates['USD'] = $baseToUsd;

        return $rates;
    }

    /**
     * Get the base currency from settings
     *
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return config('settings.localization.base_currency', 'USD');
    }

    /**
     * Clear cached exchange rates
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('exchange_rates_USD');
        Cache::forget('exchange_rates_EUR');
        Cache::forget('exchange_rates_GBP');
    }

    /**
     * Format price with currency symbol
     *
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice(float $amount, string $currencyCode): string
    {
        $currency = \App\Models\Currency::find($currencyCode);

        if (!$currency) {
            return $currencyCode . ' ' . number_format($amount, 2);
        }

        $symbol = $currency->symbol ?? $currencyCode;
        $decimals = $currency->decimal_places ?? 2;
        $decimalSep = $currency->decimal_separator ?? '.';
        $thousandSep = $currency->thousand_separator ?? ',';
        $inLeft = $currency->in_left ?? true;

        $formatted = number_format($amount, $decimals, $decimalSep, $thousandSep);

        if ($inLeft) {
            return $symbol . $formatted;
        }

        return $formatted . ' ' . $symbol;
    }
}
