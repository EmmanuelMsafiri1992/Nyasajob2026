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
        Schema::create('interactive_step_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('step_id');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('points_earned')->default(0);
            $table->json('user_actions')->nullable()->comment('Log of user actions for this step');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('course_lessons')
                ->onDelete('cascade');

            $table->foreign('step_id')
                ->references('id')
                ->on('interactive_steps')
                ->onDelete('cascade');

            $table->unique(['user_id', 'lesson_id', 'step_id'], 'user_lesson_step_unique');
            $table->index(['user_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactive_step_progress');
    }
};
