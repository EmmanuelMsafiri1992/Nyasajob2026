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
        Schema::create('job_seeker_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Job preferences
            $table->string('desired_job_title')->nullable();
            $table->text('job_keywords')->nullable(); // Comma-separated keywords
            $table->json('preferred_categories')->nullable(); // Category IDs
            $table->json('preferred_job_types')->nullable(); // Full-time, Part-time, Contract, etc.
            $table->json('preferred_locations')->nullable(); // City/Country preferences
            $table->boolean('remote_only')->default(false);

            // Salary expectations
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->string('salary_period')->default('monthly'); // hourly, daily, weekly, monthly, yearly

            // Urgency and availability
            $table->enum('urgency_level', ['not_urgent', 'within_month', 'within_week', 'immediate'])->default('within_month');
            $table->date('available_from')->nullable();
            $table->text('availability_notes')->nullable();

            // Experience and qualifications
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'executive'])->default('mid');
            $table->integer('years_of_experience')->default(0);
            $table->text('key_skills')->nullable(); // Comma-separated skills
            $table->text('qualifications')->nullable(); // Education, certifications
            $table->text('languages')->nullable(); // Languages spoken

            // CV/Resume info for refinement suggestions
            $table->text('cv_summary')->nullable(); // Brief professional summary
            $table->text('career_goals')->nullable();
            $table->string('cv_file_path')->nullable(); // Uploaded CV
            $table->timestamp('cv_last_updated')->nullable();

            // Job match settings
            $table->boolean('email_alerts')->default(true);
            $table->enum('alert_frequency', ['instant', 'daily', 'weekly'])->default('daily');
            $table->integer('max_alerts_per_day')->default(10);

            // Premium features tracking
            $table->integer('job_matches_count')->default(0);
            $table->integer('cv_reviews_count')->default(0);
            $table->integer('interview_tips_viewed')->default(0);
            $table->timestamp('last_job_match_at')->nullable();

            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['urgency_level', 'available_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seeker_preferences');
    }
};
