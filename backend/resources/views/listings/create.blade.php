<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post An Exclusive Listing — Big Live Marketplace</title>
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
    <header class="bg-obsidian text-white border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-20">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gold-gradient flex items-center justify-center text-obsidian font-black text-xl shadow-gold-glow">B</div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-white font-sans flex items-center gap-1.5">
                        Big Live <span class="gold-text-gradient font-serif italic text-2xl font-normal">Marketplace</span>
                    </span>
                    <span class="text-[10px] text-amber-300/60 uppercase tracking-widest font-semibold">Prestige Classifieds</span>
                </div>
            </a>
            <div class="flex items-center gap-4 text-xs font-semibold">
                @auth
                    <span class="text-slate-300">Publishing as <strong class="text-white">{{ auth()->user()->name }}</strong></span>
                @else
                    <a href="/login" class="text-amber-200 hover:text-white transition">Sign In</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-12 flex-1 w-full">
        <div class="mb-10 text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-champagne-600 block mb-2">Direct Monetization & Reach</span>
            <h1 class="text-3xl sm:text-4xl font-black text-obsidian tracking-tight">POST YOUR AD</h1>
            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Present your items with high resolution, transparent pricing, and instant buyer engagement.</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-12 shadow-soft-luxury">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-xs">
                    <ul class="list-disc pl-5 space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/sell" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Listing Title *</label>
                    <input type="text" name="title" required minlength="5" maxlength="180" 
                           placeholder="e.g. Mercedes Benz E200 AMG Line Low Mileage" 
                           value="{{ old('title') }}"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                    <span class="text-[11px] text-slate-400 mt-1.5 block">Include key attributes: make, model, year, and condition.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Category *</label>
                        <select name="category_id" required class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition cursor-pointer">
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">City / Location *</label>
                        <select name="city_id" required class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition cursor-pointer">
                            @foreach(\App\Models\City::all() as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Price (PKR) *</label>
                        <input type="number" name="price" required min="1" placeholder="e.g. 8500000" 
                               value="{{ old('price') }}"
                               class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Condition *</label>
                        <select name="condition_code" required class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition cursor-pointer">
                            <option value="used">Used / Pre-Owned</option>
                            <option value="new">Brand New (Sealed)</option>
                            <option value="refurbished">Refurbished / Certified</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Specific Neighborhood / Landmark</label>
                    <input type="text" name="location_label" placeholder="e.g. Gulberg III / DHA Phase 5, Lahore" 
                           value="{{ old('location_label') }}"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Full Description *</label>
                    <textarea name="description" rows="5" required minlength="20" maxlength="5000" 
                              placeholder="Describe your item, key highlights, warranty status, package contents, and reasons for selling..."
                              class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">{{ old('description') }}</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full gold-gradient hover:opacity-95 text-obsidian font-extrabold py-4 px-6 rounded-xl text-xs tracking-wider shadow-gold-glow uppercase transition hover:scale-[1.01]">
                        Publish Advertisement Now
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-obsidian text-slate-400 py-6 text-center text-xs border-t border-amber-500/20">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
