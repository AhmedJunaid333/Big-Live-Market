<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->char('currency', 3)->default('PKR');
            $table->unsignedBigInteger('price_minor');
            $table->string('billing_period', 20)->default('monthly'); // monthly, annual
            $table->unsignedInteger('listing_quota')->default(50);
            $table->json('benefits_json')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->restrictOnDelete();
            $table->string('provider', 30)->default('manual');
            $table->string('provider_subscription_id', 120)->nullable();
            $table->string('status', 30)->default('active'); // active, past_due, cancelled, expired
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('renews_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('promotion_products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('placement', 30); // featured, category_bump, home_page
            $table->unsignedSmallInteger('duration_hours')->default(168); // 7 days default
            $table->char('currency', 3)->default('PKR');
            $table->unsignedBigInteger('price_minor');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->char('currency', 3)->default('PKR');
            $table->unsignedBigInteger('subtotal_minor');
            $table->unsignedBigInteger('tax_minor')->default(0);
            $table->unsignedBigInteger('total_minor');
            $table->string('status', 30)->default('pending'); // pending, awaiting_payment, paid, failed, cancelled
            $table->string('idempotency_key', 100)->unique();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('item_type', 40); // promotion, subscription
            $table->unsignedBigInteger('item_reference_id');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price_minor');
            $table->unsignedBigInteger('total_minor');
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('promotion_product_id')->constrained('promotion_products')->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status', 30)->default('active'); // active, expired, cancelled
            $table->timestamps();

            $table->index(['listing_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->string('provider', 30); // stripe, jazzcash, easypaisa, mock
            $table->string('provider_payment_id', 120)->nullable();
            $table->unsignedBigInteger('amount_minor');
            $table->char('currency', 3);
            $table->string('status', 30)->default('pending'); // pending, captured, failed, refunded
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_payment_id']);
        });

        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30);
            $table->string('external_event_id', 120);
            $table->string('payload_hash', 64);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'external_event_id']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->timestamp('issued_at');
            $table->json('tax_breakdown_json')->nullable();
            $table->string('pdf_private_path', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->restrictOnDelete();
            $table->string('provider_refund_id', 120)->nullable();
            $table->unsignedBigInteger('amount_minor');
            $table->string('reason', 255)->nullable();
            $table->string('status', 30)->default('pending'); // pending, approved, processed, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payment_webhook_events');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('promotion_products');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
