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
    public function up()
    {
        Schema::table('job_matches', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('applied_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_matches', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'rejected_at']);
        });
    }
};
