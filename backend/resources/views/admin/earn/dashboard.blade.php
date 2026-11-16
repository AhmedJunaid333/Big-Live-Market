@extends('layouts.admin')

@section('title', 'Sponsored Earn Admin')
@section('page_title', 'Sponsored Earn Administration')
@section('page_subtitle', 'Advertiser campaigns, user task submissions, and payouts management')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- SUB-NAV TABS -->
    <div class="flex flex-wrap gap-2 p-1.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs max-w-fit">
        <a href="{{ route('admin.earn.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-navy-800 text-white">
            Earn Overview
        </a>
        @if($canManage)
            <a href="{{ route('admin.earn.campaigns') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Manage Campaigns
            </a>
            <a href="{{ route('admin.earn.submissions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Review Submissions
            </a>
        @endif
        @if($canFinance)
            <a href="{{ route('admin.earn.withdrawals') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Review Withdrawals
            </a>
        @endif
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Campaigns Shown</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $campaigns->count() }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Advertiser funded campaigns</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Submissions</p>
            <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ $submissions->count() }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Proof verifications awaiting review</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Withdrawals</p>
            <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $withdrawals->count() }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Payouts awaiting transfer confirmation</p>
        </div>
    </div>

    @if($canManage)
        <!-- CAMPAIGNS PREVIEW -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm">Company Campaigns (Latest 25)</h3>
                <a href="{{ route('admin.earn.campaigns') }}" class="text-xs text-brand-blue font-semibold hover:underline">Full Campaign Manager →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3.5">ID</th>
                            <th class="p-3.5">Company</th>
                            <th class="p-3.5">Campaign Title</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Payment Verified</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($campaigns as $campaign)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5 font-mono text-slate-400">#{{ $campaign->id }}</td>
                                <td class="p-3.5 font-bold text-slate-900">{{ $campaign->company_name }}</td>
                                <td class="p-3.5 text-slate-700">{{ $campaign->title }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-brand-blue capitalize">{{ $campaign->status }}</span>
                                </td>
                                <td class="p-3.5">
                                    @if($campaign->payment_verified_at)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Verified</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">No campaigns yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SUBMISSIONS PREVIEW -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm">Pending Task Submissions (Oldest 25)</h3>
                <a href="{{ route('admin.earn.submissions') }}" class="text-xs text-brand-blue font-semibold hover:underline">Review All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3.5">ID</th>
                            <th class="p-3.5">Task Title</th>
                            <th class="p-3.5">User ID</th>
                            <th class="p-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5 font-mono text-slate-400">#{{ $submission->id }}</td>
                                <td class="p-3.5 font-semibold text-slate-800">{{ $submission->task_title }}</td>
                                <td class="p-3.5 text-slate-500">User #{{ $submission->user_id }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 capitalize">{{ $submission->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">No pending submissions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($canFinance)
        <!-- WITHDRAWALS PREVIEW -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm">Manual Withdrawals (Latest 25)</h3>
                <a href="{{ route('admin.earn.withdrawals') }}" class="text-xs text-brand-blue font-semibold hover:underline">Process Payouts →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3.5">ID</th>
                            <th class="p-3.5">User ID</th>
                            <th class="p-3.5">Amount (PKR)</th>
                            <th class="p-3.5">Method</th>
                            <th class="p-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($withdrawals as $withdrawal)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5 font-mono text-slate-400">#{{ $withdrawal->id }}</td>
                                <td class="p-3.5 text-slate-700">User #{{ $withdrawal->user_id }}</td>
                                <td class="p-3.5 font-bold text-slate-900">PKR {{ number_format($withdrawal->amount_minor / 100, 2) }}</td>
                                <td class="p-3.5 uppercase font-medium text-slate-600">{{ $withdrawal->method }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 capitalize">{{ str_replace('_', ' ', $withdrawal->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">No withdrawals awaiting action.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
