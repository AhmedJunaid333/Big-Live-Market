<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Big Live Marketplace</title>
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
<body class="bg-slate-50 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="/" class="inline-flex items-center gap-2 text-2xl font-black text-navy">
            <span class="w-10 h-10 rounded-xl bg-accentAmber flex items-center justify-center text-navy font-black text-xl shadow">B</span>
            <span>Big Live <span class="text-brandBlue">Marketplace</span></span>
        </a>
        <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">Sign in to your account</h2>
        <p class="mt-2 text-sm text-slate-600">
            Or <a href="/" class="font-medium text-brandBlue hover:underline">return to home marketplace</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-slate-200 sm:px-10">
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form class="space-y-6" action="/login" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               value="{{ old('email', 'admin@biglive.pk') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required 
                               value="Admin@12345"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brandBlue focus:ring-brandBlue">
                        <label for="remember" class="ml-2 block text-slate-900">Remember me</label>
                    </div>
                    <a href="#" class="font-medium text-brandBlue hover:underline">Forgot password?</a>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-brandBlue hover:bg-blue-600 transition">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-200">
                <h4 class="text-xs font-semibold uppercase text-slate-400 tracking-wider mb-3">Pre-Configured Demo Accounts</h4>
                <div class="space-y-2 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <div>👑 <strong>Super Admin:</strong> <code class="text-brandBlue font-mono">admin@biglive.pk</code> / <code class="font-mono">Admin@12345</code></div>
                    <div>⭐ <strong>Verified Seller:</strong> <code class="text-brandBlue font-mono">seller@biglive.pk</code> / <code class="font-mono">Seller@12345</code></div>
                    <div>👤 <strong>Regular Buyer:</strong> <code class="text-brandBlue font-mono">buyer@biglive.pk</code> / <code class="font-mono">Buyer@12345</code></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
