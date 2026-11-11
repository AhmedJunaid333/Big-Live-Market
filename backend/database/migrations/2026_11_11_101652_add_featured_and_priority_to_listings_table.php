<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status')->index();
            $table->string('featured_plan', 30)->nullable()->after('is_featured'); // top_banner, spotlight, bump_up
            $table->timestamp('featured_until')->nullable()->after('featured_plan')->index();
            $table->unsignedInteger('priority_rank')->default(0)->after('featured_until')->index();
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_plan', 'featured_until', 'priority_rank']);
        });
    }
};
