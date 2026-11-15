<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class SponsoredEarnController extends Controller
{
    private function requireRole(Request $request, array $roles): void
    {
        abort_unless($request->user()?->hasAnyRole($roles), 403);
    }

    public function missions(Request $request): JsonResponse
    {
        $platform = $request->validate(['platform' => ['required', Rule::in(['web','android','ios'])]])['platform'];
        $tasks = DB::table('sponsored_tasks as t')
            ->join('sponsored_campaigns as c', 'c.id', '=', 't.campaign_id')
            ->where('t.status', 'active')->where('c.status', 'active')
            ->where('c.payment_verified_at', '!=', null)
            ->where('c.show_'.$platform, true)
            ->whereRaw('c.received_minor >= c.reserved_minor + c.spent_minor')
            ->whereColumn('t.approved_count', '<', 't.max_participants')
            ->where(function ($q) { $q->whereNull('c.starts_at')->orWhere('c.starts_at', '<=', now()); })
            ->where(function ($q) { $q->whereNull('c.ends_at')->orWhere('c.ends_at', '>=', now()); })
            ->select('t.id','t.title','t.instructions','t.task_type','t.reward_minor','c.currency')
            ->get();
        return response()->json(['data' => $tasks, 'message' => $tasks->isEmpty() ? 'No paid missions available.' : null]);
    }

    public function submit(Request $request, int $task): JsonResponse
    {
        $data = $request->validate(['proof' => ['required','string','min:10','max:5000']]);
        $active = DB::table('sponsored_tasks as t')
            ->join('sponsored_campaigns as c','c.id','=','t.campaign_id')
            ->where('t.id',$task)->where('t.status','active')->where('c.status','active')
            ->whereNotNull('c.payment_verified_at')->exists();
        abort_unless($active, 422, 'Task is not available');
        $id = DB::table('sponsored_task_submissions')->insertOrIgnore([
            'task_id'=>$task,'user_id'=>$request->user()->id,'proof'=>$data['proof'],
            'status'=>'pending','created_at'=>now(),'updated_at'=>now(),
        ]);
        return response()->json(['message'=>$id ? 'Submitted for human review' : 'Already submitted'], $id ? 201 : 200);
    }

    public function wallet(Request $request): JsonResponse
    {
        $wallet = DB::table('earning_wallets')->where('user_id',$request->user()->id)->first();
        return response()->json(['data'=>['available_minor'=>$wallet?->available_minor ?? 0,'held_minor'=>$wallet?->held_minor ?? 0,'currency'=>'PKR']]);
    }

    public function requestWithdrawal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount_minor'=>['required','integer','min:100'],
            'method'=>['required',Rule::in(['jazzcash','easypaisa','bank'])],
            'beneficiary'=>['required','string','min:5','max:500'],
        ]);
        $withdrawal = DB::transaction(function () use ($request,$data) {
            $wallet = DB::table('earning_wallets')->where('user_id',$request->user()->id)->lockForUpdate()->first();
            abort_unless($wallet && $wallet->available_minor >= $data['amount_minor'],422,'Insufficient available earnings');
            DB::table('earning_wallets')->where('id',$wallet->id)->update([
                'available_minor'=>$wallet->available_minor-$data['amount_minor'],
                'held_minor'=>$wallet->held_minor+$data['amount_minor'],'updated_at'=>now(),
            ]);
            $id = DB::table('earning_withdrawals')->insertGetId([
                'user_id'=>$request->user()->id,'amount_minor'=>$data['amount_minor'],
                'method'=>$data['method'],'beneficiary_encrypted'=>Crypt::encryptString($data['beneficiary']),
                'status'=>'requested','created_at'=>now(),'updated_at'=>now(),
            ]);
            DB::table('earning_ledger_entries')->insert([
                'user_id'=>$request->user()->id,'entry_type'=>'withdrawal_hold',
                'amount_minor'=>-$data['amount_minor'],'reference_type'=>'withdrawal',
                'reference_id'=>$id,'created_at'=>now(),'updated_at'=>now(),
            ]);
            return $id;
        });
        return response()->json(['data'=>['id'=>$withdrawal,'status'=>'requested'],'message'=>'Manual withdrawal review pending'],201);
    }

    public function createCampaign(Request $request): JsonResponse
    {
        $this->requireRole($request,['admin','super_admin']);
        $data = $request->validate([
            'company_name'=>['required','string','max:200'],
            'title'=>['required','string','max:200'],
            'description'=>['nullable','string','max:5000'],
            'received_minor'=>['prohibited'],
        ]);
        $id = DB::table('sponsored_campaigns')->insertGetId($data+['status'=>'draft','created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['data'=>['id'=>$id,'status'=>'draft']],201);
    }

    public function verifyPayment(Request $request, int $campaign): JsonResponse
    {
        $this->requireRole($request,['finance','super_admin']);
        $data = $request->validate([
            'received_minor'=>['required','integer','min:1'],
            'payment_reference'=>['required','string','max:200'],
        ]);
        $updated = DB::table('sponsored_campaigns')->where('id',$campaign)
            ->whereNull('payment_verified_at')->where('status','draft')
            ->update($data+['payment_verified_by'=>$request->user()->id,'payment_verified_at'=>now(),'updated_at'=>now()]);
        abort_unless($updated,409,'Campaign already verified or not in draft');
        return response()->json(['message'=>'Payment manually marked verified; publish separately after review']);
    }
}
