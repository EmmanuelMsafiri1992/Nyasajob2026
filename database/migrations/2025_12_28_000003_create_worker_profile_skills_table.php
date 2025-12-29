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
        Schema::create('worker_profile_skills', function (Blueprint $table) {
            $table->unsignedBigInteger('worker_profile_id');
            $table->unsignedBigInteger('worker_skill_id');

            $table->primary(['worker_profile_id', 'worker_skill_id']);
            $table->index('worker_profile_id');
            $table->index('worker_skill_id');

            $table->foreign('worker_profile_id')
                ->references('id')
                ->on('worker_profiles')
                ->onDelete('cascade');

            $table->foreign('worker_skill_id')
                ->references('id')
                ->on('worker_skills')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_profile_skills');
    }
};
