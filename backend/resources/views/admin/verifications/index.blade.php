@extends('layouts.admin')

@section('title', 'KYC & Seller Verifications')
@section('page_title', 'Seller Trust & KYC Queue')
@section('page_subtitle', 'Review merchant identity documents to grant the Verified Seller badge')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Identity & Business Verification Requests</h3>
            <span class="text-xs text-slate-400">Total: {{ $verifications->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">User</th>
                        <th class="p-3.5 font-semibold">Doc Type</th>
                        <th class="p-3.5 font-semibold">Document Info</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold">Submitted</th>
                        <th class="p-3.5 font-semibold text-right">Decision</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($verifications as $v)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5">
                                <span class="font-bold text-slate-800 block">{{ $v->user?->name ?? 'User #' . $v->user_id }}</span>
                                <span class="text-[11px] text-slate-400">{{ $v->user?->email }}</span>
                            </td>
                            <td class="p-3.5 font-medium text-slate-700 capitalize">{{ $v->document_type ?? 'CNIC / Passport' }}</td>
                            <td class="p-3.5 text-slate-500">{{ $v->document_number ?? 'Attached in file' }}</td>
                            <td class="p-3.5">
                                @if($v->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>
                                @elseif($v->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Pending Review</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-400">{{ $v->created_at?->diffForHumans() }}</td>
                            <td class="p-3.5 text-right">
                                @if($v->status === 'pending')
                                    <div class="inline-flex items-center gap-1.5">
                                        <form action="{{ route('admin.verifications.decide', $v->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="decision" value="approved">
                                            <button type="submit" class="px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition text-[11px]">
                                                Verify
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.verifications.decide', $v->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="decision" value="rejected">
                                            <input type="hidden" name="rejection_reason" value="Document unclear or expired">
                                            <button type="submit" class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold transition text-[11px]">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Decided</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">No verification requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($verifications->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $verifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
