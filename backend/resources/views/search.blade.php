<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Big Live Marketplace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#13213C',
                        brandBlue: '#2471C8',
                        accentAmber: '#F5A524',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    <header class="bg-navy text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <a href="/" class="flex items-center gap-2 text-xl font-bold tracking-tight">
                    <span class="w-8 h-8 rounded-lg bg-accentAmber flex items-center justify-center text-navy font-black text-lg">B</span>
                    <span>Big Live <span class="text-accentAmber">Marketplace</span></span>
                </a>

                <form action="/search" method="GET" class="flex-1 max-w-xl hidden md:flex">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Find cars, mobiles, laptops..." 
                           class="w-full bg-white text-slate-900 border-none rounded-l-lg px-4 py-2 text-sm focus:outline-none">
                    <button type="submit" class="bg-brandBlue hover:bg-blue-600 px-5 py-2 rounded-r-lg text-sm font-semibold transition">
                        Search
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="/dashboard" class="text-sm font-medium hover:text-accentAmber transition">{{ auth()->user()->name }}</a>
                    @else
                        <a href="/login" class="text-sm font-medium hover:text-accentAmber transition">Login</a>
                    @endauth
                    <a href="/sell" class="bg-accentAmber hover:bg-amber-500 text-navy font-bold px-4 py-2 rounded-lg text-sm shadow transition">+ SELL</a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-navy">{{ $title }}</h1>
                <p class="text-sm text-slate-500">Showing {{ $listings->count() }} active listings</p>
            </div>
            <a href="/" class="text-sm font-medium text-brandBlue hover:underline">← All Categories</a>
        </div>

        @if($listings->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm">
                <div class="w-16 h-16 bg-blue-50 text-brandBlue rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy mb-1">No Listings Found</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                    Try adjusting your search keywords or filter to find what you are looking for.
                </p>
                <a href="/" class="inline-flex items-center gap-2 bg-brandBlue hover:bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition">
                    Browse All Listings
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($listings as $item)
                    <a href="/listings/{{ $item->id }}" class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg transition duration-200 flex flex-col group">
                        <div class="h-44 bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-slate-200 transition">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase text-brandBlue mb-1">{{ $item->condition_code }}</div>
                                <h4 class="font-bold text-slate-900 text-base line-clamp-1 mb-1 group-hover:text-brandBlue transition">{{ $item->title }}</h4>
                                <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $item->description }}</p>
                            </div>
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-lg font-extrabold text-navy">
                                    {{ $item->currency }} {{ number_format(($item->price_minor ?? 0) / 100) }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $item->location_label ?? 'Pakistan' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
