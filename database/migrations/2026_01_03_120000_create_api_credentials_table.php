<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates table to store API credentials for job feed integrations.
     */
    public function up(): void
    {
        Schema::create('api_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->unique(); // adzuna, jooble, careerjet, etc.
            $table->string('name', 100); // Display name
            $table->text('credentials')->nullable(); // JSON encrypted credentials
            $table->string('api_base_url', 255)->nullable(); // Base API URL if configurable
            $table->boolean('is_active')->default(true);
            $table->integer('rate_limit_per_minute')->nullable(); // API rate limit
            $table->integer('daily_quota')->nullable(); // Daily API quota
            $table->integer('requests_today')->default(0); // Track daily usage
            $table->timestamp('quota_reset_at')->nullable(); // When daily quota resets
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('last_verified_at')->nullable(); // Last successful API call
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_credentials');
    }
};
