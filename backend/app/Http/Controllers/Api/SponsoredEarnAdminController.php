<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SponsoredEarnAdminController extends Controller
{
    private function authorizeRole(Request $request, array $roles): void
    {
        abort_unless($request->user()?->hasAnyRole($roles), 403);
    }

    public function createTask(Request $request, int $campaign): JsonResponse
    {
        $this->authorizeRole($request, ['admin', 'super_admin']);
        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'instructions' => ['required','string','max:5000'],
            'task_type' => ['required', Rule::in(['survey','honest_feedback','usability_test'])],
            'reward_minor' => ['required','integer','min:1','max:1000000000'],
            'max_participants' => ['required','integer','min:1','max:100000'],
        ]);
        $id = DB::transaction(function () use ($campaign, $data) {
            $c = DB::table('sponsored_campaigns')->where('id',$campaign)->lockForUpdate()->first();
            abort_unless($c && $c->payment_verified_at && in_array($c->status,['draft','active'],true),422,'Campaign is not funded');
            $budget = $data['reward_minor'] * $data['max_participants'];
            abort_unless($c->received_minor >= $c->reserved_minor && $c->received_minor - $c->reserved_minor >= $c->spent_minor, 422, 'Invalid campaign budget accounting');
            abort_unless($budget <= $c->received_minor - $c->reserved_minor - $c->spent_minor,422,'Insufficient funded campaign budget');
            DB::table('sponsored_campaigns')->where('id',$campaign)->update([
                'reserved_minor'=>$c->reserved_minor+$budget,'updated_at'=>now()
            ]);
            return DB::table('sponsored_tasks')->insertGetId($data+[
                'campaign_id'=>$campaign,'status'=>'draft','created_at'=>now(),'updated_at'=>now()
            ]);
        });
        return response()->json(['data'=>['id'=>$id,'status'=>'draft']],201);
    }

    public function setCampaignState(Request $request, int $campaign): JsonResponse
    {
        $this->authorizeRole($request,['admin','super_admin']);
        $data=$request->validate([
            'status'=>['required',Rule::in(['active','paused'])],
            'show_web'=>['required','boolean'],
            'show_android'=>['required','boolean'],
            'show_ios'=>['required','boolean'],
        ]);
        $c=DB::table('sponsored_campaigns')->where('id',$campaign)->first();
        abort_unless($c && $c->payment_verified_at,422,'Finance verification required');
        DB::table('sponsored_campaigns')->where('id',$campaign)->update($data+['updated_at'=>now()]);
        return response()->json(['message'=>'Campaign state updated']);
    }

    public function setTaskState(Request $request, int $task): JsonResponse
    {
        $this->authorizeRole($request,['admin','super_admin']);
        $data=$request->validate(['status'=>['required',Rule::in(['active','paused'])]]);
        $t=DB::table('sponsored_tasks')->where('id',$task)->first();
        abort_unless($t,404);
        $c=DB::table('sponsored_campaigns')->where('id',$t->campaign_id)->first();
        abort_unless($c && $c->payment_verified_at,422,'Finance verification required');
        DB::table('sponsored_tasks')->where('id',$task)->update($data+['updated_at'=>now()]);
        return response()->json(['message'=>'Task state updated']);
    }

    public function reviewSubmission(Request $request, int $submission): JsonResponse
    {
        $this->authorizeRole($request,['admin','super_admin']);
        $data=$request->validate(['decision'=>['required',Rule::in(['approved','rejected'])]]);
        DB::transaction(function () use ($submission,$data,$request) {
            $s=DB::table('sponsored_task_submissions')->where('id',$submission)->lockForUpdate()->first();
            abort_unless($s && $s->status==='pending',409,'Already reviewed');
            $t=DB::table('sponsored_tasks')->where('id',$s->task_id)->lockForUpdate()->first();
            abort_unless($t,404);
            if ($data['decision']==='approved') {
                $c=DB::table('sponsored_campaigns')->where('id',$t->campaign_id)->lockForUpdate()->first();
                abort_unless($c && $c->payment_verified_at && $c->status==='active' && $t->status==='active',422,'Task not active and funded');
                abort_unless($t->approved_count < $t->max_participants && $c->reserved_minor >= $t->reward_minor && $c->received_minor >= $c->reserved_minor && $c->received_minor - $c->reserved_minor >= $c->spent_minor,422,'No funded slots');
                DB::table('sponsored_campaigns')->where('id',$c->id)->update([
                    'reserved_minor'=>$c->reserved_minor-$t->reward_minor,
                    'spent_minor'=>$c->spent_minor+$t->reward_minor,'updated_at'=>now()
                ]);
                DB::table('sponsored_tasks')->where('id',$t->id)->update([
                    'approved_count'=>$t->approved_count+1,'updated_at'=>now()
                ]);
                DB::table('earning_wallets')->insertOrIgnore([
                    'user_id'=>$s->user_id,'available_minor'=>0,'held_minor'=>0,
                    'created_at'=>now(),'updated_at'=>now()
                ]);
                DB::table('earning_wallets')->where('user_id',$s->user_id)->increment('available_minor',$t->reward_minor);
                DB::table('earning_ledger_entries')->insert([
                    'user_id'=>$s->user_id,'entry_type'=>'task_reward','amount_minor'=>$t->reward_minor,
                    'reference_type'=>'submission','reference_id'=>$s->id,
                    'created_at'=>now(),'updated_at'=>now()
                ]);
            }
            DB::table('sponsored_task_submissions')->where('id',$s->id)->update([
                'status'=>$data['decision'],'reviewed_by'=>$request->user()->id,
                'reviewed_at'=>now(),'updated_at'=>now()
            ]);
        },3);
        return response()->json(['message'=>'Human review recorded']);
    }

    public function markTransferred(Request $request, int $withdrawal): JsonResponse
    {
        $this->authorizeRole($request,['finance','super_admin']);
        $data=$request->validate([
            'transfer_reference'=>['required','string','max:200','unique:earning_withdrawals,transfer_reference']
        ]);
        $changed=DB::table('earning_withdrawals')->where('id',$withdrawal)->where('status','requested')->update([
            'status'=>'transferred_pending_confirmation','processed_by'=>$request->user()->id,
            'transfer_reference'=>$data['transfer_reference'],'updated_at'=>now()
        ]);
        abort_unless($changed,409,'Withdrawal not awaiting transfer');
        return response()->json(['message'=>'Transfer recorded; independent confirmation required']);
    }

    public function rejectWithdrawal(Request $request, int $withdrawal): JsonResponse
    {
        $this->authorizeRole($request, ['finance', 'super_admin']);
        $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);

        DB::transaction(function () use ($withdrawal) {
            $w = DB::table('earning_withdrawals')->where('id', $withdrawal)->lockForUpdate()->first();
            abort_unless($w && $w->status === 'requested', 409, 'Only untransferred requests can be rejected');
            $wallet = DB::table('earning_wallets')->where('user_id', $w->user_id)->lockForUpdate()->first();
            abort_unless($wallet && $wallet->held_minor >= $w->amount_minor, 422, 'Held balance mismatch');

            DB::table('earning_ledger_entries')->insert([
                'user_id' => $w->user_id, 'entry_type' => 'withdrawal_released',
                'amount_minor' => $w->amount_minor, 'reference_type' => 'withdrawal',
                'reference_id' => $w->id, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('earning_wallets')->where('id', $wallet->id)->update([
                'available_minor' => $wallet->available_minor + $w->amount_minor,
                'held_minor' => $wallet->held_minor - $w->amount_minor, 'updated_at' => now(),
            ]);
            DB::table('earning_withdrawals')->where('id', $w->id)->update([
                'status' => 'rejected', 'updated_at' => now(),
            ]);
        }, 3);

        return response()->json(['message' => 'Untransferred withdrawal rejected; funds restored']);
    }

    public function confirmTransfer(Request $request, int $withdrawal): JsonResponse
    {
        $this->authorizeRole($request,['finance','super_admin']);
        DB::transaction(function () use ($request,$withdrawal) {
            $w=DB::table('earning_withdrawals')->where('id',$withdrawal)->lockForUpdate()->first();
            abort_unless($w && $w->status==='transferred_pending_confirmation',409,'Not awaiting confirmation');
            abort_if((int) $w->processed_by === (int) $request->user()->id,403,'Second approver must be different');
            $wallet=DB::table('earning_wallets')->where('user_id',$w->user_id)->lockForUpdate()->first();
            abort_unless($wallet && $wallet->held_minor >= $w->amount_minor,422,'Held balance mismatch');
            DB::table('earning_wallets')->where('id',$wallet->id)->update([
                'held_minor'=>$wallet->held_minor-$w->amount_minor,'updated_at'=>now()
            ]);
            DB::table('earning_withdrawals')->where('id',$withdrawal)->update([
                'status'=>'paid','confirmed_by'=>$request->user()->id,'updated_at'=>now()
            ]);
            DB::table('earning_ledger_entries')->insert([
                'user_id'=>$w->user_id,'entry_type'=>'withdrawal_settled',
                'amount_minor'=>0,'reference_type'=>'withdrawal','reference_id'=>$w->id,
                'created_at'=>now(),'updated_at'=>now()
            ]);
        },3);
        return response()->json(['message'=>'Withdrawal independently confirmed as paid']);
    }
}
