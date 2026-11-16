@extends('layouts.admin')

@section('title', 'Withdrawal Payouts & Settlement')
@section('page_title', 'Withdrawals & Payouts Settlement')
@section('page_subtitle', 'Dual-control financial authorization for JazzCash, EasyPaisa, and bank transfers')

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
        <a href="{{ route('admin.earn.submissions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Review Submissions
        </a>
        <a href="{{ route('admin.earn.withdrawals') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-navy-800 text-white shadow-xs">
            Review Withdrawals
        </a>
    </div>

    <!-- WITHDRAWALS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Payout Requests Queue</h3>
            <span class="text-xs text-slate-400">Total: {{ $withdrawals->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">ID</th>
                        <th class="p-3.5 font-semibold">User</th>
                        <th class="p-3.5 font-semibold">Amount (PKR)</th>
                        <th class="p-3.5 font-semibold">Method</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold">Transfer Reference</th>
                        <th class="p-3.5 font-semibold text-right">Settlement Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono text-slate-400">#{{ $w->id }}</td>
                            <td class="p-3.5 font-mono text-slate-700">User #{{ $w->user_id }}</td>
                            <td class="p-3.5 font-extrabold text-slate-900">PKR {{ number_format($w->amount_minor / 100, 2) }}</td>
                            <td class="p-3.5 uppercase font-medium text-slate-600">{{ $w->method }}</td>
                            <td class="p-3.5">
                                @if($w->status === 'requested')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Awaiting Transfer</span>
                                @elseif($w->status === 'transferred_pending_confirmation')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-brand-blue border border-blue-200">Pending Confirmation</span>
                                @elseif($w->status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Settled & Paid</span>
                                @elseif($w->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Rejected & Refunded</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">{{ $w->status }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-slate-500 text-[11px]">{{ $w->transfer_reference ?: '—' }}</td>
                            <td class="p-3.5 text-right whitespace-nowrap">
                                @if($w->status === 'requested')
                                    <!-- Officer 1: Record transfer -->
                                    <form method="POST" action="{{ route('admin.earn.withdrawals.transfer', $w->id) }}" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <input type="text" name="transfer_reference" placeholder="Trx ID (JazzCash/Bank)" required maxlength="200" class="border text-[11px] rounded-lg px-2 py-1 w-36 border-slate-300">
                                        <button type="submit" class="bg-brand-blue hover:bg-brand-light text-white font-semibold text-[11px] px-2.5 py-1 rounded-lg transition">
                                            Record Trx
                                        </button>
                                    </form>

                                    <!-- Reject -->
                                    <form method="POST" action="{{ route('admin.earn.withdrawals.reject', $w->id) }}" class="inline ml-1" onsubmit="return confirm('Reject and refund balance?');">
                                        @csrf
                                        <input type="hidden" name="reason" value="Beneficiary account invalid or unverified">
                                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] px-2 py-1 rounded-lg transition">
                                            Reject
                                        </button>
                                    </form>
                                @elseif($w->status === 'transferred_pending_confirmation')
                                    <!-- Officer 2: Confirm settlement -->
                                    <form method="POST" action="{{ route('admin.earn.withdrawals.confirm', $w->id) }}" class="inline" onsubmit="return confirm('Confirm funds settlement?');">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] px-3 py-1 rounded-lg transition">
                                            ✓ Confirm Settle
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-[11px]">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">No withdrawals pending action.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
