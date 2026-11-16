<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Free Account — Big Live Marketplace Prestige</title>
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
<body class="bg-[#F8F9FC] min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans selection:bg-champagne-500 selection:text-obsidian">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="/" class="inline-flex items-center gap-3 group">
            <img src="{{ asset('images/logo.png') }}" alt="Big Live Market" class="w-14 h-14 rounded-2xl object-cover shadow-gold-glow group-hover:scale-105 transition-transform ring-2 ring-amber-400/40">
            <div class="flex flex-col text-left">
                <span class="text-2xl font-bold tracking-tight text-obsidian font-sans">
                    Big Live <span class="gold-text-gradient font-serif italic text-2xl font-normal">Marketplace</span>
                </span>
                <span class="text-[10px] text-champagne-600 uppercase tracking-widest font-semibold">Prestige Classifieds</span>
            </div>
        </a>
        <h2 class="mt-6 text-2xl font-black tracking-tight text-obsidian">Create your free account</h2>
        <p class="mt-2 text-xs text-slate-500 font-medium">
            Already have an account? <a href="/login" class="text-champagne-600 hover:underline font-bold">Sign in</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-9 px-6 sm:px-10 shadow-soft-luxury rounded-3xl border border-slate-200/80">
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-semibold">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-4" action="/register" method="POST">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Full Name *</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                           placeholder="e.g. Tariq Mehmood"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           placeholder="name@domain.com"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div>
                    <label for="phone_e164" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Mobile Phone (E.164)</label>
                    <input id="phone_e164" name="phone_e164" type="text" value="{{ old('phone_e164') }}"
                           placeholder="+923001234567"
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password (Min 8 characters) *</label>
                    <input id="password" name="password" type="password" required 
                           class="w-full bg-slate-50/50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-champagne-500 focus:ring-2 focus:ring-champagne-500/20 transition">
                </div>

                <div class="text-[11px] text-slate-500 pt-1 leading-relaxed">
                    By registering you agree to our 
                    <a href="/terms" class="text-champagne-600 hover:underline font-bold">Terms of Service</a> and 
                    <a href="/privacy" class="text-champagne-600 hover:underline font-bold">Privacy Policy</a>.
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full gold-gradient hover:opacity-95 text-obsidian font-extrabold py-3.5 px-4 rounded-xl text-xs tracking-wider uppercase shadow-gold-glow transition hover:scale-[1.01]">
                        Create Exclusive Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
