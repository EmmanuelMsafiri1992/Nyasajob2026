<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add polymorphic columns for payable (Post|User)
            if (!Schema::hasColumn('payments', 'payable_id')) {
                $table->bigInteger('payable_id')->unsigned()->nullable()
                    ->after('id')
                    ->comment('Post|User ID');
            }

            if (!Schema::hasColumn('payments', 'payable_type')) {
                $table->string('payable_type', 255)->nullable()
                    ->after('payable_id')
                    ->comment('Post|User class name');
            }

            // Add currency_code if not exists
            if (!Schema::hasColumn('payments', 'currency_code')) {
                $table->string('currency_code', 3)->nullable()->after('amount');
            }

            // Add period tracking columns
            if (!Schema::hasColumn('payments', 'period_start')) {
                $table->timestamp('period_start')->nullable()->after('currency_code');
            }

            if (!Schema::hasColumn('payments', 'period_end')) {
                $table->timestamp('period_end')->nullable()->after('period_start');
            }

            // Add cancelation and refund tracking
            if (!Schema::hasColumn('payments', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()
                    ->after('period_end')
                    ->comment('Canceled by the user before the period end');
            }

            if (!Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('canceled_at');
            }
        });

        // Migrate existing post_id data to polymorphic columns
        DB::statement("
            UPDATE payments
            SET payable_id = post_id,
                payable_type = 'App\\\\Models\\\\Post'
            WHERE post_id IS NOT NULL AND payable_id IS NULL
        ");

        // Add indexes for new columns
        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['payable_id', 'payable_type']);
                $table->index(['transaction_id']);
                $table->index(['period_start', 'period_end']);
                $table->index(['canceled_at']);
                $table->index(['refunded_at']);
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
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex(['payable_id', 'payable_type']);
                $table->dropIndex(['transaction_id']);
                $table->dropIndex(['period_start', 'period_end']);
                $table->dropIndex(['canceled_at']);
                $table->dropIndex(['refunded_at']);
            });
        } catch (\Exception $e) {
            // Indexes might not exist
        }

        Schema::table('payments', function (Blueprint $table) {

            // Drop columns
            if (Schema::hasColumn('payments', 'payable_id')) {
                $table->dropColumn('payable_id');
            }

            if (Schema::hasColumn('payments', 'payable_type')) {
                $table->dropColumn('payable_type');
            }

            if (Schema::hasColumn('payments', 'currency_code')) {
                $table->dropColumn('currency_code');
            }

            if (Schema::hasColumn('payments', 'period_start')) {
                $table->dropColumn('period_start');
            }

            if (Schema::hasColumn('payments', 'period_end')) {
                $table->dropColumn('period_end');
            }

            if (Schema::hasColumn('payments', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }

            if (Schema::hasColumn('payments', 'refunded_at')) {
                $table->dropColumn('refunded_at');
            }
        });
    }
};
