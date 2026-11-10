<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Dashboard — Big Live Marketplace</title>
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
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-navy text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2 text-xl font-bold">
                <span class="w-8 h-8 rounded-lg bg-accentAmber flex items-center justify-center text-navy font-black text-lg">B</span>
                <span>Big Live <span class="text-accentAmber">Marketplace</span></span>
            </a>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-300">Signed in as <strong>{{ auth()->user()->name }}</strong></span>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 rounded-lg border border-slate-700 text-xs font-semibold">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-navy">Account Dashboard</h1>
                <p class="text-sm text-slate-500">Manage your active listings, verification, and role permissions.</p>
            </div>
            <a href="/" class="bg-accentAmber hover:bg-amber-500 text-navy font-bold px-4 py-2 rounded-lg text-sm transition">
                Browse Marketplace
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold uppercase text-slate-400 mb-1">Assigned Role</div>
                <div class="text-xl font-black text-brandBlue">
                    {{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'BUYER') }}
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold uppercase text-slate-400 mb-1">Account Status</div>
                <div class="text-xl font-black text-emerald-600 capitalize">
                    {{ auth()->user()->status }}
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold uppercase text-slate-400 mb-1">Default Currency</div>
                <div class="text-xl font-black text-navy">
                    {{ auth()->user()->preferred_currency }}
                </div>
            </div>
        </div>

        <!-- Role Badges & Details -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm mb-8">
            <h3 class="text-lg font-bold text-navy mb-4">Profile & Verified Credentials</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-400">Full Name:</span> <strong class="text-slate-800">{{ auth()->user()->name }}</strong></div>
                <div><span class="text-slate-400">Email:</span> <strong class="text-slate-800">{{ auth()->user()->email }}</strong></div>
                <div><span class="text-slate-400">Phone (E.164):</span> <strong class="text-slate-800">{{ auth()->user()->phone_e164 ?? 'Not set' }}</strong></div>
                <div><span class="text-slate-400">Language:</span> <strong class="text-slate-800">{{ strtoupper(auth()->user()->locale) }}</strong></div>
            </div>
        </div>
    </main>

    <footer class="bg-navy text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        &copy; {{ date('Y') }} Big Live Marketplace. All rights reserved.
    </footer>
</body>
</html>
