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
        Schema::create('premium_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_type')->default('job_seeker_premium'); // Plan identifier
            $table->decimal('amount', 10, 2)->default(5.00);
            $table->string('currency', 3)->default('USD');
            $table->string('paypal_subscription_id')->nullable()->index();
            $table->string('paypal_payer_id')->nullable();
            $table->string('paypal_payer_email')->nullable();
            $table->enum('status', ['pending', 'active', 'cancelled', 'expired', 'suspended'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->ipAddress('terms_accepted_ip')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->json('metadata')->nullable(); // Store additional PayPal data
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_subscriptions');
    }
};
