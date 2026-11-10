<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->foreignId('logo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('banner_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('verification_status', 30)->default('pending'); // pending, verified, rejected
            $table->string('status', 30)->default('active'); // active, suspended
            $table->unsignedInteger('listing_quota')->default(10);
            $table->timestamps();

            $table->index(['owner_id', 'status']);
        });

        Schema::create('business_members', function (Blueprint $table) {
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 30)->default('manager'); // owner, manager, agent
            $table->timestamps();

            $table->primary(['business_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_members');
        Schema::dropIfExists('businesses');
    }
};
