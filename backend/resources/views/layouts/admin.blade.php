<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Portal') — Big Live Marketplace</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            900: '#0B1120',
                            800: '#0F172A',
                            700: '#1E293B',
                            600: '#334155',
                        },
                        brand: {
                            blue: '#2563EB',
                            light: '#3B82F6',
                            gold: '#D4AF37',
                            amber: '#F59E0B',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Sleek custom scrollbars */
        aside nav::-webkit-scrollbar {
            width: 5px;
        }
        aside nav::-webkit-scrollbar-track {
            background: transparent;
        }
        aside nav::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        aside nav::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
        main::-webkit-scrollbar {
            width: 8px;
        }
        main::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        main::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .active-nav-link {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.22) 0%, rgba(37, 99, 235, 0.05) 100%);
            border-left: 3px solid #3B82F6;
            color: #93C5FD !important;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased flex h-screen overflow-hidden">

    <!-- LEFT SIDEBAR -->
    <aside class="w-72 bg-navy-800 text-slate-300 flex flex-col flex-shrink-0 border-r border-navy-700 select-none z-30 transition-all duration-300 shadow-2xl">
        
        <!-- Logo & Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-navy-700/80 bg-navy-900/60 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Big Live Market" class="w-10 h-10 rounded-xl object-cover shadow-md shadow-blue-900/40 ring-1 ring-white/10 flex-shrink-0">
                <div>
                    <span class="text-white font-extrabold text-sm tracking-wide flex items-center gap-1.5">
                        BIG LIVE <span class="text-[9px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-brand-gold/20 text-brand-gold border border-brand-gold/30">ADMIN</span>
                    </span>
                    <p class="text-[10px] text-slate-400 font-medium">Enterprise Classifieds</p>
                </div>
            </a>
        </div>

        <!-- Navigation Menus (Scrollable) -->
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6 text-xs">
            
            <!-- SECTION 1: CORE -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Core Platform</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.dashboard') ? 'active-nav-link' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span class="text-xs font-semibold">Dashboard Overview</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 2: TAXONOMY & CATEGORIES -->
            <div>
                <div class="flex items-center justify-between px-3 mb-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Categories & Taxonomy</p>
                    <a href="{{ route('admin.categories') }}" class="text-[10px] text-brand-light hover:underline font-semibold">View All</a>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('admin.categories') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.categories*') && !request()->has('action') ? 'active-nav-link' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            <span class="text-xs font-semibold">Categories Explorer</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-navy-700 text-slate-300 border border-slate-600/40">Drilldown</span>
                    </a>

                    <!-- DIRECT LINK TO ADD PARENT CATEGORY -->
                    <a href="{{ route('admin.categories', ['action' => 'new_parent']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 transition font-semibold border border-emerald-500/25">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span class="text-xs">+ Add Parent Category</span>
                    </a>

                    <!-- Sub-category Shortcuts -->
                    <div class="pl-6 pr-1 py-1 space-y-1 border-l-2 border-navy-700/80 ml-4">
                        <a href="{{ route('admin.categories', ['parent_id' => 1]) }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-navy-700/40 transition">
                            <span class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM3 9l2-4h14l2 4M3 9h18v7a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                <span>Vehicles & Motors</span>
                            </span>
                            <span class="text-[9px] text-slate-500 font-mono">11 subs</span>
                        </a>
                        <a href="{{ route('admin.categories', ['parent_id' => 3]) }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-navy-700/40 transition">
                            <span class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <span>Mobile Phones</span>
                            </span>
                            <span class="text-[9px] text-slate-500 font-mono">7 subs</span>
                        </a>
                        <a href="{{ route('admin.categories', ['parent_id' => 5]) }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-navy-700/40 transition">
                            <span class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>Property & Plots</span>
                            </span>
                            <span class="text-[9px] text-slate-500 font-mono">6 subs</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: MARKETPLACE ADS -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Classified Ads</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.listings') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.listings') && !request()->has('status') ? 'active-nav-link' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span class="text-xs font-semibold">All Listings</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request('status') === 'pending_review' ? 'active-nav-link' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-semibold text-amber-200">Pending Review</span>
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Action</span>
                    </a>
                    <a href="{{ route('admin.listings', ['status' => 'active']) }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-navy-700/60 transition {{ request('status') === 'active' ? 'active-nav-link' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-xs font-medium">Live Active Ads</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 4: USERS & TRUST -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Users & Trust (KYC)</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.users*') ? 'active-nav-link' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-xs font-semibold">User Directory</span>
                    </a>
                    <a href="{{ route('admin.verifications') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.verifications*') ? 'active-nav-link' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span class="text-xs font-semibold">KYC Verification</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Trust</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 5: SPONSORED EARN PROGRAM -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Sponsored Earn</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.earn.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.earn.dashboard') ? 'active-nav-link' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-xs font-semibold">Earn Overview</span>
                    </a>
                    <a href="{{ route('admin.earn.campaigns') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.earn.campaigns*') ? 'active-nav-link' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        <span class="text-xs">Company Campaigns</span>
                    </a>
                    <a href="{{ route('admin.earn.submissions') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.earn.submissions*') ? 'active-nav-link' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="text-xs">Task Submissions</span>
                    </a>
                    <a href="{{ route('admin.earn.withdrawals') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.earn.withdrawals*') ? 'active-nav-link' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span class="text-xs">Review Withdrawals</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    </a>
                </div>
            </div>

            <!-- SECTION 6: SAFETY & MONETIZATION -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Safety & Billing</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.reports') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.reports*') ? 'active-nav-link' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                            <span class="text-xs font-semibold">Abuse Reports</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.monetization') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.monetization*') ? 'active-nav-link' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                        <span class="text-xs">Orders & Invoices</span>
                    </a>
                    <a href="{{ route('admin.audit-logs') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-navy-700/60 transition {{ request()->routeIs('admin.audit-logs*') ? 'active-nav-link' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0zM3 12a9 9 0 0115.364-6.364l1.636 1.636M21 3v5h-5"></path></svg>
                        <span class="text-xs">Audit Trail Logs</span>
                    </a>
                </div>
            </div>

        </nav>

        <!-- Sidebar User Footer -->
        <div class="p-3.5 border-t border-navy-700/80 bg-navy-900/70 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-slate-200 font-bold text-xs border border-slate-600 flex-shrink-0">
                    {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                </div>
                <div class="overflow-hidden min-w-0">
                    <p class="text-xs font-semibold text-white truncate max-w-[125px]">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] text-slate-400 truncate capitalize">{{ str_replace('_', ' ', Auth::user()->roles->pluck('name')->first() ?? 'Super Admin') }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit" title="Logout" class="p-2 rounded-lg hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-20 shadow-xs flex-shrink-0">
            <div class="flex items-center gap-4">
                <h1 class="text-base font-bold text-slate-800">@yield('page_title', 'Dashboard')</h1>
                <span class="text-slate-300">|</span>
                <p class="text-xs text-slate-500 hidden sm:block">@yield('page_subtitle', 'Big Live Marketplace Operations')</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>View Public Site</span>
                </a>
                <a href="/dashboard" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>User Portal</span>
                </a>
                <div class="h-6 w-px bg-slate-200"></div>
                <span class="text-xs text-slate-400 hidden md:block">{{ now()->format('D, M d, Y') }}</span>
            </div>
        </header>

        <!-- FLASH ALERTS -->
        @if(session('success'))
            <div class="mx-6 mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-base font-bold">×</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-base font-bold">×</button>
            </div>
        @endif

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-slate-100/60">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
