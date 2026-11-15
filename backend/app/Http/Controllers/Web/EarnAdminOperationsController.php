<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EarnAdminOperationsController extends Controller
{
    public function campaigns(Request $request): View
    {
        abort_unless($request->user()?->hasAnyRole(['admin', 'super_admin', 'finance']), 403);
        return view('admin.earn.campaigns', [
            'campaigns' => DB::table('sponsored_campaigns')->orderByDesc('id')->paginate(30),
        ]);
    }

    public function storeCampaign(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasAnyRole(['admin', 'super_admin']), 403);
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'currency' => ['required', 'in:PKR'],
        ]);
        DB::table('sponsored_campaigns')->insert($data + [
            'status' => 'draft',
            'received_minor' => 0,
            'reserved_minor' => 0,
            'spent_minor' => 0,
            'show_web' => true,
            'show_android' => true,
            'show_ios' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.earn.campaigns')->with('success', 'Draft campaign created. Finance verification is required before activation.');
    }

    public function verifyCampaign(Request $request, int $campaign): RedirectResponse
    {
        abort_unless($request->user()?->hasAnyRole(['finance', 'super_admin']), 403);
        $data = $request->validate([
            'received_minor' => ['required', 'integer', 'min:100'],
            'payment_reference' => ['required', 'string', 'max:200', 'unique:sponsored_campaigns,payment_reference'],
        ]);

        $updated = DB::table('sponsored_campaigns')
            ->where('id', $campaign)
            ->whereNull('payment_verified_at')
            ->update([
                'received_minor' => $data['received_minor'],
                'payment_reference' => $data['payment_reference'],
                'payment_verified_by' => $request->user()->id,
                'payment_verified_at' => now(),
                'status' => 'active',
                'updated_at' => now(),
            ]);

        abort_unless($updated, 409, 'Campaign already verified or not found');
        return redirect()->route('admin.earn.campaigns')->with('success', 'Payment verified and campaign activated successfully!');
    }

    public function withdrawals(Request $request): View
    {
        abort_unless($request->user()?->hasAnyRole(['finance', 'super_admin']), 403);
        return view('admin.earn.withdrawals', [
            'withdrawals' => DB::table('earning_withdrawals')
                ->whereIn('status', ['requested', 'transferred_pending_confirmation', 'paid', 'rejected'])
                ->orderByDesc('id')
                ->paginate(30),
        ]);
    }

    public function transferWithdrawal(Request $request, int $withdrawal): RedirectResponse
    {
        abort_unless($request->user()?->hasAnyRole(['finance', 'super_admin']), 403);
        $data = $request->validate([
            'transfer_reference' => ['required', 'string', 'max:200', 'unique:earning_withdrawals,transfer_reference'],
        ]);

        $changed = DB::table('earning_withdrawals')
            ->where('id', $withdrawal)
            ->where('status', 'requested')
            ->update([
                'status' => 'transferred_pending_confirmation',
                'processed_by' => $request->user()->id,
                'transfer_reference' => $data['transfer_reference'],
                'updated_at' => now(),
            ]);

        abort_unless($changed, 409, 'Withdrawal not awaiting transfer');
        return redirect()->route('admin.earn.withdrawals')->with('success', 'Transfer reference recorded. Awaiting second officer confirmation.');
    }

    public function confirmWithdrawal(Request $request, int $withdrawal): RedirectResponse
    {
        abort_unless($request->user()?->hasAnyRole(['finance', 'super_admin']), 403);

        DB::transaction(function () use ($request, $withdrawal) {
            $w = DB::table('earning_withdrawals')->where('id', $withdrawal)->lockForUpdate()->first();
            abort_unless($w && $w->status === 'transferred_pending_confirmation', 409, 'Not awaiting confirmation');
            abort_if((int) $w->processed_by === (int) $request->user()->id, 403, 'Second confirmation officer must be different from transfer officer');

            $wallet = DB::table('earning_wallets')->where('user_id', $w->user_id)->lockForUpdate()->first();
            abort_unless($wallet && $wallet->held_minor >= $w->amount_minor, 422, 'Held balance mismatch');

            DB::table('earning_wallets')->where('id', $wallet->id)->update([
                'held_minor' => $wallet->held_minor - $w->amount_minor,
                'updated_at' => now(),
            ]);

            DB::table('earning_withdrawals')->where('id', $withdrawal)->update([
                'status' => 'paid',
                'confirmed_by' => $request->user()->id,
                'updated_at' => now(),
            ]);

            DB::table('earning_ledger_entries')->insert([
                'user_id' => $w->user_id,
                'entry_type' => 'withdrawal_settled',
                'amount_minor' => 0,
                'reference_type' => 'withdrawal',
                'reference_id' => $w->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, 3);

        return redirect()->route('admin.earn.withdrawals')->with('success', 'Payout confirmed as settled and completed.');
    }

    public function rejectWithdrawal(Request $request, int $withdrawal): RedirectResponse
    {
        abort_unless($request->user()?->hasAnyRole(['finance', 'super_admin']), 403);
        $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);

        DB::transaction(function () use ($withdrawal) {
            $w = DB::table('earning_withdrawals')->where('id', $withdrawal)->lockForUpdate()->first();
            abort_unless($w && $w->status === 'requested', 409, 'Only untransferred requests can be rejected');

            $wallet = DB::table('earning_wallets')->where('user_id', $w->user_id)->lockForUpdate()->first();
            abort_unless($wallet && $wallet->held_minor >= $w->amount_minor, 422, 'Held balance mismatch');

            DB::table('earning_ledger_entries')->insert([
                'user_id' => $w->user_id,
                'entry_type' => 'withdrawal_released',
                'amount_minor' => $w->amount_minor,
                'reference_type' => 'withdrawal',
                'reference_id' => $w->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('earning_wallets')->where('id', $wallet->id)->update([
                'available_minor' => $wallet->available_minor + $w->amount_minor,
                'held_minor' => $wallet->held_minor - $w->amount_minor,
                'updated_at' => now(),
            ]);

            DB::table('earning_withdrawals')->where('id', $w->id)->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);
        }, 3);

        return redirect()->route('admin.earn.withdrawals')->with('success', 'Withdrawal request rejected. Held funds safely released back to user.');
    }

    public function submissions(Request $request): View
    {
        abort_unless($request->user()?->hasAnyRole(['admin', 'super_admin']), 403);
        return view('admin.earn.submissions', [
            'submissions' => DB::table('sponsored_task_submissions as s')
                ->join('sponsored_tasks as t', 't.id', '=', 's.task_id')
                ->join('sponsored_campaigns as c', 'c.id', '=', 't.campaign_id')
                ->select(
                    's.id',
                    's.user_id',
                    's.proof',
                    's.status',
                    's.created_at',
                    't.title as task_title',
                    't.reward_minor',
                    'c.company_name',
                    'c.currency'
                )
                ->where('s.status', 'pending')
                ->orderBy('s.id')
                ->paginate(30),
        ]);
    }

    public function reviewSubmission(Request $request, int $submission): RedirectResponse
    {
        abort_unless($request->user()?->hasAnyRole(['admin', 'super_admin']), 403);
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        DB::transaction(function () use ($submission, $data, $request) {
            $s = DB::table('sponsored_task_submissions')->where('id', $submission)->lockForUpdate()->first();
            abort_unless($s && $s->status === 'pending', 409, 'Already reviewed');

            $t = DB::table('sponsored_tasks')->where('id', $s->task_id)->lockForUpdate()->first();
            abort_unless($t, 404, 'Task not found');

            if ($data['decision'] === 'approved') {
                $c = DB::table('sponsored_campaigns')->where('id', $t->campaign_id)->lockForUpdate()->first();
                abort_unless($c && $c->payment_verified_at && $c->status === 'active' && $t->status === 'active', 422, 'Task campaign not active and funded');

                DB::table('sponsored_tasks')->where('id', $t->id)->update([
                    'approved_count' => $t->approved_count + 1,
                    'updated_at' => now(),
                ]);

                DB::table('earning_wallets')->insertOrIgnore([
                    'user_id' => $s->user_id,
                    'available_minor' => 0,
                    'held_minor' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('earning_wallets')->where('user_id', $s->user_id)->increment('available_minor', $t->reward_minor);

                DB::table('earning_ledger_entries')->insert([
                    'user_id' => $s->user_id,
                    'entry_type' => 'task_reward',
                    'amount_minor' => $t->reward_minor,
                    'reference_type' => 'submission',
                    'reference_id' => $s->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('sponsored_task_submissions')->where('id', $s->id)->update([
                'status' => $data['decision'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);
        }, 3);

        $msg = $data['decision'] === 'approved' ? 'Submission approved and reward credited to user wallet!' : 'Submission rejected.';
        return redirect()->route('admin.earn.submissions')->with('success', $msg);
    }
}
