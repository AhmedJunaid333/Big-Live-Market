@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Platform Operations Overview')
@section('page_subtitle', 'Live analytics, moderation alerts, and marketplace health')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Ads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Live Active Ads</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($metrics['active_listings']) }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">Total: {{ number_format($metrics['total_listings']) }} ads</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Review</p>
                <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ number_format($metrics['pending_review']) }}</h3>
                <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="text-[11px] font-semibold text-brand-blue hover:underline mt-1 inline-block">Review Queue →</a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Total Users & KYC -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Users</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($metrics['total_users']) }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">{{ number_format($metrics['verified_users']) }} Verified Sellers</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- Categories & Subcategories -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Categories Tree</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $metrics['total_categories'] }} <span class="text-sm font-normal text-slate-400">Main</span></h3>
                <p class="text-[11px] text-slate-500 mt-1">{{ $metrics['total_subcategories'] }} Sub-categories</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS & DRILL-DOWN BANNER -->
    <div class="bg-gradient-to-r from-navy-800 to-navy-900 rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-6 border border-navy-700">
        <div>
            <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded bg-brand-gold/20 text-brand-gold border border-brand-gold/30">Taxonomy & Ads Navigator</span>
            <h2 class="text-xl font-bold mt-2">Manage Categories, Sub-categories & Review Queue</h2>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                Browse hierarchical categories, explore child subcategories with their attributes, or take instant moderation actions on new ads and payout requests.
            </p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('admin.categories') }}" class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-semibold shadow-md transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                <span>Open Categories Explorer</span>
            </a>
            <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span>Moderate Ads</span>
            </a>
        </div>
    </div>

    <!-- 2-COLUMN OPERATIONAL QUEUES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- PENDING ADS FOR APPROVAL -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Ads Awaiting Review
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Approve or reject seller listings before they go public</p>
                </div>
                <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="text-xs text-brand-blue font-semibold hover:underline">View All</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingListings as $listing)
                    <div class="p-4 hover:bg-slate-50 transition flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center text-slate-400 overflow-hidden">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 truncate">{{ $listing->title }}</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $listing->category?->name ?? 'Category' }} • PKR {{ number_format($listing->price_minor / 100) }} • By {{ $listing->seller?->name ?? 'Seller' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="reason" value="Rejected during review">
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <svg class="w-8 h-8 text-emerald-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        No ads pending review. All caught up!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- WITHDRAWALS & SPONSORED EARN QUEUE -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Pending Withdrawals (Payouts)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Sponsored earn payout requests from mobile members</p>
                </div>
                <a href="{{ route('admin.earn.withdrawals') }}" class="text-xs text-brand-blue font-semibold hover:underline">Manage Payouts</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentWithdrawals as $w)
                    <div class="p-4 hover:bg-slate-50 transition flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold text-slate-800">PKR {{ number_format($w->amount_minor / 100, 2) }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Method: <span class="capitalize">{{ $w->method }}</span> • User ID: #{{ $w->user_id }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 capitalize">
                            {{ str_replace('_', ' ', $w->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        No pending withdrawal requests.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
