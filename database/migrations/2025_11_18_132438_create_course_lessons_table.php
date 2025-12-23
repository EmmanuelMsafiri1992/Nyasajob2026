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
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['video', 'text', 'quiz', 'exercise'])->default('text');
            $table->string('video_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_lessons');
    }
};
