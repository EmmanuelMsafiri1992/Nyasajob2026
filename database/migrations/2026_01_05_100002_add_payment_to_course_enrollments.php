<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('course_enrollments', 'payment_id')) {
                $table->foreignId('payment_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('course_enrollments', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_id');
            }
            if (!Schema::hasColumn('course_enrollments', 'currency_code')) {
                $table->string('currency_code', 3)->default('USD')->after('amount_paid');
            }
            if (!Schema::hasColumn('course_enrollments', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('currency_code')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('course_enrollments', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_id');
            }
            if (!Schema::hasColumn('course_enrollments', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded', 'free'])->default('pending')->after('discount_amount');
            }
            if (!Schema::hasColumn('course_enrollments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_status');
            }
        });

        // Add currency to courses table if not exists
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'currency_code')) {
                $table->string('currency_code', 3)->default('USD')->after('price');
            }
            if (!Schema::hasColumn('courses', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('currency_code')->comment('For showing strikethrough price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['payment_id', 'amount_paid', 'currency_code', 'coupon_id', 'discount_amount', 'payment_status', 'transaction_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'original_price']);
        });
    }
};
