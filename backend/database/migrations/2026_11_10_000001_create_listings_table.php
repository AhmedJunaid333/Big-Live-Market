<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('city_id')->index();
            $table->string('title', 180);
            $table->string('slug', 220)->nullable()->index();
            $table->text('description');
            $table->unsignedBigInteger('price_minor')->nullable();
            $table->char('currency', 3)->default('PKR');
            $table->boolean('negotiable')->default(false);
            $table->string('condition_code', 20)->default('used');
            $table->string('status', 30)->default('draft');
            $table->string('moderation_status', 30)->default('pending');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_label', 190)->nullable();
            $table->text('address_private')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('favorites_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'published_at', 'id']);
            $table->index(['category_id', 'status', 'price_minor']);
            $table->index(['city_id', 'status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
