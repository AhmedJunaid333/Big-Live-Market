@extends('layouts.admin')

@section('title', 'Sponsored Task Reviews')
@section('page_title', 'Sponsored Task Review Queue')
@section('page_subtitle', 'Review member task proofs; approving immediately credits the member earning wallet')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- SUB-NAV TABS -->
    <div class="flex flex-wrap gap-2 p-1.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs max-w-fit">
        <a href="{{ route('admin.earn.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Earn Overview
        </a>
        <a href="{{ route('admin.earn.campaigns') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Manage Campaigns
        </a>
        <a href="{{ route('admin.earn.submissions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-navy-800 text-white shadow-xs">
            Review Submissions
        </a>
        <a href="{{ route('admin.earn.withdrawals') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Review Withdrawals
        </a>
    </div>

    <!-- SUBMISSIONS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Task Submissions Awaiting Approval</h3>
            <span class="text-xs text-slate-400">Total: {{ $submissions->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">ID</th>
                        <th class="p-3.5 font-semibold">Company</th>
                        <th class="p-3.5 font-semibold">Task Title</th>
                        <th class="p-3.5 font-semibold">User</th>
                        <th class="p-3.5 font-semibold">Reward</th>
                        <th class="p-3.5 font-semibold">Proof Submitted</th>
                        <th class="p-3.5 font-semibold">Date</th>
                        <th class="p-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono text-slate-400">#{{ $s->id }}</td>
                            <td class="p-3.5 font-semibold text-slate-800">{{ $s->company_name }}</td>
                            <td class="p-3.5 text-slate-700">{{ $s->task_title }}</td>
                            <td class="p-3.5 font-mono text-slate-600">User #{{ $s->user_id }}</td>
                            <td class="p-3.5 font-bold text-emerald-600">PKR {{ number_format($s->reward_minor / 100, 2) }}</td>
                            <td class="p-3.5 max-w-xs">
                                <div class="bg-slate-50 rounded-lg p-2 border border-slate-200 text-slate-700 font-mono text-[11px] whitespace-pre-wrap break-words">
                                    {{ $s->proof }}
                                </div>
                            </td>
                            <td class="p-3.5 text-slate-400 text-[11px]">{{ $s->created_at }}</td>
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.earn.submissions.review', $s->id) }}" class="inline" onsubmit="return confirm('Approve submission and credit wallet?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="approve">
                                    <button class="px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-[11px] transition">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.earn.submissions.review', $s->id) }}" class="inline ml-1" onsubmit="return confirm('Reject submission?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="reject">
                                    <input type="hidden" name="reason" value="Verification proof does not match task criteria">
                                    <button class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] transition">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">No pending submissions awaiting review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
