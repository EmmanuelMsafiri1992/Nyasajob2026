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
        Schema::create('ad_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('short_name', 100)->nullable();
            $table->decimal('price', 10, 2)->unsigned()->default('0.00');
            $table->string('currency_code', 3)->nullable();
            $table->integer('duration_days')->unsigned()->nullable()->comment('Duration in days');
            $table->boolean('first_position')->default(0)->comment('Show ad in first position');
            $table->integer('impressions_limit')->unsigned()->nullable()->comment('Maximum impressions allowed');
            $table->integer('clicks_limit')->unsigned()->nullable()->comment('Maximum clicks allowed');
            $table->text('description')->nullable();
            $table->integer('lft')->unsigned()->nullable();
            $table->integer('rgt')->unsigned()->nullable();
            $table->integer('depth')->unsigned()->nullable();
            $table->boolean('recommended')->unsigned()->nullable()->default('0');
            $table->boolean('active')->unsigned()->default('1');
            $table->timestamps();

            $table->index(['active']);
            $table->index(['first_position']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_packages');
    }
};
