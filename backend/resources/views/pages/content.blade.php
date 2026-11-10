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
                <a href="/login" class="text-amber-200 hover:text-white transition">Sign In</a>
                <a href="/sell" class="gold-gradient text-obsidian font-bold px-4 py-2 rounded-xl shadow-gold-glow">+ POST AD</a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-12 flex-1 w-full">
        <div class="bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-14 shadow-soft-luxury">
            <span class="text-xs font-bold uppercase tracking-widest text-champagne-600 block mb-2">Legal & Institutional Information</span>
            <h1 class="text-3xl sm:text-4xl font-black text-obsidian mb-8 tracking-tight">{{ $title }}</h1>
            <div class="prose max-w-none text-slate-700 leading-relaxed text-sm sm:text-base space-y-5">
                {!! $content !!}
            </div>
            <div class="mt-10 pt-6 border-t border-slate-100">
                <a href="/" class="text-xs font-bold uppercase tracking-wider text-champagne-600 hover:text-champagne-700">← Return to Marketplace</a>
            </div>
        </div>
    </main>

    <footer class="bg-obsidian text-slate-400 py-6 text-center text-xs border-t border-amber-500/20">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
