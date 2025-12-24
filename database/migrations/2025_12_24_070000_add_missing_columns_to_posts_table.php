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
        Schema::table('posts', function (Blueprint $table) {
            // Add payment_id column for subscription tracking
            if (!Schema::hasColumn('posts', 'payment_id')) {
                $table->bigInteger('payment_id')->unsigned()->nullable()
                    ->after('user_id')
                    ->comment('ID of the subscription used to publish the listing');
            }

            // Add currency_code column
            if (!Schema::hasColumn('posts', 'currency_code')) {
                $table->string('currency_code', 3)->nullable()->after('salary_type_id');
            }

            // Add create_from_ip column
            if (!Schema::hasColumn('posts', 'create_from_ip')) {
                $table->string('create_from_ip', 50)->nullable()
                    ->after('lat')
                    ->comment('IP address of creation');
            }

            // Add latest_update_ip column
            if (!Schema::hasColumn('posts', 'latest_update_ip')) {
                $table->string('latest_update_ip', 50)->nullable()
                    ->after('create_from_ip')
                    ->comment('Latest update IP address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $columns = ['payment_id', 'currency_code', 'create_from_ip', 'latest_update_ip'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('posts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
