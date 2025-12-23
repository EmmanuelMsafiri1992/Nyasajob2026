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
        Schema::create('ad_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('ad_package_id')->nullable();
            $table->integer('payment_method_id')->unsigned()->nullable();
            $table->string('transaction_id', 255)->nullable()->comment('Unique Transaction ID');
            $table->decimal('amount', 10, 2)->unsigned()->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->unsigned()->default('0');
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['ad_package_id']);
            $table->index(['payment_method_id']);
            $table->index(['transaction_id']);
            $table->index(['status']);
            $table->index(['active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_subscriptions');
    }
};
