<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Merchant & Member Dashboard — Big Live Marketplace Prestige</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
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
<body class="bg-[#F8F9FC] text-slate-800 font-sans min-h-screen flex flex-col selection:bg-champagne-500 selection:text-obsidian">
    <!-- Navbar -->
    <header class="bg-obsidian text-white border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-20">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Big Live Market" class="w-12 h-12 rounded-xl object-cover shadow-gold-glow ring-2 ring-amber-400/30">
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-white font-sans flex items-center gap-1.5">
                        Big Live <span class="gold-text-gradient font-serif italic text-2xl font-normal">Marketplace</span>
                    </span>
                    <span class="text-[10px] text-amber-300/60 uppercase tracking-widest font-semibold">Prestige Classifieds</span>
                </div>
            </a>
            <div class="flex items-center gap-4 text-xs">
                <span class="text-slate-300 hidden sm:inline">Signed in as <strong class="text-white">{{ auth()->user()->name }}</strong></span>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-amber-200 px-3.5 py-2 rounded-xl border border-slate-700 text-xs font-bold tracking-wider transition">
                        SIGN OUT
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-champagne-600 mb-1 block">Account Center</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight">Executive Dashboard</h1>
                <p class="text-xs text-slate-500 mt-1">Review your active adverts, reputation score, and verified profile status.</p>
            </div>
            <a href="/" class="gold-gradient hover:opacity-95 text-obsidian font-bold px-5 py-2.5 rounded-xl text-xs tracking-wider shadow-gold-glow transition">
                Browse Live Marketplace
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-soft-luxury">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Assigned Membership Role</div>
                <div class="text-2xl font-black text-obsidian flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-champagne-500"></span>
                    {{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'BUYER') }}
                </div>
            </div>
            <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-soft-luxury">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Verification & Standing</div>
                <div class="text-2xl font-black text-emerald-600 capitalize flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    {{ auth()->user()->status }}
                </div>
            </div>
            <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-soft-luxury">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Settlement Currency</div>
                <div class="text-2xl font-black text-obsidian">
                    {{ auth()->user()->preferred_currency }}
                </div>
            </div>
        </div>

        <!-- Profile Details Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-soft-luxury">
            <h3 class="text-lg font-bold text-obsidian mb-6 tracking-tight flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-champagne-500"></span>
                Profile Credentials & Preferences
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Full Name</span>
                    <strong class="text-slate-800 text-sm font-bold">{{ auth()->user()->name }}</strong>
                </div>
                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Email Address</span>
                    <strong class="text-slate-800 text-sm font-bold">{{ auth()->user()->email }}</strong>
                </div>
                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Contact Mobile (E.164)</span>
                    <strong class="text-slate-800 text-sm font-bold">{{ auth()->user()->phone_e164 ?? 'Not registered' }}</strong>
                </div>
                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-1 uppercase font-semibold text-[10px]">Interface Locale</span>
                    <strong class="text-slate-800 text-sm font-bold">{{ strtoupper(auth()->user()->locale) }}</strong>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-obsidian text-slate-400 py-6 text-center text-xs border-t border-amber-500/20">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
