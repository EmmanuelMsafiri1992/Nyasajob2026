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
        // Enable auto currency conversion in localization settings
        DB::table('settings')
            ->where('key', 'localization')
            ->update([
                'value' => DB::raw("JSON_SET(value, '$.auto_currency_conversion', '1')")
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
                'value' => DB::raw("JSON_SET(value, '$.auto_currency_conversion', '0')")
            ]);
    }
};
