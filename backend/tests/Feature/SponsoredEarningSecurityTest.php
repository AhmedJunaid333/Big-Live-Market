<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SponsoredEarningSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function actor(string $role): User
    {
        Role::findOrCreate($role, 'web');
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function wallet(User $user, int $available, int $held = 0): void
    {
        DB::table('earning_wallets')->insert([
            'user_id' => $user->id,
            'available_minor' => $available,
            'held_minor' => $held,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_withdrawal_cannot_exceed_available_balance(): void
    {
        $user = $this->actor('user');
        $this->wallet($user, 500);

        $this->actingAs($user)->postJson('/api/v1/earn/withdrawals', [
            'amount_minor' => 501,
            'method' => 'jazzcash',
            'beneficiary' => '03001234567',
        ])->assertStatus(422);

        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id,
            'available_minor' => 500,
            'held_minor' => 0,
        ]);
        $this->assertDatabaseCount('earning_withdrawals', 0);
    }

    public function test_successful_withdrawal_holds_funds_once(): void
    {
        $user = $this->actor('user');
        $this->wallet($user, 1000);

        $this->actingAs($user)->postJson('/api/v1/earn/withdrawals', [
            'amount_minor' => 600,
            'method' => 'easypaisa',
            'beneficiary' => '03001234567',
        ])->assertCreated();

        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id,
            'available_minor' => 400,
            'held_minor' => 600,
        ]);
        $this->assertDatabaseCount('earning_ledger_entries', 1);
    }

    public function test_same_finance_officer_cannot_confirm_their_own_transfer(): void
    {
        $user = $this->actor('user');
        $finance = $this->actor('finance');
        $this->wallet($user, 0, 600);
        $id = DB::table('earning_withdrawals')->insertGetId([
            'user_id' => $user->id,
            'amount_minor' => 600,
            'method' => 'bank',
            'beneficiary_encrypted' => encrypt('test-bank-account'),
            'status' => 'transferred_pending_confirmation',
            'processed_by' => $finance->id,
            'transfer_reference' => 'manual-ref-001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($finance)->postJson("/api/v1/admin/earn/withdrawals/{$id}/confirm")
            ->assertForbidden();

        $this->assertDatabaseHas('earning_withdrawals', [
            'id' => $id,
            'status' => 'transferred_pending_confirmation',
        ]);
        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id,
            'held_minor' => 600,
        ]);
    }

    public function test_second_finance_officer_confirms_only_once(): void
    {
        $user = $this->actor('user');
        $first = $this->actor('finance');
        $second = $this->actor('finance');
        $this->wallet($user, 0, 600);
        $id = DB::table('earning_withdrawals')->insertGetId([
            'user_id' => $user->id,
            'amount_minor' => 600,
            'method' => 'bank',
            'beneficiary_encrypted' => encrypt('test-bank-account'),
            'status' => 'transferred_pending_confirmation',
            'processed_by' => $first->id,
            'transfer_reference' => 'manual-ref-002',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($second)->postJson("/api/v1/admin/earn/withdrawals/{$id}/confirm")
            ->assertOk();
        $this->actingAs($second)->postJson("/api/v1/admin/earn/withdrawals/{$id}/confirm")
            ->assertStatus(409);

        $this->assertDatabaseHas('earning_withdrawals', [
            'id' => $id,
            'status' => 'paid',
            'confirmed_by' => $second->id,
        ]);
        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id,
            'held_minor' => 0,
        ]);
        $this->assertDatabaseCount('earning_ledger_entries', 1);
    }
}
