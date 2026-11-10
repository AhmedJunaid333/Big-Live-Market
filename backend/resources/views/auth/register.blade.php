<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create an Account — Big Live Marketplace</title>
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
        <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">Create your free account</h2>
        <p class="mt-2 text-sm text-slate-600">
            Already have an account? <a href="/login" class="font-medium text-brandBlue hover:underline">Sign in</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-slate-200 sm:px-10">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
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
                    <label for="name" class="block text-sm font-medium text-slate-700">Full Name</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                           placeholder="e.g. Tariq Mehmood"
                           class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           placeholder="name@example.com"
                           class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                </div>

                <div>
                    <label for="phone_e164" class="block text-sm font-medium text-slate-700">Phone Number (E.164)</label>
                    <input id="phone_e164" name="phone_e164" type="text" value="{{ old('phone_e164') }}"
                           placeholder="+923001234567"
                           class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password (min 8 chars)</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brandBlue">
                </div>

                <div class="text-xs text-slate-500 pt-2">
                    By registering, you agree to Big Live Marketplace's 
                    <a href="/terms" class="text-brandBlue hover:underline">Terms of Service</a> and 
                    <a href="/privacy" class="text-brandBlue hover:underline">Privacy Policy</a>.
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-brandBlue hover:bg-blue-600 transition">
                        Register Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
