<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sponsored_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('currency', 3)->default('PKR');
            $table->unsignedBigInteger('received_minor')->default(0);
            $table->unsignedBigInteger('reserved_minor')->default(0);
            $table->unsignedBigInteger('spent_minor')->default(0);
            $table->string('payment_reference')->nullable();
            $table->foreignId('payment_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('payment_verified_at')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('show_web')->default(false);
            $table->boolean('show_android')->default(false);
            $table->boolean('show_ios')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
        Schema::create('sponsored_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('sponsored_campaigns')->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions');
            $table->string('task_type');
            $table->unsignedBigInteger('reward_minor');
            $table->unsignedInteger('max_participants');
            $table->unsignedInteger('approved_count')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
        });
        Schema::create('sponsored_task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('sponsored_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('proof');
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['task_id','user_id']);
        });
        Schema::create('earning_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('available_minor')->default(0);
            $table->unsignedBigInteger('held_minor')->default(0);
            $table->timestamps();
        });
        Schema::create('earning_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('entry_type');
            $table->bigInteger('amount_minor');
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->timestamps();
            $table->unique(['entry_type','reference_type','reference_id','user_id'], 'earning_entry_idempotency');
        });
        Schema::create('earning_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('amount_minor');
            $table->string('method');
            $table->text('beneficiary_encrypted');
            $table->string('status')->default('requested');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transfer_reference')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        foreach (['earning_withdrawals','earning_ledger_entries','earning_wallets','sponsored_task_submissions','sponsored_tasks','sponsored_campaigns'] as $table) Schema::dropIfExists($table);
    }
};
