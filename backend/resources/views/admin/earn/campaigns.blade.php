<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Campaigns | Big Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="max-w-7xl mx-auto p-5 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('admin.earn.dashboard') }}" class="text-indigo-700 underline font-semibold">← Earn Dashboard</a>
            <span class="text-xs bg-indigo-100 text-indigo-800 font-bold px-3 py-1 rounded-full uppercase">Campaign Operations</span>
        </div>

        <h1 class="text-3xl font-bold">Company Campaign Management</h1>
        <p class="text-slate-600 mt-2 mb-6">Create sponsored campaigns. Before user tasks go live, Finance must verify payment receipt.</p>

        @if(session('success'))
            <div role="status" class="bg-green-50 border-l-4 border-green-600 text-green-800 p-4 mb-6 rounded-r-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 mb-6 rounded-r-lg shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="bg-white p-5 rounded-xl shadow-sm mb-7">
            <h2 class="text-xl font-semibold mb-4">Create New Campaign</h2>
            <form method="POST" action="{{ route('admin.earn.campaigns.store') }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                <label class="block">
                    <span class="text-xs font-semibold text-slate-600 uppercase">Company Name</span>
                    <input name="company_name" maxlength="255" required value="{{ old('company_name') }}" class="border rounded w-full p-2 mt-1" placeholder="e.g. Toyota Motors Pakistan">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-slate-600 uppercase">Campaign Title</span>
                    <input name="title" maxlength="255" required value="{{ old('title') }}" class="border rounded w-full p-2 mt-1" placeholder="e.g. Corolla Cross Launch Promotion">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-xs font-semibold text-slate-600 uppercase">Description</span>
                    <textarea name="description" maxlength="5000" rows="3" class="border rounded w-full p-2 mt-1" placeholder="Sponsored campaign details and engagement instructions">{{ old('description') }}</textarea>
                </label>
                <input type="hidden" name="currency" value="PKR">
                <button class="bg-indigo-700 hover:bg-indigo-800 text-white font-semibold rounded-lg px-5 py-2.5 w-fit shadow-sm" type="submit">
                    Create Campaign Draft
                </button>
            </form>
        </section>

        <section class="bg-white p-5 rounded-xl shadow-sm overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4">All Campaigns</h2>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b bg-slate-50 text-slate-700">
                        <th class="p-3">ID</th>
                        <th class="p-3">Company</th>
                        <th class="p-3">Title</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Received (PKR)</th>
                        <th class="p-3">Payment Reference</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $c)
                        <tr class="border-b align-top hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold">{{ $c->id }}</td>
                            <td class="p-3 font-semibold">{{ $c->company_name }}</td>
                            <td class="p-3">{{ $c->title }}</td>
                            <td class="p-3">
                                @if($c->status === 'active')
                                    <span class="bg-emerald-100 text-emerald-800 text-xs px-2.5 py-1 rounded-full font-bold">Active</span>
                                @elseif($c->status === 'draft')
                                    <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-1 rounded-full font-bold">Draft (Unfunded)</span>
                                @else
                                    <span class="bg-slate-100 text-slate-800 text-xs px-2.5 py-1 rounded-full font-bold">{{ $c->status }}</span>
                                @endif
                            </td>
                            <td class="p-3 font-mono">PKR {{ number_format($c->received_minor / 100, 2) }}</td>
                            <td class="p-3 font-mono text-xs text-slate-600">{{ $c->payment_reference ?: '—' }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
                                @if(!$c->payment_verified_at)
                                    <!-- Finance Verification Form -->
                                    <form method="POST" action="{{ route('admin.earn.campaigns.verify', $c->id) }}" class="inline-flex items-center gap-1.5" onsubmit="return confirm('Verify payment receipt and activate campaign?');">
                                        @csrf
                                        <input type="number" name="received_minor" placeholder="Amount (Minor/Cents)" required min="100" class="border text-xs rounded px-2 py-1 w-28">
                                        <input type="text" name="payment_reference" placeholder="Deposit Slip Ref" required maxlength="200" class="border text-xs rounded px-2 py-1 w-32">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                            Verify & Activate
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-semibold text-emerald-600">✓ Verified</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                No campaigns registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $campaigns->links() }}</div>
        </section>
    </main>
</body>
</html>
