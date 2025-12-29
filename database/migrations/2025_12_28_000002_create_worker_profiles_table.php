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
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('country_code', 2)->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('district', 150)->nullable();
            $table->string('title', 200)->nullable();
            $table->text('bio')->nullable();
            $table->text('custom_skills')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'not_available'])->default('available');
            $table->boolean('is_public')->default(false);
            $table->string('photo', 255)->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('whatsapp', 60)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamp('featured_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('country_code');
            $table->index('city_id');
            $table->index('availability_status');
            $table->index('is_public');
            $table->index('experience_years');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_profiles');
    }
};
