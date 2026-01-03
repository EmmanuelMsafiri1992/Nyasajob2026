<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds API-based feed formats for Adzuna, Jooble, and Careerjet integrations.
     */
    public function up(): void
    {
        // Modify the feed_format enum to include API formats
        DB::statement("ALTER TABLE job_feed_sources MODIFY COLUMN feed_format ENUM('rss', 'atom', 'json', 'api_adzuna', 'api_jooble', 'api_careerjet') DEFAULT 'rss'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (will fail if any rows use new formats)
        DB::statement("ALTER TABLE job_feed_sources MODIFY COLUMN feed_format ENUM('rss', 'atom', 'json') DEFAULT 'rss'");
    }
};
