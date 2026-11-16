@extends('layouts.admin')

@section('title', 'Abuse Reports')
@section('page_title', 'Marketplace Safety & Abuse Reports')
@section('page_subtitle', 'Review user disputes, spam flags, and suspicious activities')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <div class="flex items-center gap-2 p-1.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs max-w-fit">
        <a href="{{ route('admin.reports', ['status' => 'open']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'open' ? 'bg-navy-800 text-white' : 'text-slate-600 hover:text-slate-900' }}">
            Open Reports
        </a>
        <a href="{{ route('admin.reports', ['status' => 'resolved']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'resolved' ? 'bg-navy-800 text-white' : 'text-slate-600 hover:text-slate-900' }}">
            Resolved
        </a>
        <a href="{{ route('admin.reports', ['status' => 'all']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'all' ? 'bg-navy-800 text-white' : 'text-slate-600 hover:text-slate-900' }}">
            All Reports
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">Target</th>
                        <th class="p-3.5 font-semibold">Reported By</th>
                        <th class="p-3.5 font-semibold">Reason & Description</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold">Date</th>
                        <th class="p-3.5 font-semibold text-right">Moderation Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $r)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5">
                                <span class="font-bold text-slate-800 uppercase text-[10px] px-1.5 py-0.5 rounded bg-slate-100">{{ $r->target_type }}</span>
                                <span class="text-xs font-semibold text-slate-700 block mt-1">ID: #{{ $r->target_id }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600">
                                {{ $r->reporter?->name ?? 'User #' . $r->reporter_id }}
                            </td>
                            <td class="p-3.5 max-w-xs">
                                <span class="font-bold text-slate-800 block">{{ $r->reason }}</span>
                                <span class="text-[11px] text-slate-400 line-clamp-1">{{ $r->description ?? 'No extra notes' }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $r->status === 'open' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-400">{{ $r->created_at?->diffForHumans() }}</td>
                            <td class="p-3.5 text-right">
                                @if($r->status === 'open')
                                    <form action="{{ route('admin.reports.resolve', $r->id) }}" method="POST" class="inline-flex gap-1.5">
                                        @csrf
                                        <input type="hidden" name="reason" value="Resolved by admin review">
                                        <button type="submit" name="action" value="dismiss" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                            Dismiss
                                        </button>
                                        <button type="submit" name="action" value="warn_user" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-[11px]">
                                            Warn
                                        </button>
                                        @if($r->target_type === 'listing')
                                            <button type="submit" name="action" value="remove_listing" class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px]">
                                                Remove Ad
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-slate-400 text-[11px]">Resolved</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">No abuse reports in this category.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
