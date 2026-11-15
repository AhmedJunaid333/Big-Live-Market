<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EarnAdminOperationsController extends Controller
{
    public function campaigns(Request $request): View
    {
        abort_unless($request->user()?->hasAnyRole(['admin', 'super_admin']), 403);
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
            'show_web' => false,
            'show_android' => false,
            'show_ios' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.earn.campaigns')->with('success', 'Draft campaign created. Finance verification is required before activation.');
    }

    public function withdrawals(Request $request): View
    {
        abort_unless($request->user()?->hasAnyRole(['finance', 'super_admin']), 403);
        return view('admin.earn.withdrawals', [
            'withdrawals' => DB::table('earning_withdrawals')
                ->whereIn('status', ['requested', 'transferred_pending_confirmation'])
                ->orderBy('id')
                ->paginate(30),
        ]);
    }
    public function submissions(Request $request): View
    {
        abort_unless($request->user()?->hasAnyRole(['admin', 'super_admin']), 403);
        return view('admin.earn.submissions', [
            'submissions' => DB::table('sponsored_task_submissions as s')
                ->join('sponsored_tasks as t', 't.id', '=', 's.task_id')
                ->join('sponsored_campaigns as c', 'c.id', '=', 't.campaign_id')
                ->select('s.id', 's.user_id', 's.proof', 's.created_at', 't.title as task_title',
                    't.reward_minor', 'c.company_name', 'c.currency')
                ->where('s.status', 'pending')
                ->orderBy('s.id')
                ->paginate(30),
        ]);
    }

}
