@extends('layouts.admin')

@section('title', 'Admin Operations Dashboard')
@section('page_title', 'Platform Operations Command Center')
@section('page_subtitle', 'Live classifieds telemetry, ad moderation queues, payout settlements, and taxonomy management')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto pb-12">

    <!-- 1. TOP COMMAND BAR & REALISTIC TACTILE ACTIONS -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-blue to-cyan-500 p-0.5 shadow-md flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Big Live" class="w-full h-full object-cover rounded-[14px]">
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">System Status: Active</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Live
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Logged in as <strong>{{ auth()->user()->name }}</strong> (Super Administrator)</p>
            </div>
        </div>

        <!-- REALISTIC 3D TACTILE ACTION BUTTONS ROW -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.categories') }}" class="btn-real btn-real-emerald px-4 py-2.5 text-xs shadow-md">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Add Parent Category</span>
            </a>

            <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="btn-real btn-real-blue px-4 py-2.5 text-xs shadow-md">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span>Review Ads</span>
                @if($metrics['pending_review'] > 0)
                    <span class="ml-1.5 px-1.5 py-0.2 rounded-full bg-white text-brand-blue text-[10px] font-black">{{ $metrics['pending_review'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.earn.withdrawals') }}" class="btn-real btn-real-amber px-4 py-2.5 text-xs shadow-md">
                <svg class="w-4 h-4 mr-1.5 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Payouts</span>
                @if($metrics['pending_withdrawals'] > 0)
                    <span class="ml-1.5 px-1.5 py-0.2 rounded-full bg-amber-900 text-amber-200 text-[10px] font-black">{{ $metrics['pending_withdrawals'] }}</span>
                @endif
            </a>

            <a href="/" target="_blank" class="btn-real btn-real-light px-3.5 py-2.5 text-xs shadow-sm">
                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Live Marketplace</span>
            </a>
        </div>
    </div>

    <!-- 2. HIGH-IMPACT KPI TELEMETRY GRID (6 CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        
        <!-- 1. Live Active Ads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Active Ads</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900">{{ number_format($metrics['active_listings']) }}</h3>
            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                <span>Total: {{ number_format($metrics['total_listings']) }}</span>
                <span class="text-emerald-600 font-bold">100% Live</span>
            </div>
        </div>

        <!-- 2. Pending Review (Actionable) -->
        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-amber-500/10 rounded-bl-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Needs Review</span>
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-amber-600">{{ number_format($metrics['pending_review']) }}</h3>
            <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="mt-2 text-[11px] font-bold text-amber-800 hover:underline flex items-center gap-1">
                <span>Open Queue</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- 3. Total Users & Verified KYC -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Users</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900">{{ number_format($metrics['total_users']) }}</h3>
            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                <span>{{ number_format($metrics['verified_users']) }} Verified</span>
                <a href="{{ route('admin.users') }}" class="text-brand-blue font-bold hover:underline">Directory</a>
            </div>
        </div>

        <!-- 4. Categories & Subcategories Tree -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Taxonomy Tree</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900">{{ $metrics['total_categories'] }} <span class="text-xs font-normal text-slate-400">Roots</span></h3>
            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                <span>{{ $metrics['total_subcategories'] }} Sub-types</span>
                <a href="{{ route('admin.categories') }}" class="text-purple-600 font-bold hover:underline">Explore</a>
            </div>
        </div>

        <!-- 5. Sponsored Earn Campaigns & Withdrawals -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Sponsored Earn</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900">{{ $metrics['pending_withdrawals'] }} <span class="text-xs font-normal text-slate-400">Payouts</span></h3>
            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                <span>{{ $metrics['active_campaigns'] }} Campaigns</span>
                <a href="{{ route('admin.earn') }}" class="text-amber-600 font-bold hover:underline">Ledger</a>
            </div>
        </div>

        <!-- 6. Safety & Reports Queue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Trust & Reports</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black {{ $metrics['open_reports'] > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $metrics['open_reports'] }}</h3>
            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                <span>{{ $metrics['pending_kyc'] }} KYC Queued</span>
                <a href="{{ route('admin.reports') }}" class="text-rose-600 font-bold hover:underline">Review</a>
            </div>
        </div>

    </div>

    <!-- 3. COMMAND CENTER 4-PANEL QUICK ACTIONS WITH REALISTIC TACTILE BUTTONS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Panel 1: Taxonomy Command -->
        <div class="bg-gradient-to-b from-white to-slate-50/60 p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center mb-3 border border-purple-100 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Categories & Schemas</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Manage parent root categories, dynamic attributes, filters, and custom fields.</p>
            </div>
            <div class="mt-5 space-y-2">
                <a href="{{ route('admin.categories') }}" class="w-full btn-real btn-real-emerald py-2 text-xs">
                    <span>+ Add Parent Category</span>
                </a>
                <a href="{{ route('admin.categories') }}" class="w-full btn-real btn-real-light py-2 text-xs">
                    <span>Explore Hierarchy Tree →</span>
                </a>
            </div>
        </div>

        <!-- Panel 2: Ads Moderation Queue -->
        <div class="bg-gradient-to-b from-white to-slate-50/60 p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center mb-3 border border-blue-100 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Adverts Moderation</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Inspect submitted ads, approve high quality items, or reject fraudulent submissions.</p>
            </div>
            <div class="mt-5 space-y-2">
                <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="w-full btn-real btn-real-blue py-2 text-xs">
                    <span>Review Pending Ads ({{ $metrics['pending_review'] }})</span>
                </a>
                <a href="{{ route('admin.listings') }}" class="w-full btn-real btn-real-light py-2 text-xs">
                    <span>All Published Ads →</span>
                </a>
            </div>
        </div>

        <!-- Panel 3: Sponsored Earn & Payouts -->
        <div class="bg-gradient-to-b from-white to-slate-50/60 p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3 border border-amber-100 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Payouts & Wallets</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Review member withdrawal requests for Easypaisa & JazzCash settlement.</p>
            </div>
            <div class="mt-5 space-y-2">
                <a href="{{ route('admin.earn.withdrawals') }}" class="w-full btn-real btn-real-amber py-2 text-xs">
                    <span>Process Withdrawals ({{ $metrics['pending_withdrawals'] }})</span>
                </a>
                <a href="{{ route('admin.earn.campaigns') }}" class="w-full btn-real btn-real-light py-2 text-xs">
                    <span>Manage Campaigns →</span>
                </a>
            </div>
        </div>

        <!-- Panel 4: Users & KYC Verification -->
        <div class="bg-gradient-to-b from-white to-slate-50/60 p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 border border-emerald-100 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Identity & Trust</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Approve seller identity documents, award verified badges, and audit user permissions.</p>
            </div>
            <div class="mt-5 space-y-2">
                <a href="{{ route('admin.verifications') }}" class="w-full btn-real btn-real-purple py-2 text-xs">
                    <span>Review KYC Queue ({{ $metrics['pending_kyc'] }})</span>
                </a>
                <a href="{{ route('admin.users') }}" class="w-full btn-real btn-real-light py-2 text-xs">
                    <span>User Directory ({{ $metrics['total_users'] }}) →</span>
                </a>
            </div>
        </div>

    </div>

    <!-- 4. OPERATIONAL WORKFLOW QUEUES (2 EQUAL COLUMNS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: ADS PENDING MODERATION (7 COLS) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <h3 class="font-extrabold text-slate-900 text-sm">Classified Ads Awaiting Moderation</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Quickly verify and approve items submitted by sellers</p>
                    </div>
                    <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="btn-real btn-real-light px-3 py-1.5 text-xs font-bold">
                        <span>View All Queue</span>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($pendingListings as $listing)
                        <div class="p-4 hover:bg-slate-50/80 transition flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center text-slate-400 overflow-hidden shadow-xs">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-extrabold text-slate-900 truncate" title="{{ $listing->title }}">{{ $listing->title }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <span class="font-bold text-brand-blue">{{ $listing->category?->name ?? 'General' }}</span>
                                        <span>•</span>
                                        <span class="font-extrabold text-slate-800">PKR {{ number_format($listing->price_minor / 100) }}</span>
                                        <span>•</span>
                                        <span class="text-slate-400">by {{ $listing->seller?->name ?? 'User #' . $listing->seller_id }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- REALISTIC TACTILE APPROVE & REJECT BUTTONS -->
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-real btn-real-emerald px-3.5 py-1.5 text-xs">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Approve</span>
                                    </button>
                                </form>

                                <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="reason" value="Rejected by administrator review">
                                    <button type="submit" class="btn-real btn-real-rose px-3.5 py-1.5 text-xs">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        <span>Reject</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 text-xs">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-2 border border-emerald-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-bold text-slate-700 block text-sm">All Ads Reviewed!</span>
                            <span class="text-slate-400 mt-1 block">There are no pending seller ads waiting in the review queue.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500">
                <span>Total pending ads awaiting action: <strong>{{ $metrics['pending_review'] }}</strong></span>
                <a href="{{ route('admin.listings') }}" class="text-brand-blue font-bold hover:underline">Manage All Listings →</a>
            </div>
        </div>

        <!-- RIGHT COLUMN: SPONSORED WITHDRAWALS & PAYOUTS (5 COLS) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="font-extrabold text-slate-900 text-sm">Sponsored Payout Requests</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Easypaisa & JazzCash reward settlement</p>
                    </div>
                    <a href="{{ route('admin.earn.withdrawals') }}" class="btn-real btn-real-amber px-3 py-1.5 text-xs font-bold">
                        <span>Process Payouts</span>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentWithdrawals as $w)
                        <div class="p-4 hover:bg-slate-50/80 transition flex items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-900 text-sm">PKR {{ number_format($w->amount_minor / 100, 2) }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 uppercase border border-slate-200">
                                        {{ $w->method }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">User #{{ $w->user_id }} • ID: #{{ $w->id }}</p>
                            </div>

                            <a href="{{ route('admin.earn.withdrawals') }}" class="btn-real btn-real-light px-3 py-1.5 text-[11px] font-bold">
                                <span>Inspect</span>
                            </a>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 text-xs">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-2 border border-amber-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="font-bold text-slate-700 block text-sm">Zero Pending Payouts</span>
                            <span class="text-slate-400 mt-1 block">All withdrawal requests have been settled.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500">
                <span>Active campaigns: <strong>{{ $metrics['active_campaigns'] }}</strong></span>
                <a href="{{ route('admin.earn') }}" class="text-amber-700 font-bold hover:underline">Open Earn Dashboard →</a>
            </div>
        </div>

    </div>

    <!-- 5. REAL-TIME AUDIT TRAIL STREAM & SAFETY LOGS -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">System Audit Trail & Security Logs</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Real-time immutable administrative and moderation event ledger</p>
                </div>
            </div>
            <a href="{{ route('admin.audit-logs') }}" class="btn-real btn-real-light px-3.5 py-1.5 text-xs font-bold">
                <span>View Full Audit Log →</span>
            </a>
        </div>

        <div class="divide-y divide-slate-100 text-xs">
            @forelse($recentAudits as $log)
                <div class="p-4 hover:bg-slate-50 transition flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                        <div>
                            <span class="font-extrabold text-slate-800 capitalize">{{ str_replace('_', ' ', $log->action) }}</span>
                            <span class="text-slate-400 ml-1">on {{ $log->subject_type }} #{{ $log->subject_id }}</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">
                                Actor: <strong class="text-slate-700">{{ $log->actor?->name ?? 'System Automated' }}</strong>
                            </span>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono flex-shrink-0">
                        {{ $log->created_at?->diffForHumans() ?? 'Recent' }}
                    </span>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400">
                    No recent audit events recorded.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
