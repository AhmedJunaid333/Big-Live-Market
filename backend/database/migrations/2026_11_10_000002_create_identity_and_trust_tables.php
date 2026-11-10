<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider', 30);
            $table->string('provider_subject', 190);
            $table->timestamps();

            $table->unique(['provider', 'provider_subject']);
            $table->index('user_id');
        });

        Schema::create('otp_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('destination_hash', 64);
            $table->string('purpose', 30);
            $table->string('code_hash', 128);
            $table->timestamp('expires_at');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();

            $table->index(['destination_hash', 'purpose']);
        });

        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_name', 100)->nullable();
            $table->string('device_fingerprint_hash', 64)->nullable();
            $table->string('platform', 30)->default('unknown');
            $table->text('push_token_encrypted')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'revoked_at']);
        });

        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('document_type', 40); // terms_of_service, privacy_policy, age_declaration
            $table->string('version', 20);
            $table->timestamp('accepted_at');
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'document_type']);
        });

        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 30); // cnic, passport, trade_license
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->string('evidence_private_path', 255);
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['blocker_id', 'blocked_id']);
        });

        Schema::create('seller_follows', function (Blueprint $table) {
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['follower_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_follows');
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('user_verifications');
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('otp_challenges');
        Schema::dropIfExists('oauth_accounts');
    }
};
