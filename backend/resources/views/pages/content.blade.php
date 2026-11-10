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

    <main class="max-w-4xl mx-auto px-4 py-12 flex-1 w-full">
        <div class="bg-white border border-slate-200 rounded-2xl p-8 sm:p-12 shadow-sm">
            <h1 class="text-3xl font-black text-navy mb-6">{{ $title }}</h1>
            <div class="prose max-w-none text-slate-700 leading-relaxed text-sm sm:text-base space-y-4">
                {!! $content !!}
            </div>
            <div class="mt-8 pt-6 border-t border-slate-200">
                <a href="/" class="text-sm font-bold text-brandBlue hover:underline">← Return to Marketplace</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
