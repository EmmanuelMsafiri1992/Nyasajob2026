<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_feed_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('feed_url', 500);
            $table->string('country_code', 2)->default('MW');
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('post_type_id')->unsigned()->default(1);
            $table->enum('feed_format', ['rss', 'atom', 'json'])->default('rss');
            $table->json('field_mapping')->nullable();
            $table->enum('status', ['active', 'paused', 'failed', 'testing'])->default('testing');
            $table->integer('fetch_interval_minutes')->default(360);
            $table->integer('priority')->default(5);
            $table->integer('max_items_per_fetch')->default(50);
            $table->integer('rate_limit_delay_ms')->default(1000);
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamp('last_successful_at')->nullable();
            $table->integer('consecutive_failures')->default(0);
            $table->integer('total_jobs_fetched')->default(0);
            $table->integer('total_jobs_imported')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('auto_approve')->default(true);
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('country_code');
            $table->index('last_fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_feed_sources');
    }
};
