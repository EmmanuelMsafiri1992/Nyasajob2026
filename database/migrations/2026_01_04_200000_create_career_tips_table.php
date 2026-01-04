<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_tips', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('category')->default('general'); // general, cv, interview, job-search, career-growth
            $table->string('featured_image')->nullable();
            $table->integer('reading_time')->default(5); // minutes
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('active')->default(true);
            $table->json('meta_tags')->nullable();
            $table->timestamps();

            $table->index(['category', 'active']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_tips');
    }
};
