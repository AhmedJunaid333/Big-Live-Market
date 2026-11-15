<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sponsored Earn Admin | Big Live Marketplace</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<main class="max-w-7xl mx-auto p-4 md:p-8">
<header class="mb-8 flex flex-wrap items-center justify-between gap-4"><div><p class="text-sm text-indigo-600 font-semibold">BIG LIVE MARKETPLACE</p><h1 class="text-3xl font-bold">Sponsored Earn Administration</h1><p class="text-slate-600 mt-2">Role-restricted read-only overview. Financial actions require separate verification.</p></div><a href="/dashboard" class="underline">Marketplace dashboard</a></header>
<div class="grid md:grid-cols-3 gap-4 mb-8">
<div class="bg-white rounded-xl p-5 shadow-sm"><p class="text-slate-500">Campaigns shown</p><strong class="text-3xl">{{ $campaigns->count() }}</strong></div>
<div class="bg-white rounded-xl p-5 shadow-sm"><p class="text-slate-500">Pending submissions shown</p><strong class="text-3xl">{{ $submissions->count() }}</strong></div>
<div class="bg-white rounded-xl p-5 shadow-sm"><p class="text-slate-500">Withdrawals awaiting action shown</p><strong class="text-3xl">{{ $withdrawals->count() }}</strong></div>
</div>
@if($canManage)
<section class="bg-white rounded-xl shadow-sm mb-6 p-5 overflow-x-auto"><h2 class="font-bold text-xl mb-3">Company campaigns (latest 25)</h2><table class="w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">ID</th><th class="p-2">Company</th><th class="p-2">Campaign</th><th class="p-2">Status</th><th class="p-2">Payment verified</th></tr></thead><tbody>@forelse($campaigns as $campaign)<tr class="border-b"><td class="p-2">{{ $campaign->id }}</td><td class="p-2">{{ $campaign->company_name }}</td><td class="p-2">{{ $campaign->title }}</td><td class="p-2">{{ $campaign->status }}</td><td class="p-2">{{ $campaign->payment_verified_at ? 'Yes' : 'No' }}</td></tr>@empty<tr><td colspan="5" class="p-3 text-slate-500">No campaigns yet.</td></tr>@endforelse</tbody></table></section>
<section class="bg-white rounded-xl shadow-sm mb-6 p-5 overflow-x-auto"><h2 class="font-bold text-xl mb-3">Pending task submissions (oldest 25)</h2><table class="w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">ID</th><th class="p-2">Task</th><th class="p-2">User ID</th><th class="p-2">Status</th></tr></thead><tbody>@forelse($submissions as $submission)<tr class="border-b"><td class="p-2">{{ $submission->id }}</td><td class="p-2">{{ $submission->task_title }}</td><td class="p-2">{{ $submission->user_id }}</td><td class="p-2">{{ $submission->status }}</td></tr>@empty<tr><td colspan="4" class="p-3 text-slate-500">No pending submissions.</td></tr>@endforelse</tbody></table></section>
@endif
@if($canFinance)
<section class="bg-white rounded-xl shadow-sm mb-6 p-5 overflow-x-auto"><h2 class="font-bold text-xl mb-3">Manual withdrawals (latest 25)</h2><table class="w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">ID</th><th class="p-2">User ID</th><th class="p-2">Amount (PKR)</th><th class="p-2">Method</th><th class="p-2">Status</th></tr></thead><tbody>@forelse($withdrawals as $withdrawal)<tr class="border-b"><td class="p-2">{{ $withdrawal->id }}</td><td class="p-2">{{ $withdrawal->user_id }}</td><td class="p-2">{{ number_format($withdrawal->amount_minor / 100, 2) }}</td><td class="p-2">{{ $withdrawal->method }}</td><td class="p-2">{{ $withdrawal->status }}</td></tr>@empty<tr><td colspan="5" class="p-3 text-slate-500">No withdrawals awaiting action.</td></tr>@endforelse</tbody></table></section>
@endif
<p class="text-sm text-slate-500">This is an initial read-only admin view. Approval, payment and payout controls will be added after security checks and tests.</p>
</main></body></html>
