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
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('reaction_type', 20)->default('like'); // like, love, celebrate, insightful, curious
            $table->timestamps();

            // Indexes
            $table->index('post_id');
            $table->index('user_id');
            $table->index('reaction_type');
            $table->index(['post_id', 'reaction_type']);

            // Unique constraint: one reaction type per user/ip per post
            $table->unique(['post_id', 'user_id', 'reaction_type'], 'post_user_reaction_unique');
            $table->unique(['post_id', 'ip_address', 'reaction_type'], 'post_ip_reaction_unique');

            // Foreign keys
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
