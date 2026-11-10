<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $listing->title }} — Big Live Marketplace Prestige</title>
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
    </style>
</head>
<body class="bg-[#F8F9FC] text-slate-800 font-sans min-h-screen flex flex-col justify-between selection:bg-champagne-500 selection:text-obsidian">
    <!-- Navbar -->
    <header class="bg-obsidian/95 backdrop-blur-md text-white sticky top-0 z-50 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-6">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl gold-gradient flex items-center justify-center text-obsidian font-black text-xl shadow-gold-glow group-hover:scale-105 transition-transform">
                        B
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight text-white font-sans flex items-center gap-1.5">
                            Big Live <span class="gold-text-gradient font-serif italic text-2xl font-normal">Marketplace</span>
                        </span>
                        <span class="text-[10px] text-amber-300/60 uppercase tracking-widest font-semibold">Prestige Classifieds</span>
                    </div>
                </a>

                <form action="/search" method="GET" class="flex-1 max-w-xl hidden md:flex bg-slate-900/80 p-1.5 rounded-2xl border border-slate-700/80 focus-within:border-champagne-500/80 transition">
                    <input type="text" name="q" placeholder="Find cars, mobiles, luxury watches..." 
                           class="w-full bg-transparent text-sm text-white placeholder-slate-400 px-3 focus:outline-none">
                    <button type="submit" class="gold-gradient text-obsidian px-5 py-2 rounded-xl text-xs font-bold tracking-wider hover:opacity-95 transition">
                        SEARCH
                    </button>
                </form>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="/dashboard" class="text-xs font-semibold text-slate-200 hover:text-champagne-400 transition">{{ auth()->user()->name }}</a>
                    @else
                        <a href="/login" class="text-xs font-bold text-slate-300 hover:text-champagne-400 transition">SIGN IN</a>
                    @endauth
                    <a href="/sell" class="gold-gradient hover:opacity-95 text-obsidian font-extrabold px-5 py-2.5 rounded-xl text-xs tracking-wider shadow-gold-glow flex items-center gap-1.5 transition">
                        + POST AD
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-slate-400 mb-6 gap-2 items-center font-medium">
            <a href="/" class="hover:text-champagne-600 transition">Home</a>
            <span>/</span>
            <a href="/category/{{ $listing->category->slug ?? 'all' }}" class="hover:text-champagne-600 transition">{{ $listing->category->name ?? 'Category' }}</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $listing->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Showcase & Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image Showcase Box -->
                <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-soft-luxury">
                    <div class="h-96 sm:h-[460px] bg-gradient-to-tr from-obsidian via-slate-900 to-slate-800 flex flex-col items-center justify-center text-slate-400 relative">
                        <div class="w-24 h-24 rounded-full bg-slate-800/80 border border-slate-700/80 flex items-center justify-center mb-3 shadow-inner">
                            <svg class="w-12 h-12 text-champagne-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-xs uppercase font-bold tracking-widest text-amber-200/80">Verified Listing Showcase</span>
                        <div class="absolute bottom-5 left-5 bg-obsidian/85 backdrop-blur-md text-white text-xs px-4 py-2 rounded-xl font-medium border border-amber-500/20 shadow-md">
                            Condition: <span class="uppercase text-champagne-400 font-bold">{{ $listing->condition_code }}</span>
                        </div>
                        <div class="absolute bottom-5 right-5 bg-emerald-500/20 backdrop-blur-md text-emerald-300 text-xs px-3.5 py-1.5 rounded-xl font-bold border border-emerald-500/30">
                            ✓ Verified Ad
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-10 shadow-soft-luxury">
                    <h2 class="text-xl font-bold text-obsidian mb-4 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-champagne-500"></span>
                        Overview & Specifications
                    </h2>
                    <p class="text-slate-700 leading-relaxed whitespace-pre-line text-sm sm:text-base font-normal">
                        {{ $listing->description }}
                    </p>

                    <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs">
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Location</span>
                            <strong class="text-slate-800 text-sm font-bold">{{ $listing->location_label ?? ($listing->city->name ?? 'Pakistan') }}</strong>
                        </div>
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Published Date</span>
                            <strong class="text-slate-800 text-sm font-bold">{{ $listing->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Reference Identifier</span>
                            <strong class="text-slate-800 text-sm font-bold">#BLM-{{ str_pad($listing->id, 5, '0', STR_PAD_LEFT) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col: Price, Seller & Direct Actions -->
            <div class="space-y-6">
                <!-- Asking Price Card -->
                <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-soft-luxury">
                    <div class="text-[11px] font-bold text-champagne-600 uppercase tracking-widest mb-1.5">Asking Price</div>
                    <div class="text-3xl sm:text-4xl font-black text-obsidian tracking-tight mb-3">
                        <span class="text-base text-champagne-600 font-bold mr-1">{{ $listing->currency }}</span>{{ number_format(($listing->price_minor ?? 0) / 100) }}
                    </div>
                    <h1 class="text-lg font-bold text-slate-900 mb-4 leading-snug">
                        {{ $listing->title }}
                    </h1>
                    <div class="text-xs text-slate-500 flex items-center gap-1.5 pb-6 border-b border-slate-100 mb-6">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <span>{{ $listing->location_label ?? 'Pakistan' }}</span>
                    </div>

                    <!-- Seller Card -->
                    <div class="bg-slate-50/90 rounded-2xl p-5 border border-slate-200/80 mb-6">
                        <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-3">Verified Merchant</div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl gold-gradient text-obsidian flex items-center justify-center font-extrabold text-lg shadow-sm">
                                {{ strtoupper(substr($listing->seller->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $listing->seller->name ?? 'Verified Seller' }}</h4>
                                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ID & Phone Verified
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Action Buttons -->
                    <div class="space-y-3">
                        <button onclick="document.getElementById('contact-box').classList.toggle('hidden')" 
                                class="w-full gold-gradient hover:opacity-95 text-obsidian font-bold py-3.5 px-4 rounded-xl text-xs tracking-wider shadow-gold-glow flex items-center justify-center gap-2 transition hover:scale-[1.01]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            SHOW PHONE NUMBER
                        </button>
                        <div id="contact-box" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-800 text-center py-3 rounded-xl text-sm font-bold tracking-wide">
                            {{ $listing->seller->phone_e164 ?? '+92 300 1234567' }}
                        </div>

                        <a href="mailto:{{ $listing->seller->email ?? 'support@biglive.pk' }}?subject=Inquiry regarding: {{ urlencode($listing->title) }}" 
                           class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl text-xs tracking-wider flex items-center justify-center gap-2 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            START SECURE CHAT
                        </a>
                    </div>
                </div>

                <!-- Safety Assurance -->
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 text-xs text-amber-900/90 leading-relaxed shadow-sm">
                    <strong class="font-bold flex items-center gap-1.5 mb-1 text-amber-950">
                        <svg class="w-4 h-4 text-champagne-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"></path>
                        </svg>
                        Prestige Safety Guarantee
                    </strong>
                    Meet sellers in secure public locations. Never release advance payments before physical inspection.
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-obsidian text-slate-400 py-8 text-center text-xs border-t border-amber-500/20">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
