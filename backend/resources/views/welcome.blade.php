<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Big Live Marketplace — Premium Classifieds & Lifestyle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    },
                    colors: {
                        obsidian: '#090D16',
                        midnight: '#0F172A',
                        slateCard: '#1E293B',
                        champagne: {
                            50: '#FDFCF7',
                            100: '#FAF6EB',
                            200: '#F3E8C9',
                            300: '#EBD49F',
                            400: '#DFBD69',
                            500: '#D4AF37',
                            600: '#B89228',
                            700: '#8C6C1B',
                        },
                        pearl: '#F8F9FC',
                    },
                    boxShadow: {
                        'soft-luxury': '0 12px 36px -4px rgba(15, 23, 42, 0.05), 0 4px 12px -2px rgba(15, 23, 42, 0.02)',
                        'hover-luxury': '0 20px 40px -8px rgba(212, 175, 55, 0.16), 0 8px 16px -4px rgba(15, 23, 42, 0.06)',
                        'gold-glow': '0 4px 20px rgba(212, 175, 55, 0.35)',
                    }
                }
            }
        }
    </script>
    <style>
        .gold-gradient {
            background: linear-gradient(135deg, #F3E8C9 0%, #D4AF37 50%, #AA821C 100%);
        }
        .gold-text-gradient {
            background: linear-gradient(135deg, #DFBD69 0%, #D4AF37 50%, #F5E5B8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .luxury-card-gradient {
            background: linear-gradient(180deg, #FFFFFF 0%, #FCFDFE 100%);
        }
    </style>
</head>
<body class="bg-[#F8F9FC] text-slate-800 antialiased font-sans flex flex-col min-h-screen justify-between selection:bg-champagne-500 selection:text-obsidian">
    <div>
        <!-- Top Announcement Ribbon -->
        <div class="bg-obsidian border-b border-amber-500/20 text-amber-200/90 text-xs py-1.5 px-4 text-center tracking-wide font-medium flex items-center justify-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-champagne-500 animate-pulse"></span>
            <span>Discover verified luxury cars, real estate, gadgets, and exclusive deals across Pakistan.</span>
        </div>

        <!-- Premium Navigation Header -->
        <header class="bg-obsidian/95 backdrop-blur-md text-white sticky top-0 z-50 border-b border-slate-800/80 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-6">
                    <!-- Brand Logo with Metallic Emblem -->
                    <a href="/" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl gold-gradient flex items-center justify-center text-obsidian font-black text-xl shadow-gold-glow group-hover:scale-105 transition-transform duration-300">
                            B
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold tracking-tight text-white font-sans flex items-center gap-1.5">
                                Big Live <span class="gold-text-gradient font-serif italic text-2xl font-normal">Marketplace</span>
                            </span>
                            <span class="text-[10px] text-amber-300/60 uppercase tracking-widest font-semibold">Prestige Classifieds</span>
                        </div>
                    </a>

                    <!-- Elegant Search Bar with City Selector -->
                    <form action="/search" method="GET" class="flex-1 max-w-2xl hidden md:flex items-center bg-slate-900/80 p-1.5 rounded-2xl border border-slate-700/80 focus-within:border-champagne-500/80 focus-within:shadow-[0_0_20px_rgba(212,175,55,0.15)] transition-all duration-300">
                        <div class="relative w-44 border-r border-slate-800 pr-2">
                            <select name="city" class="w-full bg-transparent text-xs text-amber-100 font-medium px-3 py-2 focus:outline-none cursor-pointer">
                                <option value="" class="bg-slate-900 text-white">All Pakistan</option>
                                <option value="lahore" class="bg-slate-900 text-white">Lahore</option>
                                <option value="karachi" class="bg-slate-900 text-white">Karachi</option>
                                <option value="islamabad" class="bg-slate-900 text-white">Islamabad</option>
                                <option value="rawalpindi" class="bg-slate-900 text-white">Rawalpindi</option>
                            </select>
                        </div>
                        <div class="relative flex-1 px-3">
                            <input type="text" name="q" placeholder="Search luxury cars, apartments, flagship phones..." 
                                   class="w-full bg-transparent text-sm text-white placeholder-slate-400 focus:outline-none">
                        </div>
                        <button type="submit" class="gold-gradient text-obsidian px-5 py-2 rounded-xl text-xs font-bold tracking-wider hover:opacity-95 transition-opacity shadow-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            SEARCH
                        </button>
                    </form>

                    <!-- Account Navigation & Gold Sell CTA -->
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="/dashboard" class="flex items-center gap-2 text-xs font-semibold text-slate-200 hover:text-champagne-400 transition py-1.5 px-3 rounded-lg hover:bg-slate-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                {{ auth()->user()->name }}
                            </a>
                        @else
                            <a href="/login" class="text-xs font-bold text-slate-300 hover:text-champagne-400 transition tracking-wide px-3 py-2">SIGN IN</a>
                            <a href="/register" class="text-xs font-bold text-amber-200/90 hover:text-white transition tracking-wide hidden lg:inline-block px-3 py-2 border border-amber-500/30 rounded-xl hover:border-amber-400">JOIN</a>
                        @endauth
                        <a href="/sell" class="gold-gradient hover:opacity-95 text-obsidian font-extrabold px-5 py-2.5 rounded-xl text-xs tracking-wider shadow-gold-glow flex items-center gap-1.5 hover:scale-[1.02] transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            POST AD
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Categories Gold-Accented Sub-Nav -->
        <nav class="bg-white border-b border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-x-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center gap-8 text-xs font-semibold uppercase tracking-wider whitespace-nowrap text-slate-600">
                <a href="/" class="text-obsidian font-bold border-b-2 border-champagne-500 pb-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-champagne-500"></span>
                    ALL CATEGORIES
                </a>
                <a href="/category/motors" class="hover:text-champagne-600 transition pb-1">Motors & Supercars</a>
                <a href="/category/mobile-phones" class="hover:text-champagne-600 transition pb-1">Flagship Phones</a>
                <a href="/category/property" class="hover:text-champagne-600 transition pb-1">Luxury Real Estate</a>
                <a href="/category/electronics-appliances" class="hover:text-champagne-600 transition pb-1">Electronics & Audio</a>
                <a href="/category/cars" class="hover:text-champagne-600 transition pb-1">Pre-Owned Cars</a>
                <a href="/category/smartphones" class="hover:text-champagne-600 transition pb-1">Apple & Samsung</a>
            </div>
        </nav>

        <!-- Hero Showcase Banner with Subtle Rich Ambience -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="relative rounded-3xl overflow-hidden shadow-soft-luxury mb-12 border border-slate-800/40 bg-gradient-to-br from-obsidian via-slate-900 to-[#121926] p-8 sm:p-14 text-white">
                <!-- Decorative Glow Balls -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 max-w-2xl">
                    <div class="inline-flex items-center gap-2 bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-bold uppercase tracking-widest px-3.5 py-1 rounded-full mb-5 backdrop-blur-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-champagne-500"></span>
                        Premium Marketplace Experience
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-4">
                        Buy & Sell with <span class="gold-text-gradient font-serif italic font-normal">Unmatched Distinction</span>
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-8 max-w-xl font-normal">
                        Pakistan’s premier classifieds ecosystem. Verified listings, certified sellers, instant encrypted chat, and an exquisite user experience crafted for luxury and everyday excellence.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/sell" class="gold-gradient hover:opacity-95 text-obsidian font-bold px-7 py-3.5 rounded-xl text-xs tracking-wider shadow-gold-glow transition-transform hover:scale-[1.02]">
                            POST A FREE LISTING
                        </a>
                        <a href="/search" class="bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold px-6 py-3.5 rounded-xl text-xs tracking-wider backdrop-blur-md transition">
                            EXPLORE ALL ADVERTS
                        </a>
                    </div>
                </div>
            </div>

            <!-- Verified Curated Recommendations Section -->
            <section>
                <div class="flex items-end justify-between mb-8 pb-4 border-b border-slate-200/80">
                    <div>
                        <span class="text-xs font-bold tracking-widest text-champagne-600 uppercase mb-1 block">Curated Collection</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight">Fresh Recommendations</h2>
                    </div>
                    <a href="/search" class="text-xs font-bold tracking-wider uppercase text-champagne-600 hover:text-champagne-700 flex items-center gap-1 group">
                        <span>View Entire Collection</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                @php
                    $listings = \Illuminate\Support\Facades\Schema::hasTable('listings')
                        ? \App\Models\Listing::where('status', 'active')->latest('id')->take(8)->get()
                        : collect();
                @endphp

                @if($listings->isEmpty())
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-16 text-center shadow-soft-luxury">
                        <div class="w-20 h-20 bg-amber-50 text-champagne-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-100 shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-obsidian mb-2">Exclusive Catalog Ready</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                            Be the first to present your luxury vehicle, electronics, or real estate item to thousands of verified buyers.
                        </p>
                        <a href="/sell" class="gold-gradient text-obsidian font-bold px-6 py-3 rounded-xl text-xs tracking-wider shadow-gold-glow inline-flex items-center gap-2">
                            Post First Ad
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-7">
                        @foreach($listings as $item)
                            <a href="/listings/{{ $item->id }}" class="group luxury-card-gradient border border-slate-200/80 rounded-2xl overflow-hidden shadow-soft-luxury hover:shadow-hover-luxury hover:border-amber-400/50 transition-all duration-300 flex flex-col">
                                <!-- Card Image Container with Smooth Zoom -->
                                <div class="h-48 bg-gradient-to-tr from-slate-900 to-slate-800 flex items-center justify-center text-slate-400 relative overflow-hidden">
                                    <div class="transform group-hover:scale-105 transition-transform duration-500 flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-600 group-hover:text-champagne-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="absolute top-3 left-3 bg-obsidian/80 backdrop-blur-md text-amber-200 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md border border-amber-500/20">
                                        {{ $item->condition_code }}
                                    </div>
                                    <div class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-sm text-slate-300 text-[10px] px-2 py-0.5 rounded">
                                        Verified
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="text-[11px] font-semibold text-champagne-600 uppercase tracking-wider mb-1">
                                            {{ $item->category->name ?? 'Classified' }}
                                        </div>
                                        <h4 class="font-bold text-slate-900 text-base line-clamp-1 group-hover:text-champagne-600 transition-colors duration-200 mb-1">
                                            {{ $item->title }}
                                        </h4>
                                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                            {{ $item->description }}
                                        </p>
                                    </div>
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] uppercase text-slate-400 font-bold block">Asking</span>
                                            <span class="text-lg font-black text-obsidian tracking-tight">
                                                <span class="text-xs text-champagne-600 font-bold">{{ $item->currency }}</span> 
                                                {{ number_format(($item->price_minor ?? 0) / 100) }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-slate-400 font-medium block">Location</span>
                                            <span class="text-xs font-semibold text-slate-600">{{ $item->location_label ?? 'Pakistan' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>
    </div>

    <!-- Luxurious Footer -->
    <footer class="bg-obsidian text-slate-400 py-16 mt-20 border-t border-amber-500/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-10 text-xs">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg gold-gradient flex items-center justify-center text-obsidian font-black text-sm">B</div>
                    <span class="text-white font-bold text-sm tracking-wide">Big Live Marketplace</span>
                </div>
                <p class="text-slate-500 leading-relaxed text-xs mb-4">
                    The premium classifieds standard in Pakistan. Elevating commerce, trust, and community.
                </p>
                <span class="text-[10px] uppercase tracking-widest text-amber-400/80 font-bold">Verified & Secure</span>
            </div>
            <div>
                <h5 class="text-white font-bold mb-4 uppercase tracking-widest text-[11px] text-amber-200/90">Popular Categories</h5>
                <ul class="space-y-2.5">
                    <li><a href="/category/cars" class="hover:text-champagne-400 transition">Motors & Cars</a></li>
                    <li><a href="/category/property" class="hover:text-champagne-400 transition">Luxury Villas & Apartments</a></li>
                    <li><a href="/category/smartphones" class="hover:text-champagne-400 transition">Smartphones & Watches</a></li>
                    <li><a href="/category/electronics-appliances" class="hover:text-champagne-400 transition">Premium Electronics</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-4 uppercase tracking-widest text-[11px] text-amber-200/90">Trending Searches</h5>
                <ul class="space-y-2.5">
                    <li><a href="/search?q=Civic" class="hover:text-champagne-400 transition">Honda Civic Oriel</a></li>
                    <li><a href="/search?q=iPhone" class="hover:text-champagne-400 transition">Apple iPhone Pro Max</a></li>
                    <li><a href="/search?q=Fortuner" class="hover:text-champagne-400 transition">Toyota Fortuner Legender</a></li>
                    <li><a href="/search?q=MacBook" class="hover:text-champagne-400 transition">Apple MacBook Pro</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-4 uppercase tracking-widest text-[11px] text-amber-200/90">Customer Trust</h5>
                <ul class="space-y-2.5">
                    <li><a href="/safety" class="hover:text-champagne-400 transition">Safety & Fraud Guidelines</a></li>
                    <li><a href="/help" class="hover:text-champagne-400 transition">24/7 Concierge Support</a></li>
                    <li><a href="/terms" class="hover:text-champagne-400 transition">Terms of Service</a></li>
                    <li><a href="/privacy" class="hover:text-champagne-400 transition">Privacy & Encryption</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.</p>
            <p class="mt-2 sm:mt-0 text-amber-300/40">Crafted with Luxury & Precision</p>
        </div>
    </footer>
</body>
</html>
