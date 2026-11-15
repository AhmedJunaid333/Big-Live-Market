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

    public function test_submission_rejected_when_campaign_has_expired(): void
    {
        $user = $this->actor('buyer');
        $campaign = DB::table('sponsored_campaigns')->insertGetId([
            'company_name' => 'Example', 'title' => 'Expired campaign',
            'status' => 'active', 'payment_verified_at' => now(),
            'received_minor' => 1000, 'reserved_minor' => 1000,
            'starts_at' => now()->subDays(3), 'ends_at' => now()->subDay(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $task = DB::table('sponsored_tasks')->insertGetId([
            'campaign_id' => $campaign, 'title' => 'Expired task',
            'instructions' => 'Complete a survey', 'task_type' => 'survey',
            'reward_minor' => 100, 'max_participants' => 10,
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($user)->postJson("/api/v1/earn/tasks/{$task}/submissions", [
            'proof' => 'Proof of task completion',
        ])->assertStatus(422);
        $this->assertDatabaseCount('sponsored_task_submissions', 0);
    }

    public function test_underfunded_campaign_rejects_submissions(): void
    {
        $user = $this->actor('buyer');
        $campaign = DB::table('sponsored_campaigns')->insertGetId([
            'company_name' => 'Example', 'title' => 'Underfunded',
            'status' => 'active', 'payment_verified_at' => now(),
            'received_minor' => 100, 'reserved_minor' => 200,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $task = DB::table('sponsored_tasks')->insertGetId([
            'campaign_id' => $campaign, 'title' => 'Survey',
            'instructions' => 'Complete the survey', 'task_type' => 'survey',
            'reward_minor' => 100, 'max_participants' => 2,
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($user)->postJson("/api/v1/earn/tasks/{$task}/submissions", [
            'proof' => 'Valid proof submitted',
        ])->assertStatus(422);
        $this->assertDatabaseCount('sponsored_task_submissions', 0);
    }

    public function test_withdrawal_cannot_exceed_available_balance(): void
    {
        $user = $this->actor('user');
        $this->wallet($user, 500);

        $this->actingAs($user)->postJson('/api/v1/earn/withdrawals', [
            'amount_minor' => 501,
            'method' => 'jazzcash',
            'beneficiary' => '03001234567',
            'idempotency_key' => 'withdrawal-test-001',
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
            'idempotency_key' => 'withdrawal-test-001',
        ])->assertCreated();

        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id,
            'available_minor' => 400,
            'held_minor' => 600,
        ]);
        $this->assertDatabaseCount('earning_ledger_entries', 1);
    }

    public function test_retrying_same_withdrawal_key_does_not_hold_funds_twice(): void
    {
        $user = $this->actor('buyer');
        $this->wallet($user, 2000);
        $payload = [
            'amount_minor' => 600, 'method' => 'jazzcash',
            'beneficiary' => '03001234567', 'idempotency_key' => 'retry-safe-001',
        ];
        $this->actingAs($user)->postJson('/api/v1/earn/withdrawals', $payload)->assertCreated();
        $this->actingAs($user)->postJson('/api/v1/earn/withdrawals', $payload)->assertOk();
        $this->actingAs($user)->postJson('/api/v1/earn/withdrawals', [
            ...$payload, 'amount_minor' => 700,
        ])->assertStatus(409);
        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id, 'available_minor' => 1400, 'held_minor' => 600,
        ]);
        $this->assertDatabaseCount('earning_withdrawals', 1);
        $this->assertDatabaseCount('earning_ledger_entries', 1);
    }

    public function test_pending_withdrawal_can_be_cancelled_once_and_refunds_wallet(): void
    {
        $user = $this->actor('buyer');
        $this->wallet($user, 400, 600);
        $id = DB::table('earning_withdrawals')->insertGetId([
            'user_id' => $user->id, 'amount_minor' => 600, 'method' => 'bank',
            'beneficiary_encrypted' => encrypt('test-account'), 'status' => 'requested',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $url = "/api/v1/earn/withdrawals/{$id}/cancel";
        $this->actingAs($user)->postJson($url)->assertOk();
        $this->actingAs($user)->postJson($url)->assertStatus(409);
        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id, 'available_minor' => 1000, 'held_minor' => 0,
        ]);
        $this->assertDatabaseHas('earning_withdrawals', ['id' => $id, 'status' => 'cancelled']);
        $this->assertDatabaseCount('earning_ledger_entries', 1);
    }

    public function test_finance_rejection_releases_funds_once_and_blocks_transferred_payouts(): void
    {
        $user = $this->actor('buyer');
        $finance = $this->actor('finance');
        $this->wallet($user, 100, 600);
        $id = DB::table('earning_withdrawals')->insertGetId([
            'user_id' => $user->id, 'amount_minor' => 600, 'method' => 'bank',
            'beneficiary_encrypted' => encrypt('test-account'), 'status' => 'requested',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $url = "/api/v1/admin/earn/withdrawals/{$id}/reject";
        $this->actingAs($finance)->postJson($url, ['reason' => 'Invalid beneficiary'])->assertOk();
        $this->actingAs($finance)->postJson($url, ['reason' => 'Invalid beneficiary'])->assertStatus(409);
        $this->assertDatabaseHas('earning_wallets', [
            'user_id' => $user->id, 'available_minor' => 700, 'held_minor' => 0,
        ]);
        $this->assertDatabaseHas('earning_withdrawals', ['id' => $id, 'status' => 'rejected']);
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

    public function test_duplicate_payment_reference_on_campaign_verification_is_rejected(): void
    {
        $finance = $this->actor('finance');
        $c1 = DB::table('sponsored_campaigns')->insertGetId([
            'company_name' => 'Brand A',
            'title' => 'Campaign A',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $c2 = DB::table('sponsored_campaigns')->insertGetId([
            'company_name' => 'Brand B',
            'title' => 'Campaign B',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($finance)->postJson("/api/v1/admin/earn/campaigns/{$c1}/verify-payment", [
            'received_minor' => 50000,
            'payment_reference' => 'DEPOSIT-REF-999',
        ])->assertOk();

        // Duplicate payment reference for campaign 2 must fail validation (422)
        $this->actingAs($finance)->postJson("/api/v1/admin/earn/campaigns/{$c2}/verify-payment", [
            'received_minor' => 70000,
            'payment_reference' => 'DEPOSIT-REF-999',
        ])->assertUnprocessable();
    }

    public function test_duplicate_transfer_reference_on_withdrawal_is_rejected(): void
    {
        $user = $this->actor('user');
        $finance = $this->actor('finance');
        $this->wallet($user, 0, 1200);

        $w1 = DB::table('earning_withdrawals')->insertGetId([
            'user_id' => $user->id,
            'amount_minor' => 600,
            'method' => 'bank',
            'beneficiary_encrypted' => encrypt('test-bank-1'),
            'status' => 'requested',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $w2 = DB::table('earning_withdrawals')->insertGetId([
            'user_id' => $user->id,
            'amount_minor' => 600,
            'method' => 'bank',
            'beneficiary_encrypted' => encrypt('test-bank-2'),
            'status' => 'requested',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($finance)->postJson("/api/v1/admin/earn/withdrawals/{$w1}/transfer", [
            'transfer_reference' => 'TRX-PAYOUT-UNIQUE-001',
        ])->assertOk();

        // Attempting to reuse TRX-PAYOUT-UNIQUE-001 on w2 must be rejected with 422
        $this->actingAs($finance)->postJson("/api/v1/admin/earn/withdrawals/{$w2}/transfer", [
            'transfer_reference' => 'TRX-PAYOUT-UNIQUE-001',
        ])->assertUnprocessable();
    }
}
