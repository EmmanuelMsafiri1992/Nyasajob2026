<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->json('options'); // Array of options with scores
            $table->string('category')->default('personality'); // personality, skills, interests, work-style
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['category', 'active', 'order']);
        });

        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->string('result_key')->unique(); // e.g., 'creative', 'analytical', 'leader'
            $table->string('title');
            $table->text('description');
            $table->json('recommended_categories'); // Category IDs
            $table->json('traits')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_quiz_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->json('answers');
            $table->string('result_key')->nullable();
            $table->json('scores')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_quiz_responses');
        Schema::dropIfExists('quiz_results');
        Schema::dropIfExists('quiz_questions');
    }
};
