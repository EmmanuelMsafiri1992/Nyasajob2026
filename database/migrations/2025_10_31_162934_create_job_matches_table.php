<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_matches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('post_id')->unsigned()->index(); // Job post
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

            // Matching score and details
            $table->integer('match_percentage')->default(0); // 0-100%
            $table->json('match_details')->nullable(); // What matched (skills, category, salary, etc.)

            // Status tracking
            $table->enum('status', [
                'pending_review',    // Waiting for user to review/approve
                'approved',          // User approved, ready to apply
                'auto_applied',      // Automatically applied
                'manually_applied',  // User applied manually
                'rejected',          // User rejected this match
                'expired'            // Job expired before action taken
            ])->default('pending_review');

            // Application details
            $table->boolean('applied')->default(false);
            $table->timestamp('applied_at')->nullable();
            $table->bigInteger('resume_id')->unsigned()->nullable();
            $table->foreign('resume_id')->references('id')->on('resumes')->onDelete('set null');
            $table->text('cover_letter')->nullable();

            // Notification tracking
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();

            // User interaction
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('user_action_at')->nullable(); // When user took action

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'applied']);
            $table->index('match_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_matches');
    }
};
