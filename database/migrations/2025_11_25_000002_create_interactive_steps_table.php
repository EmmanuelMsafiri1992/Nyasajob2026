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
        Schema::create('interactive_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->integer('step_number')->default(1);
            $table->string('title');
            $table->text('instruction');
            $table->enum('action_type', [
                'click',
                'double_click',
                'right_click',
                'type',
                'drag',
                'open_app',
                'close_window',
                'minimize_window',
                'maximize_window',
                'navigate',
                'create_file',
                'create_folder',
                'rename',
                'delete',
                'copy',
                'paste',
                'select'
            ])->default('click');
            $table->string('target_element')->nullable()->comment('CSS selector or element ID');
            $table->json('action_data')->nullable()->comment('Additional action parameters like text to type, coordinates, etc.');
            $table->json('validation_rules')->nullable()->comment('Rules for validating step completion');
            $table->string('hint')->nullable()->comment('Help text shown after failed attempts');
            $table->integer('points')->default(10);
            $table->boolean('is_required')->default(true);
            $table->integer('timeout_seconds')->nullable()->comment('Max time allowed for this step');
            $table->timestamps();

            $table->foreign('lesson_id')
                ->references('id')
                ->on('course_lessons')
                ->onDelete('cascade');

            $table->index(['lesson_id', 'step_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactive_steps');
    }
};
