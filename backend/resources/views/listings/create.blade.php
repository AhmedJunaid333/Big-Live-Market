<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post Your Ad — Big Live Marketplace</title>
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
    <header class="bg-navy text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2 text-xl font-bold">
                <span class="w-8 h-8 rounded-lg bg-accentAmber flex items-center justify-center text-navy font-black text-lg">B</span>
                <span>Big Live <span class="text-accentAmber">Marketplace</span></span>
            </a>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <span class="text-slate-300">Posting as <strong>{{ auth()->user()->name }}</strong></span>
                @else
                    <a href="/login" class="text-slate-300 hover:text-white">Sign In</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8 flex-1 w-full">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-navy">POST YOUR AD</h1>
            <p class="text-sm text-slate-500 mt-1">Reach millions of buyers across Pakistan effortlessly</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-10 shadow-sm">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/sell" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-navy mb-1">Ad Title *</label>
                    <input type="text" name="title" required minlength="5" maxlength="180" 
                           placeholder="e.g. Honda Civic Oriel 2022 Low Mileage" 
                           value="{{ old('title') }}"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                    <span class="text-xs text-slate-400 mt-1 block">Mention key details like brand, model, and condition</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-navy mb-1">Category *</label>
                        <select name="category_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-navy mb-1">City *</label>
                        <select name="city_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                            @foreach(\App\Models\City::all() as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-navy mb-1">Price (PKR) *</label>
                        <input type="number" name="price" required min="1" placeholder="e.g. 50000" 
                               value="{{ old('price') }}"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-navy mb-1">Condition *</label>
                        <select name="condition_code" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                            <option value="used">Used</option>
                            <option value="new">New</option>
                            <option value="refurbished">Refurbished</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-navy mb-1">Location Details</label>
                    <input type="text" name="location_label" placeholder="e.g. DHA Phase 6, Lahore" 
                           value="{{ old('location_label') }}"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                </div>

                <div>
                    <label class="block text-sm font-bold text-navy mb-1">Description *</label>
                    <textarea name="description" rows="5" required minlength="20" maxlength="5000" 
                              placeholder="Include condition, reason for sale, features, and package contents..."
                              class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">{{ old('description') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                    <a href="/" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancel</a>
                    <button type="submit" class="bg-brandBlue hover:bg-blue-600 text-white font-bold px-8 py-2.5 rounded-lg text-sm shadow transition">
                        Post Ad Now
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
