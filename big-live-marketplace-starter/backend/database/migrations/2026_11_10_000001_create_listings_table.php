<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('city_id');
            $table->string('title',180);
            $table->text('description');
            $table->unsignedBigInteger('price_minor')->nullable();
            $table->char('currency',3);
            $table->string('condition_code',20);
            $table->string('status',30)->default('draft');
            $table->timestamps();
            $table->index(['status','id']);
            $table->index(['category_id','status']);
            $table->index(['city_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('listings'); }
};
