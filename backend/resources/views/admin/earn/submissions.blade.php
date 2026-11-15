<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sponsored Task Reviews | Big Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="max-w-7xl mx-auto p-5 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('admin.earn.dashboard') }}" class="text-indigo-700 underline font-semibold">← Earn Dashboard</a>
            <span class="text-xs bg-indigo-100 text-indigo-800 font-bold px-3 py-1 rounded-full uppercase">Admin Queue</span>
        </div>

        <h1 class="text-3xl font-bold">Sponsored Task Review Queue</h1>
        <p class="text-slate-600 mt-2 mb-6">Review authentic user task submissions. Approving a submission instantly credits the user's earning wallet.</p>

        @if(session('success'))
            <div role="status" class="bg-green-50 border-l-4 border-green-600 text-green-800 p-4 mb-6 rounded-r-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <section class="bg-white shadow-sm rounded-xl overflow-x-auto p-5">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b bg-slate-50 text-slate-700">
                        <th class="p-3">ID</th>
                        <th class="p-3">Company</th>
                        <th class="p-3">Task Title</th>
                        <th class="p-3">User ID</th>
                        <th class="p-3">Reward</th>
                        <th class="p-3">Proof / Submission</th>
                        <th class="p-3">Submitted At</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                        <tr class="border-b align-top hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold">{{ $s->id }}</td>
                            <td class="p-3 font-semibold">{{ $s->company_name }}</td>
                            <td class="p-3">{{ $s->task_title }}</td>
                            <td class="p-3 font-mono">{{ $s->user_id }}</td>
                            <td class="p-3 font-bold text-green-600">{{ number_format($s->reward_minor / 100, 2) }} {{ $s->currency }}</td>
                            <td class="p-3 max-w-xs whitespace-pre-wrap break-words text-slate-700 bg-slate-50 rounded p-2 text-xs font-mono">{{ $s->proof }}</td>
                            <td class="p-3 text-xs text-slate-500">{{ $s->created_at }}</td>
                            <td class="p-3 text-right space-x-2 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.earn.submissions.review', $s->id) }}" class="inline" onsubmit="return confirm('Approve this submission and credit funds?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="approved">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                        ✓ Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.earn.submissions.review', $s->id) }}" class="inline" onsubmit="return confirm('Reject this submission?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="rejected">
                                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                        ✕ Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                No pending task submissions in review queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $submissions->links() }}</div>
        </section>
    </main>
</body>
</html>
