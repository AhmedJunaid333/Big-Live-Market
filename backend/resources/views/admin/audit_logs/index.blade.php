@extends('layouts.admin')

@section('title', 'Audit Trail Logs')
@section('page_title', 'Security & Audit Trail')
@section('page_subtitle', 'System activity, administrative events, and state mutations')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Security Audit Trail</h3>
            <span class="text-xs text-slate-400">Total: {{ $logs->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">Log ID</th>
                        <th class="p-3.5 font-semibold">Actor</th>
                        <th class="p-3.5 font-semibold">Action</th>
                        <th class="p-3.5 font-semibold">Target</th>
                        <th class="p-3.5 font-semibold">Details</th>
                        <th class="p-3.5 font-semibold">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                    @forelse($logs as $l)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 text-slate-500">#{{ $l->id }}</td>
                            <td class="p-3.5 text-slate-800 font-semibold">{{ $l->actor?->name ?? 'System' }}</td>
                            <td class="p-3.5 text-brand-blue font-bold">{{ $l->action }}</td>
                            <td class="p-3.5 text-slate-600">{{ $l->subject_type }} #{{ $l->subject_id }}</td>
                            <td class="p-3.5 text-slate-500 max-w-xs truncate">{{ json_encode($l->metadata_json) }}</td>
                            <td class="p-3.5 text-slate-400 font-sans text-xs">{{ $l->created_at?->toDayDateTimeString() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-sans text-xs">No audit logs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
