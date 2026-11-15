<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('earning_withdrawals', function (Blueprint $table) {
            $table->string('idempotency_key', 100)->nullable();
            $table->unique(['user_id', 'idempotency_key'], 'earning_withdrawal_user_idempotency');
        });
    }

    public function down(): void
    {
        Schema::table('earning_withdrawals', function (Blueprint $table) {
            $table->dropUnique('earning_withdrawal_user_idempotency');
            $table->dropColumn('idempotency_key');
        });
    }
};
