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
        Schema::create('job_feed_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('feed_source_id')->unsigned();
            $table->enum('status', ['success', 'partial', 'failed'])->default('success');
            $table->integer('items_found')->default(0);
            $table->integer('items_new')->default(0);
            $table->integer('items_duplicate')->default(0);
            $table->integer('items_failed')->default(0);
            $table->integer('duration_ms')->default(0);
            $table->text('error_message')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->foreign('feed_source_id')
                ->references('id')
                ->on('job_feed_sources')
                ->onDelete('cascade');

            $table->index(['feed_source_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_feed_logs');
    }
};
