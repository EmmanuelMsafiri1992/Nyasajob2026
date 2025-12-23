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
        Schema::create('ad_targeting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_advertisement_id');
            $table->enum('target_type', ['country', 'state', 'city'])->default('country');
            $table->string('target_code', 50)->comment('country_code, admin1_code, or city_id');
            $table->timestamps();

            $table->index(['product_advertisement_id']);
            $table->index(['target_type', 'target_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_targeting');
    }
};
