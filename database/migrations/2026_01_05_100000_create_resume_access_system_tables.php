<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Resume/Candidate Access Packages for Employers
        Schema::create('resume_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency_code', 3)->default('USD');
            $table->integer('credits')->comment('Number of candidate contacts allowed');
            $table->integer('validity_days')->default(30)->comment('Days before credits expire');
            $table->boolean('unlimited_search')->default(true);
            $table->boolean('export_allowed')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Employer Credits for Accessing Resumes/Candidates
        Schema::create('resume_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resume_package_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('credits_purchased')->default(0);
            $table->integer('credits_used')->default(0);
            $table->integer('credits_remaining')->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('currency_code', 3)->default('USD');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });

        // Track which candidates/profiles an employer has viewed/contacted
        Schema::create('resume_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('worker_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('resume_credit_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('contact_unlocked')->default(false);
            $table->timestamp('viewed_at');
            $table->timestamp('contact_unlocked_at')->nullable();
            $table->timestamps();

            $table->unique(['employer_id', 'worker_profile_id']);
            $table->index('viewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_views');
        Schema::dropIfExists('resume_credits');
        Schema::dropIfExists('resume_packages');
    }
};
