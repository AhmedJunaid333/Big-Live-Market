<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Big Live Marketplace</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
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
                        brand: {
                            blue: '#2563EB',
                            light: '#3B82F6',
                            navy: '#0B1120',
                        },
                        gold: {
                            500: '#D4AF37',
                        }
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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen justify-between selection:bg-gold-500 selection:text-slate-900">

    <!-- HEADER -->
    <header class="bg-brand-navy/95 backdrop-blur-md text-white sticky top-0 z-50 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4 sm:gap-6">
                <!-- Brand Logo -->
                <a href="/" class="flex items-center gap-3 group flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Big Live" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-amber-400/40 group-hover:scale-105 transition-transform">
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold tracking-tight text-white flex items-center gap-1">
                            BIG LIVE <span class="gold-text-shimmer font-serif italic text-2xl font-normal">Market</span>
                        </span>
                        <span class="text-[9px] text-amber-300/80 uppercase tracking-widest font-bold">Classifieds & Rewards</span>
                    </div>
                </a>

                <!-- Search Input -->
                <form action="/search" method="GET" class="flex-1 max-w-xl hidden md:flex items-center bg-slate-900/90 p-1.5 rounded-2xl border border-slate-700/80 focus-within:border-brand-blue transition">
                    <input type="text" name="q" placeholder="Search in {{ $parentCategory->name }}..." 
                           class="flex-1 bg-transparent px-3 text-xs text-white placeholder-slate-400 focus:outline-none">
                    <input type="hidden" name="category_id" value="{{ $parentCategory->id }}">
                    <button type="submit" class="bg-brand-blue hover:bg-brand-light text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Search</span>
                    </button>
                </form>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition">Admin Portal</a>
                    <a href="/sell" class="gold-gradient-bg hover:opacity-95 text-slate-900 font-black px-4 sm:px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-sm flex items-center gap-1.5 hover:scale-[1.02] transition">
                        <span>+ Post Ad</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-8">

        <!-- BREADCRUMBS & HERO HEADER -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-xs">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-4 overflow-x-auto whitespace-nowrap">
                <a href="/" class="hover:text-brand-blue transition">Home</a>
                <span>/</span>
                <a href="/#categories" class="hover:text-brand-blue transition">Categories</a>
                <span>/</span>
                <a href="/category/{{ $parentCategory->slug }}" class="hover:text-brand-blue font-bold text-slate-700 transition">{{ $parentCategory->name }}</a>
                @if($selectedSubcategory)
                    <span>/</span>
                    <span class="text-brand-blue font-extrabold">{{ $selectedSubcategory->name }}</span>
                @endif
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        {{ $selectedSubcategory ? $selectedSubcategory->name : $parentCategory->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-2xl">
                        @if($selectedSubcategory)
                            Showing classified ads in <strong>{{ $selectedSubcategory->name }}</strong> within {{ $parentCategory->name }}.
                        @else
                            Explore all <strong>{{ $subcategories->count() }} subcategories</strong> and verified listings in {{ $parentCategory->name }}.
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-2.5 flex-shrink-0">
                    <a href="{{ route('admin.categories', ['parent_id' => $parentCategory->id]) }}" class="px-3.5 py-2 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-bold border border-purple-200 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>Manage in Admin</span>
                    </a>
                    <a href="/sell" class="px-4 py-2 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <span>+ Post Ad Here</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ⭐️ SUBCATEGORIES DIRECTORY EXPLORER GRID (THE CORE USER FEATURE) ⭐️ -->
        <section class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-blue"></span>
                    <h2 class="text-base font-extrabold text-slate-900">
                        Sub-categories in {{ $parentCategory->name }} ({{ $subcategories->count() }})
                    </h2>
                </div>
                @if($selectedSubcategory)
                    <a href="/category/{{ $parentCategory->slug }}" class="text-xs font-bold text-brand-blue hover:underline">
                        View All {{ $parentCategory->name }} Subcategories →
                    </a>
                @endif
            </div>

            <!-- SUBCATEGORIES CARDS GRID -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                
                <!-- "All Category" Filter Card -->
                <a href="/category/{{ $parentCategory->slug }}" 
                   class="p-4 rounded-2xl border text-center transition-all duration-200 flex flex-col items-center justify-center gap-2 {{ !$selectedSubcategory ? 'bg-blue-50/80 border-brand-blue shadow-xs text-brand-blue font-extrabold' : 'bg-slate-50 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-white' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ !$selectedSubcategory ? 'bg-brand-blue text-white' : 'bg-slate-200 text-slate-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <span class="text-xs leading-snug">All {{ $parentCategory->name }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Everything</span>
                </a>

                <!-- Dynamic Subcategories Cards -->
                @foreach($subcategories as $sub)
                    @php
                        $isSelected = ($selectedSubcategory && $selectedSubcategory->id === $sub->id);
                    @endphp
                    <a href="/category/{{ $parentCategory->slug }}?sub={{ $sub->slug }}" 
                       class="p-4 rounded-2xl border text-center transition-all duration-200 flex flex-col items-center justify-center gap-2 group {{ $isSelected ? 'bg-blue-50/80 border-brand-blue shadow-xs text-brand-blue font-extrabold' : 'bg-slate-50 border-slate-200 text-slate-700 hover:border-brand-blue/50 hover:bg-white hover:shadow-xs' }}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105 {{ $isSelected ? 'bg-brand-blue text-white shadow-xs' : 'bg-slate-200 text-slate-600 group-hover:bg-blue-100 group-hover:text-brand-blue' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <span class="text-xs leading-snug line-clamp-2" title="{{ $sub->name }}">{{ $sub->name }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold group-hover:text-brand-blue">Explore →</span>
                    </a>
                @endforeach

            </div>
        </section>

        <!-- CLASSIFIED ADS RESULTS SECTION -->
        <section class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">
                        {{ $selectedSubcategory ? $selectedSubcategory->name : $parentCategory->name }} Listings
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Found {{ $listings->count() }} active verified adverts</p>
                </div>

                <!-- City Filter Dropdown -->
                <form action="/category/{{ $parentCategory->slug }}" method="GET" class="flex items-center gap-2">
                    @if($selectedSubcategory)
                        <input type="hidden" name="sub" value="{{ $selectedSubcategory->slug }}">
                    @endif
                    <select name="city_id" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 shadow-xs focus:outline-none cursor-pointer">
                        <option value="">All Pakistan Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($listings->isEmpty())
                <div class="bg-white border border-slate-200/90 rounded-3xl p-14 text-center shadow-xs">
                    <div class="w-16 h-16 bg-blue-50 text-brand-blue rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-base font-extrabold text-slate-900 mb-1">No Active Listings in this Category Yet</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">
                        Be the first seller to list an item under <strong>{{ $selectedSubcategory ? $selectedSubcategory->name : $parentCategory->name }}</strong> and reach verified buyers.
                    </p>
                    <a href="/sell" class="gold-gradient-bg text-slate-950 font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-sm inline-flex items-center gap-2 hover:scale-105 transition">
                        <span>+ Post the First Ad</span>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($listings as $item)
                        <a href="/listings/{{ $item->id }}" class="group bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs hover:shadow-md hover:border-brand-blue transition flex flex-col justify-between">
                            <div>
                                <div class="h-44 bg-slate-900 flex items-center justify-center text-slate-400 relative overflow-hidden">
                                    <svg class="w-12 h-12 text-slate-600 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <div class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-sm text-amber-300 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">
                                        {{ $item->condition_code }}
                                    </div>
                                    <div class="absolute bottom-2 right-2 bg-emerald-950/80 backdrop-blur-sm text-emerald-300 text-[10px] font-semibold px-2 py-0.5 rounded">
                                        Verified
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="text-[10px] font-bold text-brand-blue uppercase tracking-wider mb-1">
                                        {{ $item->category?->name ?? 'Classified' }}
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-brand-blue transition mb-1">
                                        {{ $item->title }}
                                    </h4>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $item->description }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-4 pb-4 pt-2.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <div>
                                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Asking</span>
                                    <span class="text-base font-black text-slate-900">
                                        <span class="text-xs text-brand-blue font-bold">PKR</span>
                                        {{ number_format(($item->price_minor ?? 0) / 100) }}
                                    </span>
                                </div>
                                <span class="text-xs font-semibold text-slate-600">
                                    {{ $item->city->name ?? 'Pakistan' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="bg-brand-navy text-slate-400 py-10 border-t border-slate-800 text-center text-xs mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Big Live" class="w-7 h-7 rounded-lg object-cover">
                <span class="text-white font-extrabold text-sm">Big Live Marketplace</span>
            </div>
            <p>&copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
