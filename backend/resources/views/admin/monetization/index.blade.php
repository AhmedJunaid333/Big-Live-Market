@extends('layouts.admin')

@section('title', 'Orders & Monetization')
@section('page_title', 'Monetization, Invoices & Orders')
@section('page_subtitle', 'Business subscriptions, paid featured bumps, and billing ledger')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- REVENUE KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Platform Orders</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">PKR {{ number_format($totalRevenueMinor / 100, 2) }}</h3>
            <p class="text-[11px] text-emerald-600 font-semibold mt-1">Paid monetization ledger</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Paid Order Count</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($orders->total()) }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Subscriptions & Featured Bumps</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Currency Base</p>
            <h3 class="text-2xl font-extrabold text-brand-blue mt-1">PKR (Pakistani Rupee)</h3>
            <p class="text-[11px] text-slate-400 mt-1">Configured for Pakistan</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Monetization Orders History</h3>
            <span class="text-xs text-slate-400">Total: {{ $orders->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">Order ID</th>
                        <th class="p-3.5 font-semibold">User / Business</th>
                        <th class="p-3.5 font-semibold">Item / Plan</th>
                        <th class="p-3.5 font-semibold">Amount</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono text-[11px] text-slate-700">#{{ $o->id }}</td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-800 block">{{ $o->user?->name ?? 'User #' . $o->user_id }}</span>
                                <span class="text-[10px] text-slate-400">{{ $o->user?->email }}</span>
                            </td>
                            <td class="p-3.5 text-slate-700 capitalize">{{ $o->product_type ?? 'Featured Promotion' }}</td>
                            <td class="p-3.5 font-extrabold text-slate-900">PKR {{ number_format($o->amount_minor / 100, 2) }}</td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $o->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} capitalize">
                                    {{ $o->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-400">{{ $o->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">No monetization orders recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
