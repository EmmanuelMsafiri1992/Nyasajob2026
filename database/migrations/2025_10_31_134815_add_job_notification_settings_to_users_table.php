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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Add job notification preference column
                // Default TRUE so existing users get notifications (they can opt-out)
                $table->boolean('job_notification_enabled')->default(true)->after('can_be_impersonated');

                // Track when user last received a job notification (to prevent spam)
                $table->timestamp('last_job_notification_at')->nullable()->after('job_notification_enabled');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['job_notification_enabled', 'last_job_notification_at']);
            });
        }
    }
};
