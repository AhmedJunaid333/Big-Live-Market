<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found (404) — Big Live Marketplace</title>
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
            <div class="flex items-center gap-3">
                <a href="/login" class="text-sm font-medium hover:text-accentAmber transition">Login</a>
                <a href="/sell" class="bg-accentAmber hover:bg-amber-500 text-navy font-bold px-4 py-2 rounded-lg text-sm shadow transition">+ SELL</a>
            </div>
        </div>
    </header>

    <!-- 404 Body -->
    <main class="max-w-xl mx-auto px-4 py-16 text-center">
        <div class="w-24 h-24 bg-amber-100 text-accentAmber rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <span class="text-4xl font-black">404</span>
        </div>
        <h1 class="text-3xl font-extrabold text-navy mb-3">Oops! Page Not Found</h1>
        <p class="text-slate-600 mb-8 leading-relaxed">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>

        <!-- Search Form inside 404 -->
        <form action="/search" method="GET" class="flex gap-2 max-w-md mx-auto mb-8">
            <input type="text" name="q" placeholder="Search cars, mobiles, property..." 
                   class="flex-1 border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brandBlue shadow-sm">
            <button type="submit" class="bg-brandBlue hover:bg-blue-600 text-white font-bold px-5 py-2 rounded-lg text-sm transition">
                Search
            </button>
        </form>

        <div class="flex justify-center gap-4">
            <a href="/" class="inline-flex items-center gap-2 bg-navy hover:bg-slate-800 text-white font-bold px-6 py-2.5 rounded-lg text-sm shadow transition">
                Return to Home
            </a>
            <a href="/sell" class="inline-flex items-center gap-2 bg-accentAmber hover:bg-amber-500 text-navy font-bold px-6 py-2.5 rounded-lg text-sm shadow transition">
                Post an Ad
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
