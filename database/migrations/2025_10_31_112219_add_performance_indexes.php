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
        // Add indexes to posts table for better query performance
        if (Schema::hasTable('posts')) {
            try {
                Schema::table('posts', function (Blueprint $table) {
                    $table->index('active', 'posts_active_index');
                });
            } catch (\Exception $e) {
                // Index may already exist
            }

            try {
                Schema::table('posts', function (Blueprint $table) {
                    $table->index('created_at', 'posts_created_at_index');
                });
            } catch (\Exception $e) {
                // Index may already exist
            }

            try {
                Schema::table('posts', function (Blueprint $table) {
                    $table->index(['active', 'created_at'], 'posts_active_created_at_index');
                });
            } catch (\Exception $e) {
                // Index may already exist
            }
        }

        // Add indexes to categories table
        if (Schema::hasTable('categories')) {
            try {
                Schema::table('categories', function (Blueprint $table) {
                    $table->index('active', 'categories_active_index');
                });
            } catch (\Exception $e) {
                // Index may already exist
            }
        }

        // Add indexes to ad_packages table
        if (Schema::hasTable('ad_packages')) {
            try {
                Schema::table('ad_packages', function (Blueprint $table) {
                    $table->index('active', 'ad_packages_active_index');
                });
            } catch (\Exception $e) {
                // Index may already exist
            }

            try {
                Schema::table('ad_packages', function (Blueprint $table) {
                    $table->index(['active', 'recommended'], 'ad_packages_active_recommended_index');
                });
            } catch (\Exception $e) {
                // Index may already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex('posts_active_index');
                $table->dropIndex('posts_created_at_index');
                $table->dropIndex('posts_active_created_at_index');
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex('categories_active_index');
            });
        }

        if (Schema::hasTable('ad_packages')) {
            Schema::table('ad_packages', function (Blueprint $table) {
                $table->dropIndex('ad_packages_active_index');
                $table->dropIndex('ad_packages_active_recommended_index');
            });
        }
    }
};
