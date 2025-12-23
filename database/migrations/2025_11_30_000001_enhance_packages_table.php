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
        Schema::table('packages', function (Blueprint $table) {
            // Add type column for promotion vs subscription packages
            if (!Schema::hasColumn('packages', 'type')) {
                $table->enum('type', ['promotion', 'subscription'])->default('promotion')
                    ->after('id')
                    ->comment('Post promotion OR User subscription');
            }

            // Rename promo_duration to promotion_time if exists
            if (Schema::hasColumn('packages', 'promo_duration') && !Schema::hasColumn('packages', 'promotion_time')) {
                $table->renameColumn('promo_duration', 'promotion_time');
            }

            // Add interval column for subscription validity period
            if (!Schema::hasColumn('packages', 'interval')) {
                $table->enum('interval', ['week', 'month', 'year'])->nullable()
                    ->after('currency_code')
                    ->comment('Package\'s validity period');
            }

            // Add listings_limit column
            if (!Schema::hasColumn('packages', 'listings_limit')) {
                $table->integer('listings_limit')->nullable()
                    ->after('interval')
                    ->comment('Listings per subscriber (during the "interval")');
            }

            // Rename duration to expiration_time if exists
            if (Schema::hasColumn('packages', 'duration') && !Schema::hasColumn('packages', 'expiration_time')) {
                $table->renameColumn('duration', 'expiration_time');
            }

        });

        // Add index on type (in separate statement)
        try {
            Schema::table('packages', function (Blueprint $table) {
                $table->index(['type']);
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Remove type column
            if (Schema::hasColumn('packages', 'type')) {
                $table->dropColumn('type');
            }

            // Rename promotion_time back to promo_duration
            if (Schema::hasColumn('packages', 'promotion_time') && !Schema::hasColumn('packages', 'promo_duration')) {
                $table->renameColumn('promotion_time', 'promo_duration');
            }

            // Remove interval column
            if (Schema::hasColumn('packages', 'interval')) {
                $table->dropColumn('interval');
            }

            // Remove listings_limit column
            if (Schema::hasColumn('packages', 'listings_limit')) {
                $table->dropColumn('listings_limit');
            }

            // Rename expiration_time back to duration
            if (Schema::hasColumn('packages', 'expiration_time') && !Schema::hasColumn('packages', 'duration')) {
                $table->renameColumn('expiration_time', 'duration');
            }
        });
    }
};
