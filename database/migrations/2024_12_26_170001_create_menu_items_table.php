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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable();

            // Item type
            $table->enum('type', ['link', 'button', 'divider', 'text', 'page', 'category', 'dropdown'])
                  ->default('link');

            // Content
            $table->string('title', 100);
            $table->string('url', 500)->nullable();
            $table->string('route_name', 100)->nullable();
            $table->json('route_params')->nullable();

            // Display options
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->string('icon', 100)->nullable();
            $table->string('css_class', 255)->nullable();

            // Visibility conditions
            $table->json('visibility_conditions')->nullable();
            // Example: {"auth_required": true, "guest_only": false, "roles": ["admin"], "permissions": ["manage-users"]}

            // Additional attributes
            $table->json('attributes')->nullable();

            // Polymorphic relation (for page/category types)
            $table->string('linkable_type', 100)->nullable();
            $table->unsignedBigInteger('linkable_id')->nullable();

            // Nested set columns
            $table->unsignedInteger('lft')->default(0);
            $table->unsignedInteger('rgt')->default(0);
            $table->unsignedInteger('depth')->default(0);

            $table->boolean('active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('menu_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('active');
            $table->index(['lft', 'rgt']);
            $table->index(['linkable_type', 'linkable_id']);

            // Foreign key for parent
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('menu_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
