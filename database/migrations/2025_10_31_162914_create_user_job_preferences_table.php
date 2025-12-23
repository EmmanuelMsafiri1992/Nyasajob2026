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
        Schema::create('user_job_preferences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Job matching preferences
            $table->json('preferred_categories')->nullable(); // Array of category IDs
            $table->text('skills')->nullable(); // Comma-separated skills
            $table->text('qualifications')->nullable(); // Education, certifications
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->string('employment_type')->nullable(); // full-time, part-time, contract, etc.
            $table->boolean('remote_work')->default(false);

            // Auto-apply settings
            $table->boolean('auto_apply_enabled')->default(false);
            $table->enum('urgency_level', ['not_urgent', 'moderate', 'very_urgent', 'desperate'])->default('not_urgent');
            $table->integer('max_applications_per_day')->default(5);
            $table->integer('min_match_percentage')->default(60); // Minimum % match to auto-apply

            // Auto-apply behavior based on urgency
            // not_urgent: Show matches, wait for manual approval
            // moderate: Auto-apply to 70%+ matches, show rest
            // very_urgent: Auto-apply to 50%+ matches
            // desperate: Auto-apply to all matches 40%+

            // Application preferences
            $table->text('cover_letter_template')->nullable();
            $table->bigInteger('default_resume_id')->unsigned()->nullable();
            $table->foreign('default_resume_id')->references('id')->on('resumes')->onDelete('set null');

            // Tracking
            $table->integer('total_auto_applications')->default(0);
            $table->timestamp('last_application_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_job_preferences');
    }
};
