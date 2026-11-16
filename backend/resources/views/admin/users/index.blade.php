@extends('layouts.admin')

@section('title', 'User Directory')
@section('page_title', 'User Accounts & Roles Management')
@section('page_subtitle', 'Comprehensive directory of buyers, sellers, verified merchants, and platform staff')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Members</p>
            <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ number_format($metrics['total']) }}</h3>
            <span class="text-[10px] text-slate-400">All registered users</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Active Users</p>
            <h3 class="text-xl font-extrabold text-emerald-600 mt-0.5">{{ number_format($metrics['active']) }}</h3>
            <span class="text-[10px] text-slate-400">Normal account standing</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Suspended</p>
            <h3 class="text-xl font-extrabold text-rose-600 mt-0.5">{{ number_format($metrics['suspended']) }}</h3>
            <span class="text-[10px] text-slate-400">Blocked / under review</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-[10px] font-bold text-cyan-600 uppercase tracking-wider">Verified Sellers</p>
            <h3 class="text-xl font-extrabold text-cyan-600 mt-0.5">{{ number_format($metrics['verified']) }}</h3>
            <span class="text-[10px] text-slate-400">KYC trust badges</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-[10px] font-bold text-brand-blue uppercase tracking-wider">Staff / Admins</p>
            <h3 class="text-xl font-extrabold text-brand-blue mt-0.5">{{ number_format($metrics['staff']) }}</h3>
            <span class="text-[10px] text-slate-400">Staff role access</span>
        </div>
    </div>

    <!-- SEARCH, FILTERS & ADD USER BAR -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone..." class="text-xs rounded-xl border border-slate-300 pl-8 pr-3 py-2.5 bg-white w-64 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <select name="role" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 p-2.5 bg-white text-slate-700 font-medium">
                <option value="">All Roles</option>
                @foreach($availableRoles as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $r)) }}
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 p-2.5 bg-white text-slate-700 font-medium">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
            </select>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-xs transition">
                Filter
            </button>
            @if(request()->hasAny(['q', 'role', 'status']))
                <a href="{{ route('admin.users') }}" class="text-xs text-rose-600 hover:underline font-semibold">Clear</a>
            @endif
        </form>

        <!-- ADD NEW USER BUTTON -->
        <button onclick="document.getElementById('newUserModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>+ Create New User</span>
        </button>
    </div>

    <!-- USERS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-semibold">User Profile</th>
                        <th class="p-3.5 font-semibold">Contact Details</th>
                        <th class="p-3.5 font-semibold">Roles & Trust</th>
                        <th class="p-3.5 font-semibold">Ads Posted</th>
                        <th class="p-3.5 font-semibold">Status</th>
                        <th class="p-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        @php
                            $isVerifiedSeller = $user->hasRole('verified_seller');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- User Profile -->
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-700 to-navy-900 text-white font-bold flex items-center justify-center border border-slate-200 shadow-xs flex-shrink-0 text-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="font-bold text-slate-900 text-xs truncate">{{ $user->name }}</p>
                                            @if($isVerifiedSeller)
                                                <span title="Verified Merchant Badge" class="text-cyan-500">
                                                    <svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-0.5">ID: #{{ $user->id }} • Joined {{ $user->created_at?->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="p-3.5">
                                <span class="text-slate-800 font-semibold block text-xs">{{ $user->email }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $user->phone_e164 ?? '—' }}</span>
                            </td>

                            <!-- Roles & Trust -->
                            <td class="p-3.5">
                                <div class="flex flex-wrap gap-1 items-center">
                                    @foreach($user->roles as $role)
                                        @php
                                            $isStaff = in_array($role->name, ['super_admin', 'admin', 'moderator', 'finance']);
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $isStaff ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-brand-blue border border-blue-200' }} capitalize">
                                            {{ str_replace('_', ' ', $role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Ads Count -->
                            <td class="p-3.5">
                                @if($user->listings_count > 0)
                                    <a href="{{ route('admin.listings', ['seller_id' => $user->id]) }}" class="inline-flex items-center gap-1 font-bold text-brand-blue hover:underline text-xs">
                                        <span>{{ $user->listings_count }} ads</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">0 ads</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="p-3.5">
                                @if($user->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 capitalize">{{ $user->status }}</span>
                                @endif
                            </td>

                            <!-- MULTI-ACTION TOOLBAR -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    
                                    <!-- 1. View User Details Modal Button -->
                                    <button onclick="openUserDetailsModal({{ json_encode($user) }}, {{ $user->listings_count }}, {{ json_encode($user->roles->pluck('name')) }})" 
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" 
                                            title="View Account Profile">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <!-- 2. Change Role Button -->
                                    <button onclick="openChangeRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->roles->first()?->name ?? 'buyer' }}')" 
                                            class="p-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 transition" 
                                            title="Change Role & Permissions">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </button>

                                    <!-- 3. Reset Password Button -->
                                    <button onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')" 
                                            class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition" 
                                            title="Reset Password">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    </button>

                                    <!-- 4. Direct View Ads Shortcut -->
                                    <a href="{{ route('admin.listings', ['seller_id' => $user->id]) }}" 
                                       class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-brand-blue transition" 
                                       title="View All Ads by this User">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                    </a>

                                    <!-- 5. Suspend / Activate Toggle -->
                                    <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" 
                                                    onclick="return confirm('Suspend user {{ addslashes($user->name) }}?')" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition text-[11px]" 
                                                    title="Suspend User">
                                                Suspend
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" 
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition text-[11px]" 
                                                    title="Activate User">
                                                Activate
                                            </button>
                                        @endif
                                    </form>

                                    <!-- 6. Delete User Button -->
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete account for {{ addslashes($user->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-100 text-slate-400 hover:text-rose-600 transition" title="Delete User">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                No users found matching this search or filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL 1: CREATE NEW USER -->
<div id="newUserModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-base">Create New User Account</h3>
            <button onclick="document.getElementById('newUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name</label>
                <input type="text" name="name" required placeholder="e.g. Tariq Mehmood" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="user@example.com" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone Number</label>
                    <input type="text" name="phone_e164" placeholder="+923001234567" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Assigned Role</label>
                    <select name="role" required class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue bg-white">
                        @foreach($availableRoles as $r)
                            <option value="{{ $r }}" {{ $r === 'seller' ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $r)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Initial Password</label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimum 8 characters" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('newUserModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold shadow-sm">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: CHANGE ROLE -->
<div id="changeRoleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-base">Assign Role & Permissions</h3>
            <button onclick="document.getElementById('changeRoleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form id="changeRoleForm" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PATCH')

            <p class="text-xs text-slate-500">Updating system role for user: <strong id="roleUserName" class="text-slate-800"></strong></p>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select New Role</label>
                <select name="role" id="roleSelect" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue bg-white font-medium">
                    @foreach($availableRoles as $r)
                        <option value="{{ $r }}">
                            {{ ucwords(str_replace('_', ' ', $r)) }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Assigning staff roles (admin/moderator/finance) grants management portal privileges.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('changeRoleModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: RESET PASSWORD -->
<div id="resetPasswordModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-base">Reset User Password</h3>
            <button onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form id="resetPasswordForm" method="POST" class="mt-4 space-y-4">
            @csrf
            <p class="text-xs text-slate-500">Setting a new password for: <strong id="resetUserName" class="text-slate-800"></strong></p>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">New Password</label>
                <input type="password" name="new_password" required minlength="8" placeholder="Minimum 8 characters" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: FULL USER DETAILS DRAWER -->
<div id="userDetailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-base">Member Profile Information</h3>
            <button onclick="document.getElementById('userDetailsModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <div class="mt-4 space-y-3 text-xs" id="userDetailsContent">
            <!-- Dynamically populated -->
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
            <button type="button" onclick="document.getElementById('userDetailsModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openChangeRoleModal(userId, userName, currentRole) {
        document.getElementById('changeRoleForm').action = '/admin/users/' + userId + '/role';
        document.getElementById('roleUserName').innerText = userName;
        document.getElementById('roleSelect').value = currentRole;
        document.getElementById('changeRoleModal').classList.remove('hidden');
    }

    function openResetPasswordModal(userId, userName) {
        document.getElementById('resetPasswordForm').action = '/admin/users/' + userId + '/reset-password';
        document.getElementById('resetUserName').innerText = userName;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function openUserDetailsModal(user, adsCount, roles) {
        const container = document.getElementById('userDetailsContent');
        container.innerHTML = `
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <div class="w-12 h-12 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-sm">
                    ${user.name.charAt(0)}
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">${user.name}</h4>
                    <p class="text-slate-400 text-[11px]">UUID: <code class="font-mono text-slate-600">${user.uuid || 'N/A'}</code></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2">
                <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50/50">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Email</span>
                    <span class="font-semibold text-slate-800">${user.email}</span>
                </div>
                <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50/50">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Phone (E.164)</span>
                    <span class="font-semibold text-slate-800">${user.phone_e164 || 'None'}</span>
                </div>
                <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50/50">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Preferred Currency</span>
                    <span class="font-semibold text-slate-800">${user.preferred_currency || 'PKR'}</span>
                </div>
                <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50/50">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Status</span>
                    <span class="font-bold capitalize ${user.status === 'active' ? 'text-emerald-600' : 'text-rose-600'}">${user.status}</span>
                </div>
            </div>
            <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/50">
                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Active Roles</span>
                <div class="flex flex-wrap gap-1">
                    ${roles.map(r => `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 capitalize">${r.replace('_', ' ')}</span>`).join('')}
                </div>
            </div>
            <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Classified Ads Posted</span>
                    <span class="font-extrabold text-sm text-slate-900">${adsCount} Listings</span>
                </div>
                ${adsCount > 0 ? `<a href="/admin/listings?seller_id=${user.id}" class="px-3 py-1.5 rounded-lg bg-brand-blue text-white font-bold text-[11px] hover:bg-brand-light">Audit Ads →</a>` : ''}
            </div>
        `;
        document.getElementById('userDetailsModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
