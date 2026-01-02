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
        Schema::create('job_feed_staged_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('feed_source_id')->unsigned();
            $table->string('external_id', 255);
            $table->string('external_url', 500)->nullable();
            $table->string('title', 191);
            $table->text('raw_description');
            $table->text('cleaned_description')->nullable();
            $table->string('company_name', 200)->nullable();
            $table->string('company_logo_url', 500)->nullable();
            $table->string('location_raw', 255)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->bigInteger('city_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->string('tags', 500)->nullable();
            $table->string('contact_email', 191)->nullable();
            $table->string('application_url', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'imported', 'expired'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->bigInteger('imported_post_id')->unsigned()->nullable();
            $table->string('checksum', 64);
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->foreign('feed_source_id')
                ->references('id')
                ->on('job_feed_sources')
                ->onDelete('cascade');

            $table->unique(['feed_source_id', 'external_id']);
            $table->index('status');
            $table->index('checksum');
            $table->index('published_at');
            $table->index('country_code');
            $table->index('imported_post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_feed_staged_items');
    }
};
