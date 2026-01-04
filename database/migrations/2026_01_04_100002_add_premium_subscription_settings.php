<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add premium subscription settings to the settings table
        $settingId = DB::table('settings')->insertGetId([
            'key' => 'premium_subscription',
            'name' => 'Premium Subscription',
        ]);

        // Default value for premium subscription settings
        $defaultValue = json_encode([
            'enabled' => '1',
            'price' => '5.00',
            'currency' => 'USD',
            'duration_days' => '30',
            'features' => 'Job Matching, CV Tips, Interview Prep, Email Alerts',
            'terms_required' => '1',
            'non_refundable' => '1',
        ]);

        DB::table('settings')->where('id', $settingId)->update([
            'value' => $defaultValue,
            'description' => 'Premium subscription settings for job seekers (uses PayPal one-time payments)',
            'field' => json_encode([
                [
                    'name' => 'enabled',
                    'label' => 'Enable Premium Subscriptions',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'price',
                    'label' => 'Price (USD)',
                    'type' => 'text',
                    'hint' => 'Price per subscription period',
                ],
                [
                    'name' => 'duration_days',
                    'label' => 'Duration (Days)',
                    'type' => 'text',
                    'hint' => 'How many days does the subscription last (e.g., 30 for monthly)',
                ],
                [
                    'name' => 'features',
                    'label' => 'Features Description',
                    'type' => 'textarea',
                ],
                [
                    'name' => 'terms_required',
                    'label' => 'Require Terms Acceptance',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'non_refundable',
                    'label' => 'Non-Refundable Policy',
                    'type' => 'checkbox',
                ],
            ]),
            'parent_id' => 0,
            'lft' => 50,
            'rgt' => 51,
            'depth' => 0,
            'active' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'premium_subscription')->delete();
    }
};
