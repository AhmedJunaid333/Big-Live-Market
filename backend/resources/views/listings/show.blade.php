<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $listing->title }} — Big Live Marketplace</title>
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
                    <input type="text" name="q" placeholder="Find cars, mobiles, laptops..." 
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

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="/" class="hover:text-brandBlue">Home</a>
            <span>/</span>
            <a href="/category/{{ $listing->category->slug ?? 'all' }}" class="hover:text-brandBlue">{{ $listing->category->name ?? 'Category' }}</a>
            <span>/</span>
            <span class="text-slate-800 font-medium truncate max-w-xs">{{ $listing->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Gallery and Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image Box -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="h-96 sm:h-[420px] bg-slate-900 flex flex-col items-center justify-center text-slate-400 relative">
                        <svg class="w-20 h-20 text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Verified Product Image Preview</span>
                        <div class="absolute bottom-4 left-4 bg-navy/80 backdrop-blur text-white text-xs px-3 py-1.5 rounded-full font-semibold">
                            Condition: <span class="uppercase text-accentAmber">{{ $listing->condition_code }}</span>
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-navy mb-4">Description</h2>
                    <p class="text-slate-700 leading-relaxed whitespace-pre-line text-sm sm:text-base">
                        {{ $listing->description }}
                    </p>

                    <div class="mt-8 pt-6 border-t border-slate-200 grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Location</span>
                            <strong class="text-slate-800 text-sm">{{ $listing->location_label ?? ($listing->city->name ?? 'Pakistan') }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Posted On</span>
                            <strong class="text-slate-800 text-sm">{{ $listing->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Ad ID</span>
                            <strong class="text-slate-800 text-sm">#{{ $listing->id }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col: Price, Seller Card & Actions -->
            <div class="space-y-6">
                <!-- Price Box -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Asking Price</div>
                    <div class="text-3xl font-black text-navy mb-3">
                        {{ $listing->currency }} {{ number_format(($listing->price_minor ?? 0) / 100) }}
                    </div>
                    <h1 class="text-lg font-bold text-slate-900 mb-4 leading-snug">
                        {{ $listing->title }}
                    </h1>
                    <div class="text-xs text-slate-500 flex items-center gap-1 mb-6">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $listing->location_label ?? ($listing->city->name ?? 'Pakistan') }}</span>
                    </div>

                    <div class="space-y-3">
                        <button onclick="alert('Seller Contact: ' + '{{ $listing->seller->phone_e164 ?? '+92300-1234567' }}')" 
                                class="w-full bg-brandBlue hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-xl shadow transition text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Show Phone Number
                        </button>
                        <a href="/login" class="w-full bg-slate-100 hover:bg-slate-200 text-navy font-bold py-3 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Chat with Seller
                        </a>
                    </div>
                </div>

                <!-- Seller Profile Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Seller Description</h3>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-full bg-brandBlue/10 text-brandBlue font-black text-xl flex items-center justify-center">
                            {{ substr($listing->seller->name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-base">{{ $listing->seller->name ?? 'Verified Seller' }}</div>
                            <div class="text-xs text-slate-400">Member since {{ $listing->seller->created_at?->format('M Y') ?? '2026' }}</div>
                            <span class="inline-block mt-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                ✓ Verified Seller
                            </span>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Safe Marketplace Policy</span>
                        <a href="/safety" class="text-brandBlue font-semibold hover:underline">Safety Tips</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
