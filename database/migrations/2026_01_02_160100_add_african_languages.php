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
        $now = now();

        // Get max lft/rgt for proper tree structure
        $maxRgt = DB::table('languages')->max('rgt') ?? 0;

        $languages = [
            [
                'code' => 'sw',
                'abbr' => 'sw',
                'locale' => 'sw_KE',
                'name' => 'Swahili',
                'native' => 'Kiswahili',
                'direction' => 'ltr',
                'russian_pluralization' => 0,
                'date_format' => 'YYYY-MM-DD',
                'datetime_format' => 'YYYY-MM-DD HH:mm',
                'active' => 1,
                'default' => 0,
                'lft' => $maxRgt + 1,
                'rgt' => $maxRgt + 2,
                'depth' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'ny',
                'abbr' => 'ny',
                'locale' => 'ny_MW',
                'name' => 'Chichewa',
                'native' => 'Chichewa',
                'direction' => 'ltr',
                'russian_pluralization' => 0,
                'date_format' => 'YYYY-MM-DD',
                'datetime_format' => 'YYYY-MM-DD HH:mm',
                'active' => 1,
                'default' => 0,
                'lft' => $maxRgt + 3,
                'rgt' => $maxRgt + 4,
                'depth' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'zu',
                'abbr' => 'zu',
                'locale' => 'zu_ZA',
                'name' => 'Zulu',
                'native' => 'isiZulu',
                'direction' => 'ltr',
                'russian_pluralization' => 0,
                'date_format' => 'YYYY-MM-DD',
                'datetime_format' => 'YYYY-MM-DD HH:mm',
                'active' => 1,
                'default' => 0,
                'lft' => $maxRgt + 5,
                'rgt' => $maxRgt + 6,
                'depth' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($languages as $language) {
            // Only insert if not exists
            $exists = DB::table('languages')->where('code', $language['code'])->exists();
            if (!$exists) {
                DB::table('languages')->insert($language);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('languages')->whereIn('code', ['sw', 'ny', 'zu'])->delete();
    }
};
