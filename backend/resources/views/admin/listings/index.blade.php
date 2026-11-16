@extends('layouts.admin')

@section('title', 'Manage Listings')
@section('page_title', 'Marketplace Ads & Moderation Queue')
@section('page_subtitle', 'Review, approve, reject, and inspect seller listings across all categories')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- STATUS TABS & METRICS -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2 p-1.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <a href="{{ route('admin.listings') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'all' ? 'bg-navy-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                All Ads ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.listings', ['status' => 'pending_review']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'pending_review' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Pending Review ({{ $counts['pending_review'] }})
            </a>
            <a href="{{ route('admin.listings', ['status' => 'active']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Active ({{ $counts['active'] }})
            </a>
            <a href="{{ route('admin.listings', ['status' => 'rejected']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Rejected ({{ $counts['rejected'] }})
            </a>
        </div>

        <!-- SEARCH & CATEGORY FILTER -->
        <form method="GET" action="{{ route('admin.listings') }}" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <select name="category_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 p-2 bg-white text-slate-700">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title or seller..." class="text-xs rounded-xl border border-slate-300 p-2 bg-white w-48 sm:w-64 focus:border-brand-blue">
            <button type="submit" class="px-3 py-2 rounded-xl bg-brand-blue text-white text-xs font-bold shadow-xs">
                Search
            </button>
        </form>
    </div>

    <!-- LISTINGS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">Ad Details</th>
                        <th class="p-3.5 font-semibold">Category</th>
                        <th class="p-3.5 font-semibold">Price (PKR)</th>
                        <th class="p-3.5 font-semibold">Seller</th>
                        <th class="p-3.5 font-semibold">City</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($listings as $listing)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 flex-shrink-0">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <a href="/listings/{{ $listing->id }}" target="_blank" class="font-bold text-slate-900 hover:text-brand-blue transition line-clamp-1">
                                            {{ $listing->title }}
                                        </a>
                                        <p class="text-[10px] text-slate-400 mt-0.5">ID: #{{ $listing->id }} • {{ $listing->created_at?->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 text-slate-600 font-medium">
                                {{ $listing->category?->name ?? 'Unassigned' }}
                            </td>
                            <td class="p-3.5 font-extrabold text-slate-800">
                                {{ number_format($listing->price_minor / 100) }}
                            </td>
                            <td class="p-3.5 text-slate-600">
                                <span class="font-semibold text-slate-800">{{ $listing->seller?->name ?? 'N/A' }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $listing->seller?->email }}</span>
                            </td>
                            <td class="p-3.5 text-slate-500">
                                {{ $listing->city?->name ?? $listing->location_label ?? 'Pakistan' }}
                            </td>
                            <td class="p-3.5">
                                @if($listing->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Live Active</span>
                                @elseif($listing->status === 'pending_review')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending Review</span>
                                @elseif($listing->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 capitalize">{{ $listing->status }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($listing->status !== 'active')
                                        <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    @if($listing->status !== 'rejected')
                                        <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="reason" value="Rejected by administrator">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold transition">
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                No classified listings found matching this criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $listings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
