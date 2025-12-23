<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the type enum to include 'interactive'
        DB::statement("ALTER TABLE course_lessons MODIFY COLUMN type ENUM('video', 'text', 'quiz', 'exercise', 'interactive') DEFAULT 'text'");

        // Add interactive_config column for simulation settings
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->json('interactive_config')->nullable()->after('is_free_preview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the interactive_config column
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('interactive_config');
        });

        // Revert the type enum
        DB::statement("ALTER TABLE course_lessons MODIFY COLUMN type ENUM('video', 'text', 'quiz', 'exercise') DEFAULT 'text'");
    }
};
