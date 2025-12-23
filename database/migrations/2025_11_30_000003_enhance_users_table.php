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
        Schema::table('users', function (Blueprint $table) {
            // Add dark_mode column
            if (!Schema::hasColumn('users', 'dark_mode')) {
                $table->boolean('dark_mode')->nullable()->default('0')
                    ->after('accept_marketing_offers')
                    ->comment('User preference for dark mode theme');
            }

            // Add featured column for promoted users
            if (!Schema::hasColumn('users', 'featured')) {
                $table->boolean('featured')->nullable()->default('0')
                    ->after('time_zone')
                    ->comment('Need to be cleared from a cron tab command');
            }

            // Rename ip_addr to create_from_ip if it exists
            if (Schema::hasColumn('users', 'ip_addr') && !Schema::hasColumn('users', 'create_from_ip')) {
                $table->renameColumn('ip_addr', 'create_from_ip');
            }

            // Add latest_update_ip column
            if (!Schema::hasColumn('users', 'latest_update_ip')) {
                $table->string('latest_update_ip', 50)->nullable()
                    ->after('create_from_ip')
                    ->comment('Latest update IP address');
            }
        });

        // Update comment on create_from_ip if it was just renamed
        if (Schema::hasColumn('users', 'create_from_ip')) {
            DB::statement("ALTER TABLE users MODIFY create_from_ip VARCHAR(50) NULL COMMENT 'IP address of creation'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove dark_mode column
            if (Schema::hasColumn('users', 'dark_mode')) {
                $table->dropColumn('dark_mode');
            }

            // Remove featured column
            if (Schema::hasColumn('users', 'featured')) {
                $table->dropColumn('featured');
            }

            // Rename create_from_ip back to ip_addr
            if (Schema::hasColumn('users', 'create_from_ip') && !Schema::hasColumn('users', 'ip_addr')) {
                $table->renameColumn('create_from_ip', 'ip_addr');
            }

            // Remove latest_update_ip column
            if (Schema::hasColumn('users', 'latest_update_ip')) {
                $table->dropColumn('latest_update_ip');
            }
        });
    }
};
