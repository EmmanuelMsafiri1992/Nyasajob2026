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
        Schema::create('desktop_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->json('desktop_icons')->nullable()->comment('Icons to show on desktop');
            $table->json('taskbar_apps')->nullable()->comment('Apps pinned to taskbar');
            $table->json('start_menu_apps')->nullable()->comment('Apps shown in start menu');
            $table->json('initial_windows')->nullable()->comment('Windows open at lesson start');
            $table->json('filesystem')->nullable()->comment('Virtual file system structure');
            $table->string('wallpaper')->nullable()->comment('Desktop wallpaper image');
            $table->enum('mode', ['guided', 'free', 'both'])->default('guided');
            $table->boolean('show_taskbar')->default(true);
            $table->boolean('show_start_menu')->default(true);
            $table->boolean('show_desktop_icons')->default(true);
            $table->boolean('allow_window_resize')->default(true);
            $table->boolean('allow_window_move')->default(true);
            $table->json('disabled_apps')->nullable()->comment('Apps that cannot be opened');
            $table->json('hidden_elements')->nullable()->comment('UI elements to hide');
            $table->timestamps();

            $table->foreign('lesson_id')
                ->references('id')
                ->on('course_lessons')
                ->onDelete('cascade');

            $table->unique('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desktop_configs');
    }
};
