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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Add monthly_price and yearly_price for dual pricing
            if (!Schema::hasColumn('subscription_plans', 'monthly_price')) {
                $table->decimal('monthly_price', 10, 2)->nullable()
                    ->after('price')
                    ->comment('Monthly subscription price');
            }

            if (!Schema::hasColumn('subscription_plans', 'yearly_price')) {
                $table->decimal('yearly_price', 10, 2)->nullable()
                    ->after('monthly_price')
                    ->comment('Yearly subscription price');
            }

            // Add specific feature limits
            if (!Schema::hasColumn('subscription_plans', 'job_posts_limit')) {
                $table->integer('job_posts_limit')->nullable()
                    ->after('features')
                    ->comment('Number of job posts allowed per billing period');
            }

            if (!Schema::hasColumn('subscription_plans', 'featured_posts_limit')) {
                $table->integer('featured_posts_limit')->nullable()
                    ->after('job_posts_limit')
                    ->comment('Number of featured posts allowed');
            }

            if (!Schema::hasColumn('subscription_plans', 'resume_views_limit')) {
                $table->integer('resume_views_limit')->nullable()
                    ->after('featured_posts_limit')
                    ->comment('Number of resume views allowed');
            }

            // Add feature flags
            if (!Schema::hasColumn('subscription_plans', 'priority_support')) {
                $table->boolean('priority_support')->default(false)
                    ->after('resume_views_limit')
                    ->comment('Includes priority customer support');
            }

            if (!Schema::hasColumn('subscription_plans', 'analytics_access')) {
                $table->boolean('analytics_access')->default(false)
                    ->after('priority_support')
                    ->comment('Access to advanced analytics');
            }

            if (!Schema::hasColumn('subscription_plans', 'api_access')) {
                $table->boolean('api_access')->default(false)
                    ->after('analytics_access')
                    ->comment('Access to API endpoints');
            }

            if (!Schema::hasColumn('subscription_plans', 'white_label')) {
                $table->boolean('white_label')->default(false)
                    ->after('api_access')
                    ->comment('White label branding option');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $columns = [
                'monthly_price',
                'yearly_price',
                'job_posts_limit',
                'featured_posts_limit',
                'resume_views_limit',
                'priority_support',
                'analytics_access',
                'api_access',
                'white_label'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('subscription_plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
