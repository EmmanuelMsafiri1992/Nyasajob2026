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
        // Check if header setting already exists
        $exists = DB::table('settings')->where('key', 'header')->exists();

        if (!$exists) {
            // Get the max lft/rgt values to place the new setting at the end
            $maxRgt = DB::table('settings')->max('rgt') ?? 0;

            DB::table('settings')->insert([
                'key'         => 'header',
                'name'        => 'Header',
                'field'       => null,
                'value'       => null,
                'description' => 'Pages Header Options',
                'parent_id'   => null,
                'lft'         => $maxRgt + 1,
                'rgt'         => $maxRgt + 2,
                'depth'       => 1,
                'active'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'header')->delete();
    }
};
