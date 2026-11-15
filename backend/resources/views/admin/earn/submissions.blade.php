<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sponsored Task Reviews | Big Live</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 text-slate-900"><main class="max-w-6xl mx-auto p-5 md:p-8">
<a href="{{ route('admin.earn.dashboard') }}" class="text-indigo-700 underline">← Earn dashboard</a>
<h1 class="text-3xl font-bold mt-5">Sponsored Task Review Queue</h1>
<p class="text-slate-600 mt-2 mb-6">Review genuine user-submitted proof. Reward approval is intentionally disabled until accounting and concurrency tests pass.</p>
<section class="bg-white shadow-sm rounded-xl overflow-x-auto p-5">
<table class="w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">Submission</th><th class="p-2">Company</th><th class="p-2">Task</th><th class="p-2">User ID</th><th class="p-2">Reward</th><th class="p-2">Proof</th><th class="p-2">Submitted</th></tr></thead>
<tbody>@forelse($submissions as $s)<tr class="border-b align-top"><td class="p-2">{{ $s->id }}</td><td class="p-2">{{ $s->company_name }}</td><td class="p-2">{{ $s->task_title }}</td><td class="p-2">{{ $s->user_id }}</td><td class="p-2">{{ number_format($s->reward_minor / 100, 2) }} {{ $s->currency }}</td><td class="p-2 max-w-sm whitespace-pre-wrap break-words">{{ $s->proof }}</td><td class="p-2">{{ $s->created_at }}</td></tr>@empty<tr><td colspan="7" class="p-3 text-slate-500">No pending submissions.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $submissions->links() }}</div></section>
</main></body></html>