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
        // Fix home section method names
        DB::table('home_sections')
            ->where('method', 'getLatestPosts')
            ->update(['method' => 'getLatestListings']);

        DB::table('home_sections')
            ->where('method', 'getSponsoredPosts')
            ->update(['method' => 'getPremiumListings']);

        // Add default_country_code to localization setting if not exists
        $setting = DB::table('settings')->where('key', 'localization')->first();
        if ($setting) {
            $value = is_string($setting->value) ? json_decode($setting->value, true) : (array)$setting->value;
            if (!isset($value['default_country_code']) || empty($value['default_country_code'])) {
                $value['default_country_code'] = 'MW';
                DB::table('settings')
                    ->where('key', 'localization')
                    ->update(['value' => json_encode($value)]);
            }
        }

        // Unarchive all posts
        DB::table('posts')
            ->whereNotNull('archived_at')
            ->update(['archived_at' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert home section method names
        DB::table('home_sections')
            ->where('method', 'getLatestListings')
            ->update(['method' => 'getLatestPosts']);

        DB::table('home_sections')
            ->where('method', 'getPremiumListings')
            ->update(['method' => 'getSponsoredPosts']);
    }
};
