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
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Add billing_cycle column
            if (!Schema::hasColumn('user_subscriptions', 'billing_cycle')) {
                $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly')
                    ->after('subscription_plan_id')
                    ->comment('Billing frequency');
            }

            // Add auto_renew flag
            if (!Schema::hasColumn('user_subscriptions', 'auto_renew')) {
                $table->boolean('auto_renew')->default(true)
                    ->after('ends_at')
                    ->comment('Automatically renew subscription');
            }

            // Add features_used JSON column for tracking
            if (!Schema::hasColumn('user_subscriptions', 'features_used')) {
                $table->json('features_used')->nullable()
                    ->after('auto_renew')
                    ->comment('JSON object tracking feature usage');
            }

            // Add usage tracking columns
            if (!Schema::hasColumn('user_subscriptions', 'jobs_posted_count')) {
                $table->integer('jobs_posted_count')->default(0)
                    ->after('features_used')
                    ->comment('Number of jobs posted in current period');
            }

            if (!Schema::hasColumn('user_subscriptions', 'featured_posts_used')) {
                $table->integer('featured_posts_used')->default(0)
                    ->after('jobs_posted_count')
                    ->comment('Number of featured posts used');
            }

            if (!Schema::hasColumn('user_subscriptions', 'resume_views_used')) {
                $table->integer('resume_views_used')->default(0)
                    ->after('featured_posts_used')
                    ->comment('Number of resume views used');
            }

        });

        // Add indexes for better query performance
        try {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->index(['billing_cycle']);
                $table->index(['auto_renew']);
            });
        } catch (\Exception $e) {
            // Indexes might already exist
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop indexes first
        try {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->dropIndex(['billing_cycle']);
                $table->dropIndex(['auto_renew']);
            });
        } catch (\Exception $e) {
            // Indexes might not exist
        }

        Schema::table('user_subscriptions', function (Blueprint $table) {

            // Drop columns
            $columns = [
                'billing_cycle',
                'auto_renew',
                'features_used',
                'jobs_posted_count',
                'featured_posts_used',
                'resume_views_used'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('user_subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
