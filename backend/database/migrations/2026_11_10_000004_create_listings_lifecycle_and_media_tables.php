<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('category_attribute_id')->constrained('category_attributes')->cascadeOnDelete();
            $table->string('value_text', 255)->nullable();
            $table->decimal('value_number', 12, 2)->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();

            $table->unique(['listing_id', 'category_attribute_id']);
            $table->index(['category_attribute_id', 'value_number']);
            $table->index(['category_attribute_id', 'value_text']);
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('disk', 30)->default('local');
            $table->string('path', 255);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('bytes')->default(0);
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->string('visibility', 20)->default('public');
            $table->timestamps();
        });

        Schema::create('listing_media', function (Blueprint $table) {
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);

            $table->primary(['listing_id', 'media_id']);
        });

        Schema::create('listing_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 30);
            $table->string('to_status', 30);
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['listing_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_status_history');
        Schema::dropIfExists('listing_media');
        Schema::dropIfExists('media');
        Schema::dropIfExists('listing_attribute_values');
    }
};
