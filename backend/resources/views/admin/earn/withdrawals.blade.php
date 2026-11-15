<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Withdrawal Review & Payouts | Big Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="max-w-7xl mx-auto p-5 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('admin.earn.dashboard') }}" class="text-indigo-700 underline font-semibold">← Earn Dashboard</a>
            <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-full uppercase">Finance Officers Only</span>
        </div>

        <h1 class="text-3xl font-bold">Withdrawal Payout & Settlement Queue</h1>
        <p class="text-slate-600 mt-2 mb-6">Dual-control financial authorization. Officer 1 records the external transfer reference; Officer 2 independently confirms bank/wallet settlement.</p>

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

        <section class="bg-white p-5 rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b bg-slate-50 text-slate-700">
                        <th class="p-3">ID</th>
                        <th class="p-3">User ID</th>
                        <th class="p-3">Amount (PKR)</th>
                        <th class="p-3">Method</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Transfer Reference</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                        <tr class="border-b align-top hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold">{{ $w->id }}</td>
                            <td class="p-3 font-mono">{{ $w->user_id }}</td>
                            <td class="p-3 font-bold text-slate-900">PKR {{ number_format($w->amount_minor / 100, 2) }}</td>
                            <td class="p-3 uppercase text-xs font-semibold">{{ $w->method }}</td>
                            <td class="p-3">
                                @if($w->status === 'requested')
                                    <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-1 rounded-full font-bold">Awaiting Transfer</span>
                                @elseif($w->status === 'transferred_pending_confirmation')
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-bold">Pending Confirmation</span>
                                @elseif($w->status === 'paid')
                                    <span class="bg-emerald-100 text-emerald-800 text-xs px-2.5 py-1 rounded-full font-bold">Settled & Paid</span>
                                @elseif($w->status === 'rejected')
                                    <span class="bg-rose-100 text-rose-800 text-xs px-2.5 py-1 rounded-full font-bold">Rejected & Refunded</span>
                                @else
                                    <span class="text-slate-600">{{ $w->status }}</span>
                                @endif
                            </td>
                            <td class="p-3 font-mono text-xs text-slate-600">{{ $w->transfer_reference ?: '—' }}</td>
                            <td class="p-3 text-right space-y-2 whitespace-nowrap">
                                @if($w->status === 'requested')
                                    <!-- Officer 1: Record transfer -->
                                    <form method="POST" action="{{ route('admin.earn.withdrawals.transfer', $w->id) }}" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <input type="text" name="transfer_reference" placeholder="Trx ID (JazzCash/Bank)" required maxlength="200" class="border text-xs rounded px-2 py-1 w-44">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                            Record Transfer
                                        </button>
                                    </form>

                                    <!-- Reject Untransferred -->
                                    <form method="POST" action="{{ route('admin.earn.withdrawals.reject', $w->id) }}" class="inline" onsubmit="return confirm('Reject withdrawal and release funds back to user?');">
                                        @csrf
                                        <input type="hidden" name="reason" value="Beneficiary account invalid or unverified">
                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-2.5 py-1.5 rounded-lg shadow-sm">
                                            Reject
                                        </button>
                                    </form>
                                @elseif($w->status === 'transferred_pending_confirmation')
                                    <!-- Officer 2: Confirm settlement -->
                                    <form method="POST" action="{{ route('admin.earn.withdrawals.confirm', $w->id) }}" class="inline" onsubmit="return confirm('Confirm that funds have been settled to the user?');">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                            ✓ Confirm Settle
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Complete</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                No withdrawals pending review or action.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $withdrawals->links() }}</div>
        </section>
    </main>
</body>
</html>
