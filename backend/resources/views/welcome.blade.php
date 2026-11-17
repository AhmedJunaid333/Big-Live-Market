<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Big Live Marketplace — Pakistan's Premier Classifieds & Verified Earning Hub</title>
    <meta name="description" content="Discover verified luxury cars, real estate, smartphones, electronics, and earn cash through brand tasks on Pakistan's leading classifieds platform.">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with Custom Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#2563EB',
                            light: '#3B82F6',
                            dark: '#1D4ED8',
                            navy: '#0B1120',
                            card: '#0F172A',
                        },
                        gold: {
                            50: '#FDFBF0',
                            100: '#FAF4D3',
                            200: '#F5E69F',
                            300: '#EFD468',
                            400: '#E6BD35',
                            500: '#D4AF37',
                            600: '#B59026',
                            700: '#8A6A18',
                        }
                    },
                    boxShadow: {
                        'glow-gold': '0 4px 24px -2px rgba(212, 175, 55, 0.35)',
                        'glow-blue': '0 4px 24px -2px rgba(37, 99, 235, 0.35)',
                        'card-hover': '0 20px 35px -8px rgba(15, 23, 42, 0.08), 0 8px 16px -4px rgba(15, 23, 42, 0.03)',
                    }
                }
            }
        }
    </script>

    <style>
        .gold-gradient-bg {
            background: linear-gradient(135deg, #F5E69F 0%, #D4AF37 50%, #B59026 100%);
        }
        .gold-text-shimmer {
            background: linear-gradient(135deg, #EFD468 0%, #D4AF37 50%, #FAF4D3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-mesh {
            background-color: #090D16;
            background-image: 
                radial-gradient(at 15% 15%, rgba(37, 99, 235, 0.22) 0px, transparent 50%),
                radial-gradient(at 85% 20%, rgba(212, 175, 55, 0.18) 0px, transparent 45%),
                radial-gradient(at 50% 90%, rgba(37, 99, 235, 0.15) 0px, transparent 50%);
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen selection:bg-gold-500 selection:text-slate-900">

    <!-- 1. TOP ANNOUNCEMENT & UTILITY BAR -->
    <div class="bg-brand-navy border-b border-slate-800 text-slate-300 text-xs py-2 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-slate-400">Pakistan's Verified Ecosystem:</span>
                <span class="font-semibold text-white">100% Biometric KYC Verified Sellers & Escrow Safe Transactions</span>
            </div>
            <div class="flex items-center gap-5 text-[11px] font-medium">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 hover:bg-blue-500/30 transition">
                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Admin Portal</span>
                </a>
                <a href="{{ route('admin.earn') }}" class="text-amber-300 hover:text-amber-200 font-bold transition flex items-center gap-1">
                    <span>💰 Earn with Tasks</span>
                </a>
                <span class="text-slate-600">|</span>
                <a href="#download-app" class="hover:text-white transition">Get Mobile App</a>
            </div>
        </div>
    </div>

    <!-- 2. STICKY LUXURY HEADER -->
    <header class="bg-brand-navy/95 backdrop-blur-md text-white sticky top-0 z-50 border-b border-slate-800/90 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4 sm:gap-6">
                
                <!-- Brand Crest -->
                <a href="/" class="flex items-center gap-3 group flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Big Live Market" class="w-12 h-12 rounded-2xl object-cover shadow-glow-gold group-hover:scale-105 transition-transform duration-300 ring-2 ring-amber-400/40 flex-shrink-0">
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold tracking-tight text-white flex items-center gap-1">
                            BIG LIVE <span class="gold-text-shimmer font-serif italic text-2xl font-normal">Market</span>
                        </span>
                        <span class="text-[9px] text-amber-300/80 uppercase tracking-widest font-bold">Classifieds & Rewards</span>
                    </div>
                </a>

                <!-- Quick Location & Search Header Bar -->
                <div class="hidden lg:flex flex-1 max-w-xl mx-4">
                    <form action="/search" method="GET" class="w-full flex items-center bg-slate-900/90 p-1.5 rounded-2xl border border-slate-700/80 focus-within:border-brand-blue focus-within:ring-2 focus-within:ring-brand-blue/20 transition duration-300 shadow-inner">
                        <div class="flex items-center px-3 border-r border-slate-800 text-xs text-slate-300 gap-1.5 min-w-[130px]">
                            <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <select name="city_id" class="bg-transparent text-xs text-white focus:outline-none cursor-pointer w-full">
                                <option value="" class="bg-slate-900 text-white">All Pakistan</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" class="bg-slate-900 text-white">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" name="q" placeholder="Search cars, phones, property, jobs..." 
                               class="flex-1 bg-transparent px-3.5 text-xs text-white placeholder-slate-400 focus:outline-none">
                        <button type="submit" class="bg-brand-blue hover:bg-brand-light text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span>Search</span>
                        </button>
                    </form>
                </div>

                <!-- User & Post Ad Actions -->
                <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                    @auth
                        <div class="relative group">
                            <a href="/dashboard" class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-white text-xs font-semibold border border-slate-700 transition">
                                <div class="w-6 h-6 rounded-lg bg-brand-blue flex items-center justify-center text-white font-bold text-xs uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </a>
                            <!-- Dropdown -->
                            <div class="absolute right-0 mt-2 w-48 bg-slate-900 rounded-xl shadow-xl border border-slate-800 py-1.5 hidden group-hover:block z-50">
                                <a href="/dashboard" class="block px-4 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white">User Dashboard</a>
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-xs text-blue-400 hover:bg-slate-800 hover:text-blue-300 font-bold">Admin Portal</a>
                                <div class="border-t border-slate-800 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-400 hover:bg-slate-800 hover:text-rose-300">Sign Out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/login" class="text-xs font-bold text-slate-200 hover:text-white px-3 py-2 transition tracking-wide">
                            Sign In
                        </a>
                        <a href="/register" class="hidden sm:inline-block text-xs font-bold text-amber-300 hover:text-amber-200 px-3.5 py-2 rounded-xl border border-amber-500/30 hover:border-amber-400 transition">
                            Register
                        </a>
                    @endauth

                    <!-- Post Ad Glowing Button -->
                    <a href="/sell" class="gold-gradient-bg hover:opacity-95 text-slate-900 font-black px-4 sm:px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-glow-gold flex items-center gap-1.5 hover:scale-[1.03] transition-all duration-200 flex-shrink-0">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Post Ad</span>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- 3. SECONDARY HORIZONTAL CATEGORIES STRIP -->
    <nav class="bg-white border-b border-slate-200/80 shadow-xs overflow-x-auto custom-scrollbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-6 sm:gap-8 text-xs font-bold uppercase tracking-wider whitespace-nowrap text-slate-600">
            <a href="/" class="text-brand-blue border-b-2 border-brand-blue pb-1 flex items-center gap-1.5 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>All Categories</span>
            </a>
            @foreach($parentCategories->take(8) as $cat)
                <a href="/category/{{ $cat->slug }}" class="hover:text-brand-blue transition pb-1 flex items-center gap-1.5 text-slate-700 flex-shrink-0">
                    <span>{{ $cat->name }}</span>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.2 rounded-full font-semibold">{{ $cat->children_count }}</span>
                </a>
            @endforeach
            <a href="{{ route('admin.earn') }}" class="text-amber-600 hover:text-amber-700 transition pb-1 flex items-center gap-1 flex-shrink-0 font-extrabold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Earn Tasks</span>
            </a>
        </div>
    </nav>

    <!-- 4. HIGH-IMPACT HERO SHOWCASE & ADVANCED SEARCH -->
    <section class="hero-mesh text-white relative overflow-hidden py-14 sm:py-20 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
        <!-- Ambient Decorative Lighting Elements -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand-blue/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-gold-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto text-center relative z-10">
            <!-- Pill Badge -->
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/15 via-amber-500/15 to-purple-500/15 border border-slate-700/80 px-4 py-1.5 rounded-full text-xs font-bold text-amber-200 mb-6 backdrop-blur-md shadow-sm">
                <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
                <span>Pakistan's #1 Next-Gen Classifieds & Micro-Tasks Ecosystem</span>
            </div>

            <!-- Dynamic Headline -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight sm:leading-tight mb-5">
                Buy, Sell & Earn with <br class="hidden sm:inline">
                <span class="gold-text-shimmer font-serif italic font-normal">Unmatched Confidence</span>
            </h1>

            <p class="text-slate-300 text-sm sm:text-base max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
                Discover verified cars, flagship electronics, luxury homes, and authentic everyday essentials. Earn direct cash payouts completing advertiser tasks.
            </p>

            <!-- Comprehensive Search Master Card -->
            <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-700/90 rounded-3xl p-4 sm:p-6 shadow-2xl max-w-4xl mx-auto text-left">
                
                <!-- Quick Filter Tabs -->
                <div class="flex items-center gap-2 mb-4 overflow-x-auto pb-1 text-xs font-bold">
                    <button type="button" class="px-4 py-1.5 rounded-xl bg-brand-blue text-white shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>All Adverts</span>
                    </button>
                    <a href="/search?category_id=1" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition">Motors</a>
                    <a href="/search?category_id=3" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition">Mobiles</a>
                    <a href="/search?category_id=23" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition">Property</a>
                    <a href="/search?category_id=6" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition">Electronics</a>
                    <a href="{{ route('admin.earn') }}" class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition flex items-center gap-1">
                        <span>💰 Earn Tasks</span>
                    </a>
                </div>

                <!-- 3-Input Search Bar -->
                <form action="/search" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    
                    <!-- 1. Keyword -->
                    <div class="md:col-span-5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="q" placeholder="What are you looking for? (e.g. Corolla, iPhone 15)" 
                               class="w-full bg-slate-800/90 text-white placeholder-slate-400 text-xs sm:text-sm pl-10 pr-3 py-3.5 rounded-2xl border border-slate-700 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue focus:outline-none transition">
                    </div>

                    <!-- 2. Category -->
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        </div>
                        <select name="category_id" class="w-full bg-slate-800/90 text-white text-xs sm:text-sm pl-10 pr-8 py-3.5 rounded-2xl border border-slate-700 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue focus:outline-none transition appearance-none cursor-pointer">
                            <option value="">All Categories</option>
                            @foreach($parentCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- 3. City -->
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <select name="city_id" class="w-full bg-slate-800/90 text-white text-xs sm:text-sm pl-10 pr-8 py-3.5 rounded-2xl border border-slate-700 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue focus:outline-none transition appearance-none cursor-pointer">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- 4. Submit CTA -->
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-brand-blue hover:bg-brand-light text-white font-black py-3.5 px-4 rounded-2xl text-xs uppercase tracking-wider shadow-glow-blue transition duration-200 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span>Search</span>
                        </button>
                    </div>
                </form>

                <!-- Popular Search Pills -->
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex flex-wrap items-center gap-2 text-[11px] text-slate-400">
                    <span class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Trending:</span>
                    <a href="/search?q=Civic" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">Honda Civic</a>
                    <a href="/search?q=iPhone" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">iPhone 15 Pro</a>
                    <a href="/search?q=Corolla" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">Toyota Corolla</a>
                    <a href="/search?q=DHA" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">DHA Plots</a>
                    <a href="/search?q=YBR" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">Yamaha YBR 125</a>
                </div>

            </div>

            <!-- Key Trust Metric Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-10 max-w-4xl mx-auto text-left">
                <div class="p-3.5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-sm flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-brand-light flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">KYC Verified</div>
                        <div class="text-[10px] text-slate-400">Authentic Sellers</div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-sm flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">Earn Real Cash</div>
                        <div class="text-[10px] text-slate-400">JazzCash & Easypaisa</div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-sm flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">Real-Time Chat</div>
                        <div class="text-[10px] text-slate-400">Direct In-App Messaging</div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-sm flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">0% Free Ads</div>
                        <div class="text-[10px] text-slate-400">No Hidden Charges</div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 5. CATEGORIES DIRECTORY (TAXONOMY EXPLORER) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold tracking-widest text-brand-blue uppercase mb-1 block">Directory & Taxonomy</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Explore Categories</h2>
            </div>
            <a href="{{ route('admin.categories') }}" class="text-xs font-bold text-brand-blue hover:text-brand-dark flex items-center gap-1 group">
                <span>Manage Taxonomy in Admin</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- 11-Card Dynamic Category Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-5">
            @php
                $categoryThemes = [
                    'motors' => [
                        'bg' => 'bg-blue-50 text-blue-600 border-blue-100 group-hover:border-blue-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path>'
                    ],
                    'mobile-phones' => [
                        'bg' => 'bg-purple-50 text-purple-600 border-purple-100 group-hover:border-purple-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>'
                    ],
                    'property-for-sale' => [
                        'bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100 group-hover:border-emerald-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>'
                    ],
                    'property-for-rent' => [
                        'bg' => 'bg-teal-50 text-teal-600 border-teal-100 group-hover:border-teal-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>'
                    ],
                    'electronics-appliances' => [
                        'bg' => 'bg-amber-50 text-amber-600 border-amber-100 group-hover:border-amber-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>'
                    ],
                    'bikes-motorcycles' => [
                        'bg' => 'bg-rose-50 text-rose-600 border-rose-100 group-hover:border-rose-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18a4 4 0 100-8 4 4 0 000 8zM18 18a4 4 0 100-8 4 4 0 000 8zM6 14h3l3-5h5l2 5M12 9l-2-4h-3"></path>'
                    ],
                    'services' => [
                        'bg' => 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:border-indigo-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>'
                    ],
                    'jobs' => [
                        'bg' => 'bg-cyan-50 text-cyan-600 border-cyan-100 group-hover:border-cyan-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>'
                    ],
                    'animals' => [
                        'bg' => 'bg-green-50 text-green-600 border-green-100 group-hover:border-green-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>'
                    ],
                    'furniture-home-decor' => [
                        'bg' => 'bg-orange-50 text-orange-600 border-orange-100 group-hover:border-orange-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>'
                    ],
                    'fashion-beauty' => [
                        'bg' => 'bg-pink-50 text-pink-600 border-pink-100 group-hover:border-pink-400',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>'
                    ],
                ];
            @endphp

            @foreach($parentCategories as $cat)
                @php
                    $theme = $categoryThemes[$cat->slug] ?? ['bg' => 'bg-slate-50 text-slate-600 border-slate-100 group-hover:border-brand-blue', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>'];
                @endphp
                <a href="/category/{{ $cat->slug }}" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-card-hover hover:border-brand-blue/50 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl {{ $theme['bg'] }} flex items-center justify-center mb-3.5 border transition-transform duration-300 group-hover:scale-110 shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $theme['svg'] !!}
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-brand-blue transition line-clamp-1 mb-1">
                        {{ $cat->name }}
                    </h3>
                    <span class="text-[11px] text-brand-blue font-bold bg-blue-50/60 px-2 py-0.5 rounded-full mt-0.5">
                        {{ $cat->children_count }} sub-types →
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 6. SPONSORED EARN SHOWCASE BANNER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-brand-navy via-slate-900 to-indigo-950 p-8 sm:p-12 text-white border border-amber-500/30 shadow-xl">
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500 animate-pulse"></span>
                        <span>Big Live Earn & Rewards</span>
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight mb-3">
                        Turn Your Spare Time into <span class="gold-text-shimmer font-serif italic">Real Cash Payouts</span>
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mb-6">
                        Complete simple social micro-tasks, review advertiser campaigns, and get paid directly to your <strong>Easypaisa</strong> or <strong>JazzCash</strong> wallet with transparent settlement ledgers.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.earn') }}" class="gold-gradient-bg text-slate-950 font-black px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-glow-gold hover:scale-[1.02] transition">
                            Explore Earn System
                        </a>
                        <a href="{{ route('admin.earn.campaigns') }}" class="bg-white/10 hover:bg-white/15 text-white font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider border border-white/20 transition backdrop-blur-sm">
                            Advertise With Us
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 grid grid-cols-2 gap-3.5">
                    <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80 backdrop-blur-sm">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total Distributed</span>
                        <span class="text-xl font-black text-amber-300">₨ 500K+</span>
                        <span class="text-[10px] text-emerald-400 block mt-1">Verified payouts</span>
                    </div>
                    <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80 backdrop-blur-sm">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Instant Method</span>
                        <span class="text-xl font-black text-white">Easypaisa</span>
                        <span class="text-[10px] text-slate-300 block mt-1">& JazzCash Auto</span>
                    </div>
                    <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80 backdrop-blur-sm">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Active Tasks</span>
                        <span class="text-xl font-black text-white">Daily</span>
                        <span class="text-[10px] text-blue-400 block mt-1">Fresh campaigns</span>
                    </div>
                    <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80 backdrop-blur-sm">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Audit Ledger</span>
                        <span class="text-xl font-black text-emerald-400">100% Safe</span>
                        <span class="text-[10px] text-slate-300 block mt-1">Idempotent keys</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FRESH & VERIFIED LISTINGS RECOMMENDATIONS -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold tracking-widest text-brand-blue uppercase mb-1 block">Live Marketplace</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Fresh Recommendations</h2>
            </div>
            <a href="/search" class="text-xs font-bold text-brand-blue hover:text-brand-dark flex items-center gap-1 group">
                <span>View All Active Listings ({{ $counts['listings'] }})</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        @if($featuredListings->isEmpty())
            <div class="bg-white border border-slate-200/80 rounded-3xl p-16 text-center shadow-xs">
                <div class="w-16 h-16 bg-blue-50 text-brand-blue rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No Active Listings Currently</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                    Be the first seller to publish your vehicle, electronics, or property to thousands of active buyers.
                </p>
                <a href="/sell" class="gold-gradient-bg text-slate-950 font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-glow-gold inline-flex items-center gap-2">
                    <span>+ Post the First Ad</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($featuredListings as $ad)
                    <a href="/listings/{{ $ad->id }}" class="group bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs hover:shadow-card-hover hover:border-brand-blue/60 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between block">
                        <div>
                            <!-- Visual Placeholder / Image Area -->
                            <div class="h-48 bg-gradient-to-tr from-slate-900 via-slate-800 to-slate-900 relative flex items-center justify-center overflow-hidden">
                                
                                <!-- Icon / Graphic Representation -->
                                <div class="transform group-hover:scale-110 transition-transform duration-500 flex flex-col items-center text-slate-500 group-hover:text-amber-400">
                                    @if(str_contains(strtolower($ad->category?->slug ?? ''), 'motor') || str_contains(strtolower($ad->category?->slug ?? ''), 'car'))
                                        <svg class="w-16 h-16 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                    @elseif(str_contains(strtolower($ad->category?->slug ?? ''), 'mobile') || str_contains(strtolower($ad->category?->slug ?? ''), 'phone'))
                                        <svg class="w-16 h-16 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    @elseif(str_contains(strtolower($ad->category?->slug ?? ''), 'property'))
                                        <svg class="w-16 h-16 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    @else
                                        <svg class="w-16 h-16 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>

                                <!-- Condition Badge -->
                                <div class="absolute top-3 left-3 bg-slate-900/90 backdrop-blur-md text-amber-300 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md border border-amber-500/30">
                                    {{ $ad->condition_code ?? 'Used' }}
                                </div>

                                <!-- Verified Seller Tag -->
                                <div class="absolute bottom-2 right-2 bg-emerald-950/80 backdrop-blur-sm text-emerald-300 text-[10px] font-semibold px-2 py-0.5 rounded flex items-center gap-1 border border-emerald-500/30">
                                    <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span>Verified</span>
                                </div>
                            </div>

                            <!-- Content Details -->
                            <div class="p-4 sm:p-5">
                                <div class="text-[10px] font-bold text-brand-blue uppercase tracking-wider mb-1">
                                    {{ $ad->category->name ?? 'Classified' }}
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-brand-blue transition mb-1.5" title="{{ $ad->title }}">
                                    {{ $ad->title }}
                                </h4>
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                    {{ $ad->description }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer: Price & City -->
                        <div class="px-4 sm:px-5 pb-4 pt-3 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Asking Price</span>
                                <span class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                                    <span class="text-xs text-brand-blue font-bold">PKR</span>
                                    {{ number_format(($ad->price_minor ?? 0) / 100) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 font-medium block">City</span>
                                <span class="text-xs font-semibold text-slate-700 flex items-center gap-1 justify-end">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <span>{{ $ad->city->name ?? ($ad->location_label ?? 'Pakistan') }}</span>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <!-- 8. HOW IT WORKS (3 SIMPLE STEPS) -->
    <section class="bg-white border-y border-slate-200/80 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold tracking-widest text-brand-blue uppercase mb-1 block">Seamless Experience</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">How Big Live Works</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">Trade directly or earn real money in 3 simple steps</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-brand-blue transition">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-brand-blue font-black text-lg flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        1
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Post Your Ad in 60s</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Snap high-res photos, set your price in PKR, select your category and city, and publish your ad with zero listing fees.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-brand-blue transition">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 font-black text-lg flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        2
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Direct In-App Chat</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Connect with genuine, biometric-verified buyers through real-time encrypted messaging without exposing personal phone numbers.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-brand-blue transition">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 font-black text-lg flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        3
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Deal & Cash Out</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Finalize transaction safely in person or withdraw your sponsored task rewards directly into your Easypaisa or JazzCash account.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. MOBILE APP DOWNLOAD CTA SECTION -->
    <section id="download-app" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-gradient-to-br from-brand-navy via-slate-900 to-slate-950 rounded-3xl p-8 sm:p-14 text-white border border-slate-800 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-blue/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider mb-4">
                        <span>Flutter Native App</span>
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight mb-4">
                        Download Big Live Mobile App
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-6 max-w-xl">
                        Get instant push alerts for buyer messages, discover nearby deals with GPS radar, and submit earning tasks on the go with our lightning-fast native mobile application.
                    </p>
                    <div class="flex flex-wrap gap-4 items-center">
                        <button class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-3 rounded-2xl border border-slate-700 flex items-center gap-3 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.61-.75 1.04-1.8 1.01-2.85-.92.04-2.06.62-2.73 1.4-.59.68-1.1 1.76-1.01 2.79 1.03.08 2.12-.59 2.73-1.34z"/></svg>
                            <div class="text-left">
                                <div class="text-[9px] uppercase tracking-wider text-slate-400">Download on</div>
                                <div class="text-xs font-bold">Apple App Store</div>
                            </div>
                        </button>

                        <button class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-3 rounded-2xl border border-slate-700 flex items-center gap-3 transition">
                            <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M3.609 1.814L13.792 12 3.61 22.186c-.185-.184-.298-.445-.298-.73V2.544c0-.285.113-.546.297-.73zm11.24 11.24l2.671-2.67-12.78-7.38 10.109 10.05zm0 1.892l-10.11 10.05 12.78-7.38-2.67-2.67zm1.055-1.056l3.393-1.96c.712-.411.712-1.082 0-1.493l-3.393-1.96-1.984 1.984 1.984 1.984z"/></svg>
                            <div class="text-left">
                                <div class="text-[9px] uppercase tracking-wider text-slate-400">Get it on</div>
                                <div class="text-xs font-bold">Google Play Store</div>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-4 text-center">
                    <div class="inline-block p-4 rounded-3xl bg-slate-800/90 border border-slate-700 shadow-xl">
                        <img src="{{ asset('images/logo.png') }}" alt="Big Live App" class="w-32 h-32 rounded-2xl object-cover mx-auto shadow-2xl ring-2 ring-brand-blue/40">
                        <span class="text-[11px] font-bold text-slate-300 block mt-2.5">Official Big Live Mobile App</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. ENTERPRISE FOOTER -->
    <footer class="bg-brand-navy text-slate-400 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-5 gap-10 text-xs">
            
            <!-- Col 1: Brand Info -->
            <div class="col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Big Live Market" class="w-10 h-10 rounded-xl object-cover ring-1 ring-amber-400/40 flex-shrink-0">
                    <span class="text-white font-extrabold text-base tracking-wide">Big Live Marketplace</span>
                </div>
                <p class="text-slate-400 leading-relaxed text-xs mb-4 max-w-sm">
                    Pakistan's foremost trusted classifieds ecosystem and micro-earning platform. Elevating commerce, user trust, and community rewards.
                </p>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md bg-slate-800 text-amber-300 text-[10px] font-bold border border-slate-700">KYC Verified Network</span>
                    <span class="px-2.5 py-1 rounded-md bg-slate-800 text-emerald-300 text-[10px] font-bold border border-slate-700">Encrypted Chat</span>
                </div>
            </div>

            <!-- Col 2: Top Categories -->
            <div>
                <h5 class="text-white font-bold mb-4 uppercase tracking-wider text-[11px] text-amber-300">Popular Categories</h5>
                <ul class="space-y-2.5">
                    @foreach($parentCategories->take(5) as $cat)
                        <li><a href="/search?category_id={{ $cat->id }}" class="hover:text-amber-300 transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Col 3: Cities -->
            <div>
                <h5 class="text-white font-bold mb-4 uppercase tracking-wider text-[11px] text-amber-300">Top Locations</h5>
                <ul class="space-y-2.5">
                    @foreach($cities->take(5) as $city)
                        <li><a href="/search?city_id={{ $city->id }}" class="hover:text-amber-300 transition">{{ $city->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Col 4: Quick Portals & Safety -->
            <div>
                <h5 class="text-white font-bold mb-4 uppercase tracking-wider text-[11px] text-amber-300">Management & Help</h5>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-blue-400 font-bold hover:underline">Admin Operations Portal</a></li>
                    <li><a href="{{ route('admin.earn') }}" class="hover:text-amber-300 transition">Sponsored Earn Overview</a></li>
                    <li><a href="{{ route('admin.categories') }}" class="hover:text-amber-300 transition">Category Management</a></li>
                    <li><a href="{{ route('admin.users') }}" class="hover:text-amber-300 transition">User Verification Directory</a></li>
                    <li><a href="/login" class="hover:text-amber-300 transition">Sign In to Account</a></li>
                </ul>
            </div>

        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Big Live Marketplace. Built with high performance & verified security.</p>
            <p class="mt-2 sm:mt-0 text-amber-300/60 font-semibold">Lahore • Karachi • Islamabad • Rawalpindi</p>
        </div>
    </footer>

</body>
</html>
