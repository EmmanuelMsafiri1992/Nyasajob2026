<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Allows country_code to be null for global/remote job feeds
     */
    public function up(): void
    {
        Schema::table('job_feed_sources', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_feed_sources', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable(false)->default('MW')->change();
        });
    }
};
