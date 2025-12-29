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
        Schema::create('worker_skills', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedInteger('lft')->nullable();
            $table->unsignedInteger('rgt')->nullable();
            $table->unsignedInteger('depth')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('active');
            $table->index('lft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_skills');
    }
};
