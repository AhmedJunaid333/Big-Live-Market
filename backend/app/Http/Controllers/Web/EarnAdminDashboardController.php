<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarnAdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin', 'finance', 'moderator']), 403);

        $canManage = $request->user()->hasAnyRole(['super_admin', 'admin']);
        $canFinance = $request->user()->hasAnyRole(['super_admin', 'finance']);

        return view('admin.earn.dashboard', [
            'canManage' => $canManage,
            'canFinance' => $canFinance,
            'campaigns' => $canManage ? DB::table('sponsored_campaigns')->latest('id')->limit(25)->get() : collect(),
            'submissions' => $canManage ? DB::table('sponsored_task_submissions')
                ->join('sponsored_tasks', 'sponsored_tasks.id', '=', 'sponsored_task_submissions.task_id')
                ->select('sponsored_task_submissions.*', 'sponsored_tasks.title as task_title')
                ->where('sponsored_task_submissions.status', 'pending')
                ->orderBy('sponsored_task_submissions.id')->limit(25)->get() : collect(),
            'withdrawals' => $canFinance ? DB::table('earning_withdrawals')
                ->whereIn('status', ['requested', 'transferred_pending_confirmation'])
                ->latest('id')->limit(25)->get() : collect(),
        ]);
    }
}
