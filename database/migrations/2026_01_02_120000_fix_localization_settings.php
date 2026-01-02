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
        // Fix localization settings (ID 19) to have correct values:
        // - default_country_code: MW (Malawi) instead of US
        // - ipinfo_token: Add the token for GeoIP detection
        DB::table('settings')
            ->where('key', 'localization')
            ->update([
                'value' => DB::raw("JSON_SET(value, '$.ipinfo_token', '5a4772cacdb39f', '$.default_country_code', 'MW')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original values if needed
        DB::table('settings')
            ->where('key', 'localization')
            ->update([
                'value' => DB::raw("JSON_SET(value, '$.default_country_code', 'US')")
            ]);
    }
};
