<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Coupons/Promo Codes
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->nullable()->comment('Minimum purchase to apply');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->comment('Cap for percentage discounts');
            $table->string('currency_code', 3)->default('USD');
            $table->integer('usage_limit')->nullable()->comment('Total times coupon can be used');
            $table->integer('usage_limit_per_user')->default(1);
            $table->integer('times_used')->default(0);
            $table->json('applicable_to')->nullable()->comment('packages, courses, subscriptions, all');
            $table->json('excluded_items')->nullable()->comment('Specific IDs to exclude');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_first_order_only')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['code', 'active']);
            $table->index(['starts_at', 'expires_at']);
        });

        // Track coupon usage per user
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('original_amount', 10, 2);
            $table->decimal('final_amount', 10, 2);
            $table->string('order_type')->nullable()->comment('package, course, subscription');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamps();

            $table->index(['coupon_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
