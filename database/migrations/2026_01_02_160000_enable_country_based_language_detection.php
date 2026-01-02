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
        // Change language detection from browser-based to country-based
        DB::table('settings')
            ->where('key', 'localization')
            ->update([
                'value' => DB::raw("JSON_SET(value, '$.auto_detect_language', 'from_country')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('key', 'localization')
            ->update([
                'value' => DB::raw("JSON_SET(value, '$.auto_detect_language', 'from_browser')")
            ]);
    }
};
