<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Big Live Marketplace Prestige</title>
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
<body class="bg-[#F8F9FC] min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-champagne-500 selection:text-obsidian">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="/" class="inline-flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-xl gold-gradient flex items-center justify-center text-obsidian font-black text-2xl shadow-gold-glow group-hover:scale-105 transition-transform">
                B
            </div>
            <div class="flex flex-col text-left">
                <span class="text-2xl font-bold tracking-tight text-obsidian font-sans">
                    Big Live <span class="gold-text-gradient font-serif italic text-2xl font-normal">Marketplace</span>
                </span>
                <span class="text-[10px] text-champagne-600 uppercase tracking-widest font-semibold">Prestige Classifieds</span>
            </div>
        </a>
        <h2 class="mt-6 text-2xl font-black tracking-tight text-obsidian">Sign In to Your Account</h2>
        <p class="mt-2 text-xs text-slate-500 font-medium">
            Or <a href="/" class="text-champagne-600 hover:underline font-bold">return to the marketplace homepage</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-9 px-6 sm:px-10 shadow-soft-luxury rounded-3xl border border-slate-200/80">
            @if(session('error'))
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <form class="space-y-5" action="/login" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           value="{{ old('email', 'admin@biglive.pk') }}"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                    <input id="password" name="password" type="password" required 
                           value="Admin@12345"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input id="remember" name="remember" type="checkbox" class="rounded border-slate-300 text-champagne-600 focus:ring-champagne-500">
                        <span>Remember credentials</span>
                    </label>
                    <a href="#" class="text-champagne-600 hover:underline font-bold">Need assistance?</a>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full gold-gradient hover:opacity-95 text-obsidian font-extrabold py-3.5 px-4 rounded-xl text-xs tracking-wider uppercase shadow-gold-glow transition hover:scale-[1.01]">
                        Sign in to Account
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <span class="text-[10px] font-bold uppercase text-champagne-600 tracking-widest block mb-2.5">Instant Demo Credentials</span>
                <div class="space-y-2 text-xs text-slate-600 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-200/80">
                    <div class="flex items-center justify-between">
                        <span>👑 <strong>Super Admin:</strong></span>
                        <code class="text-obsidian font-mono text-[11px] font-bold">admin@biglive.pk</code>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>⭐ <strong>Verified Seller:</strong></span>
                        <code class="text-obsidian font-mono text-[11px] font-bold">seller@biglive.pk</code>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>👤 <strong>Regular Buyer:</strong></span>
                        <code class="text-obsidian font-mono text-[11px] font-bold">buyer@biglive.pk</code>
                    </div>
                    <div class="text-[10px] text-slate-400 text-center pt-1 border-t border-slate-200/50">
                        Password for all accounts: <strong class="text-slate-700 font-mono">[Role]@12345</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
