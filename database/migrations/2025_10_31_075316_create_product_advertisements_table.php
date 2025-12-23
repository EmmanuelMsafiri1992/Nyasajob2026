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
        Schema::create('product_advertisements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('ad_subscription_id')->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('url', 500)->nullable()->comment('External product/landing page URL');
            $table->enum('status', ['pending', 'active', 'paused', 'expired', 'rejected'])->default('pending');
            $table->integer('impressions')->unsigned()->default(0)->comment('Total views');
            $table->integer('clicks')->unsigned()->default(0)->comment('Total clicks');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->unsigned()->default('1');
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['ad_subscription_id']);
            $table->index(['status']);
            $table->index(['active']);
            $table->index(['starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_advertisements');
    }
};
