@extends('layouts.admin')

@section('title', 'Company Campaigns')
@section('page_title', 'Sponsored Campaigns Management')
@section('page_subtitle', 'Create company campaigns and verify advertiser deposit slips for activation')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- SUB-NAV TABS -->
    <div class="flex flex-wrap gap-2 p-1.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs max-w-fit">
        <a href="{{ route('admin.earn.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Earn Overview
        </a>
        <a href="{{ route('admin.earn.campaigns') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-navy-800 text-white shadow-xs">
            Manage Campaigns
        </a>
        <a href="{{ route('admin.earn.submissions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Review Submissions
        </a>
        <a href="{{ route('admin.earn.withdrawals') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            Review Withdrawals
        </a>
    </div>

    <!-- CREATE CAMPAIGN FORM -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <h3 class="text-sm font-bold text-slate-800 mb-1">Create New Sponsored Campaign</h3>
        <p class="text-xs text-slate-400 mb-4">Set up an advertiser campaign. Campaigns remain draft until Finance confirms payment receipt.</p>

        <form method="POST" action="{{ route('admin.earn.campaigns.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Company Name</label>
                <input name="company_name" maxlength="255" required value="{{ old('company_name') }}" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue" placeholder="e.g. Toyota Motors Pakistan">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Campaign Title</label>
                <input name="title" maxlength="255" required value="{{ old('title') }}" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue" placeholder="e.g. Corolla Cross Launch Promotion">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description & Tasks</label>
                <textarea name="description" maxlength="5000" rows="2" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue" placeholder="Sponsored campaign details and engagement instructions">{{ old('description') }}</textarea>
            </div>
            <input type="hidden" name="currency" value="PKR">
            <div class="md:col-span-2">
                <button class="px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold shadow-xs transition" type="submit">
                    Create Campaign Draft
                </button>
            </div>
        </form>
    </div>

    <!-- CAMPAIGNS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">All Registered Campaigns</h3>
            <span class="text-xs text-slate-400">Total: {{ $campaigns->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">ID</th>
                        <th class="p-3.5 font-semibold">Company</th>
                        <th class="p-3.5 font-semibold">Title</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold">Received</th>
                        <th class="p-3.5 font-semibold">Payment Reference</th>
                        <th class="p-3.5 font-semibold text-right">Verification & Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono text-slate-400">#{{ $c->id }}</td>
                            <td class="p-3.5 font-bold text-slate-900">{{ $c->company_name }}</td>
                            <td class="p-3.5 text-slate-700">{{ $c->title }}</td>
                            <td class="p-3.5">
                                @if($c->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                @elseif($c->status === 'draft')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Draft (Unfunded)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 capitalize">{{ $c->status }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-semibold text-slate-900">PKR {{ number_format($c->received_minor / 100, 2) }}</td>
                            <td class="p-3.5 font-mono text-slate-500 text-[11px]">{{ $c->payment_reference ?: '—' }}</td>
                            <td class="p-3.5 text-right whitespace-nowrap">
                                @if(!$c->payment_verified_at)
                                    <form method="POST" action="{{ route('admin.earn.campaigns.verify', $c->id) }}" class="inline-flex items-center gap-1.5" onsubmit="return confirm('Verify payment receipt and activate campaign?');">
                                        @csrf
                                        <input type="number" name="received_minor" placeholder="Amount (Cents)" required min="100" class="border text-[11px] rounded-lg px-2 py-1 w-28 border-slate-300">
                                        <input type="text" name="payment_reference" placeholder="Slip Ref" required maxlength="200" class="border text-[11px] rounded-lg px-2 py-1 w-28 border-slate-300">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] px-2.5 py-1 rounded-lg transition">
                                            Verify
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] font-semibold text-emerald-600 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">check_circle</span> Verified
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">No campaigns registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
