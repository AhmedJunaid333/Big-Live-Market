<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Big Live Marketplace Prestige</title>
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
        .luxury-card-gradient {
            background: linear-gradient(180deg, #FFFFFF 0%, #FCFDFE 100%);
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
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Find cars, mobiles, luxury watches..." 
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
        <!-- Search Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 pb-4 border-b border-slate-200/80 gap-4">
            <div>
                <span class="text-xs font-bold tracking-widest text-champagne-600 uppercase mb-1 block">Catalog Results</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight">{{ $title }}</h1>
                <p class="text-xs text-slate-500 mt-1">Found {{ $listings->count() }} active verified listings</p>
            </div>
            <a href="/" class="text-xs font-bold uppercase tracking-wider text-champagne-600 hover:text-champagne-700 flex items-center gap-1">
                ← Return to All Categories
            </a>
        </div>

        @if($listings->isEmpty())
            <div class="bg-white border border-slate-200/80 rounded-3xl p-16 text-center shadow-soft-luxury">
                <div class="w-20 h-20 bg-amber-50 text-champagne-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-100 shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-obsidian mb-2">No Matching Classifieds</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                    Try adjusting your search criteria, keyword spelling, or browse all categories to explore available items.
                </p>
                <a href="/" class="gold-gradient text-obsidian font-bold px-6 py-3 rounded-xl text-xs tracking-wider shadow-gold-glow inline-flex items-center gap-2">
                    Browse All Classifieds
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-7">
                @foreach($listings as $item)
                    <a href="/listings/{{ $item->id }}" class="group luxury-card-gradient border border-slate-200/80 rounded-2xl overflow-hidden shadow-soft-luxury hover:shadow-hover-luxury hover:border-amber-400/50 transition-all duration-300 flex flex-col">
                        <div class="h-48 bg-gradient-to-tr from-slate-900 to-slate-800 flex items-center justify-center text-slate-400 relative overflow-hidden">
                            <div class="transform group-hover:scale-105 transition-transform duration-500 flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-600 group-hover:text-champagne-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="absolute top-3 left-3 bg-obsidian/80 backdrop-blur-md text-amber-200 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md border border-amber-500/20">
                                {{ $item->condition_code }}
                            </div>
                        </div>

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
    </main>

    <!-- Footer -->
    <footer class="bg-obsidian text-slate-400 py-8 text-center text-xs border-t border-amber-500/20">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
