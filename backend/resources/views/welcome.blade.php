<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Big Live Marketplace — Buy & Sell Everything in Pakistan</title>
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
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Top Utility Bar -->
    <header class="bg-navy text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-2 text-xl font-bold tracking-tight">
                        <span class="w-8 h-8 rounded-lg bg-accentAmber flex items-center justify-center text-navy font-black text-lg">B</span>
                        <span>Big Live <span class="text-accentAmber">Marketplace</span></span>
                    </a>
                </div>

                <!-- Search Input Bar -->
                <div class="flex-1 max-w-2xl hidden md:flex items-center gap-2">
                    <div class="relative w-48">
                        <select class="w-full bg-slate-800 border border-slate-700 text-sm text-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-brandBlue">
                            <option>Pakistan (All)</option>
                            <option>Lahore</option>
                            <option>Karachi</option>
                            <option>Islamabad</option>
                        </select>
                    </div>
                    <div class="relative flex-1">
                        <input type="text" placeholder="Find Cars, Mobile Phones, Properties and more..." 
                               class="w-full bg-white text-slate-900 border border-transparent rounded-lg px-4 py-2 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue shadow-inner">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button class="bg-brandBlue hover:bg-blue-600 px-5 py-2 rounded-lg text-sm font-semibold transition">
                        Search
                    </button>
                </div>

                <!-- User Navigation & Sell CTA -->
                <div class="flex items-center gap-3">
                    <a href="/login" class="text-sm font-medium hover:text-accentAmber transition px-2 py-1">Login</a>
                    <a href="/register" class="text-sm font-medium hover:text-accentAmber transition px-2 py-1 hidden sm:inline-block">Sign Up</a>
                    <a href="/sell" class="inline-flex items-center gap-1.5 bg-accentAmber hover:bg-amber-500 text-navy font-bold px-4 py-2 rounded-lg text-sm shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + SELL
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories Sub-header -->
    <nav class="bg-white border-b border-slate-200 shadow-sm overflow-x-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-6 text-sm font-medium whitespace-nowrap text-slate-700">
            <span class="font-bold text-navy">ALL CATEGORIES</span>
            <a href="#" class="hover:text-brandBlue transition">Motors</a>
            <a href="#" class="hover:text-brandBlue transition">Mobile Phones</a>
            <a href="#" class="hover:text-brandBlue transition">Property</a>
            <a href="#" class="hover:text-brandBlue transition">Electronics & Appliances</a>
            <a href="#" class="hover:text-brandBlue transition">Bikes</a>
            <a href="#" class="hover:text-brandBlue transition">Services</a>
            <a href="#" class="hover:text-brandBlue transition">Jobs</a>
            <a href="#" class="hover:text-brandBlue transition">Fashion & Beauty</a>
        </div>
    </nav>

    <!-- Hero Banner -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-navy to-slate-900 rounded-2xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden mb-10">
            <div class="relative z-10 max-w-xl">
                <span class="inline-block bg-accentAmber text-navy text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-4">Official Release</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-3">
                    Explore Big Live Marketplace
                </h1>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-6">
                    Pakistan's next-generation classifieds platform. Browse verified listings, communicate safely with live chat, and buy or sell items effortlessly.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="/sell" class="bg-accentAmber hover:bg-amber-500 text-navy font-bold px-6 py-2.5 rounded-lg text-sm transition">
                        Post Free Ad
                    </a>
                    <a href="/api/v1/health" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm transition">
                        API Status
                    </a>
                </div>
            </div>
        </div>

        <!-- Featured Listings Section -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-navy">Fresh Recommendations</h2>
                    <p class="text-sm text-slate-500">Discover items recently posted by verified sellers</p>
                </div>
                <a href="#" class="text-sm font-semibold text-brandBlue hover:underline">View all</a>
            </div>

            <!-- Dynamic or Empty State Grid -->
            @php
                $listings = \Illuminate\Support\Facades\Schema::hasTable('listings')
                    ? \App\Models\Listing::where('status', 'active')->latest('id')->take(8)->get()
                    : collect();
            @endphp

            @if($listings->isEmpty())
                <div class="bg-white border border-slate-200 rounded-xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 bg-blue-50 text-brandBlue rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-navy mb-1">No Public Listings Yet</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                        The marketplace catalog is ready. Create the first listing or run seeders to populate mock verified items.
                    </p>
                    <a href="/sell" class="inline-flex items-center gap-2 bg-brandBlue hover:bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition">
                        Post Your First Ad
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($listings as $item)
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg transition duration-200 flex flex-col">
                            <div class="h-44 bg-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="text-xs font-semibold uppercase text-brandBlue mb-1">{{ $item->condition_code }}</div>
                                    <h4 class="font-bold text-slate-900 text-base line-clamp-1 mb-1">{{ $item->title }}</h4>
                                    <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $item->description }}</p>
                                </div>
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-lg font-extrabold text-navy">
                                        {{ $item->currency }} {{ number_format(($item->price_minor ?? 0) / 100) }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $item->location_label ?? 'Pakistan' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-slate-400 py-12 mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
            <div>
                <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-xs">Popular Categories</h5>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">Cars</a></li>
                    <li><a href="#" class="hover:text-white transition">Flats for rent</a></li>
                    <li><a href="#" class="hover:text-white transition">Mobile Phones</a></li>
                    <li><a href="#" class="hover:text-white transition">Motorcycles</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-xs">Trending Searches</h5>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">Bikes</a></li>
                    <li><a href="#" class="hover:text-white transition">Watches</a></li>
                    <li><a href="#" class="hover:text-white transition">Books</a></li>
                    <li><a href="#" class="hover:text-white transition">Laptops</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-xs">About Us</h5>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">About Big Live Group</a></li>
                    <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Big Live for Businesses</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-xs">Big Live Marketplace</h5>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">Help & Support</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Sitemap</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs">
            <p>&copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.</p>
            <p class="mt-2 sm:mt-0">Independent Classifieds Platform</p>
        </div>
    </footer>
</body>
</html>
