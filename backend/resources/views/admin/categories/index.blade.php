@extends('layouts.admin')

@section('title', 'Categories & Subcategories')
@section('page_title', 'Taxonomy & Categories Explorer')
@section('page_subtitle', 'Browse main categories, drill-down into sub-categories, schema attributes and related ads')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- TOP CONTROLS & STATS BAR -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Hierarchical Categories Directory</h2>
                <p class="text-xs text-slate-400">Total <strong>{{ $parentCategories->count() }}</strong> Top-Level Parent Categories configured</p>
            </div>
        </div>

        <!-- 2 EXPLICIT BUTTONS: ADD PARENT VS ADD SUB-CATEGORY -->
        <div class="flex items-center gap-2.5">
            <button onclick="openNewParentCategoryModal()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Add Parent Category</span>
            </button>

            <button onclick="openSubcategoryModal({{ $selectedCategory ? $selectedCategory->id : 'null' }})" class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span>+ Add Sub-category</span>
            </button>
        </div>
    </div>

    <!-- 2-COLUMN CATEGORIES EXPLORER -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT LIST: MAIN / PARENT CATEGORIES -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block">Parent Categories ({{ $parentCategories->count() }})</span>
                    <span class="text-[10px] text-slate-400">Click to inspect subcategories</span>
                </div>
                <button onclick="openNewParentCategoryModal()" title="Add Parent Category" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition flex items-center gap-1 text-[11px] font-bold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add Parent</span>
                </button>
            </div>

            <div class="divide-y divide-slate-100 max-h-[700px] overflow-y-auto">
                @foreach($parentCategories as $cat)
                    @php
                        $isSelected = ($selectedCategory && $selectedCategory->id === $cat->id);
                    @endphp
                    <a href="{{ route('admin.categories', ['parent_id' => $cat->id]) }}" 
                       class="p-4 flex items-center justify-between transition group {{ $isSelected ? 'bg-blue-50/60 border-l-4 border-brand-blue text-brand-blue' : 'hover:bg-slate-50 text-slate-700' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $isSelected ? 'bg-brand-blue text-white shadow-sm' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold truncate {{ $isSelected ? 'text-brand-blue' : 'text-slate-800' }}">
                                    {{ $cat->name }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">/{{ $cat->slug }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $isSelected ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600' }}">
                                {{ $cat->children_count }} subs
                            </span>
                            <svg class="w-4 h-4 {{ $isSelected ? 'text-brand-blue' : 'text-slate-300 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- RIGHT DETAIL: SUB-CATEGORIES & DATA DRILL-DOWN -->
        <div class="lg:col-span-8 space-y-6">

            @if($selectedCategory)
                <!-- Selected Category Header Card with Edit & Add Subcategory Options -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-blue to-indigo-600 text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-extrabold text-slate-900">{{ $selectedCategory->name }}</h2>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Parent Category</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $selectedCategory->active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $selectedCategory->active ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                Slug: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-mono">/{{ $selectedCategory->slug }}</code> • Sort Order: <strong>{{ $selectedCategory->sort_order }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="openEditCategoryModal({{ $selectedCategory->id }}, '{{ addslashes($selectedCategory->name) }}', '{{ $selectedCategory->icon }}', {{ $selectedCategory->sort_order }}, {{ $selectedCategory->active ? 'true' : 'false' }})" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            <span>Edit Parent</span>
                        </button>

                        <a href="{{ route('admin.listings', ['category_id' => $selectedCategory->id]) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            <span>View Ads</span>
                        </a>
                    </div>
                </div>

                <!-- SUB-CATEGORIES TABLE -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Sub-categories under "{{ $selectedCategory->name }}"</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $subcategories->count() }} sub-items configured for this section</p>
                        </div>

                        <button onclick="openSubcategoryModal({{ $selectedCategory->id }})" class="px-3.5 py-1.5 rounded-lg bg-brand-blue text-white hover:bg-brand-light text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>+ Add Sub-category</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="p-3.5 font-semibold">Sub-category Name</th>
                                    <th class="p-3.5 font-semibold">Slug</th>
                                    <th class="p-3.5 font-semibold">Dynamic Attributes</th>
                                    <th class="p-3.5 font-semibold">Status</th>
                                    <th class="p-3.5 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($subcategories as $sub)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-800">{{ $sub->name }}</span>
                                                    @if($sub->posting_fee_minor > 0)
                                                        <span class="block text-[10px] text-amber-600 font-semibold">Posting Fee: PKR {{ number_format($sub->posting_fee_minor / 100) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3.5 text-slate-500 font-mono text-[11px]">{{ $sub->slug }}</td>
                                        <td class="p-3.5">
                                            @if($sub->attributes->count() > 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                    {{ $sub->attributes->count() }} attributes
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">—</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sub->active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $sub->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button onclick="openEditCategoryModal({{ $sub->id }}, '{{ addslashes($sub->name) }}', '{{ $sub->icon }}', {{ $sub->sort_order }}, {{ $sub->active ? 'true' : 'false' }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <a href="{{ route('admin.listings', ['category_id' => $sub->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition" title="Filter Ads">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    <span>Ads</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400">
                                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                            No sub-categories yet under {{ $selectedCategory->name }}. Click "+ Add Sub-category" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- UNIFIED MODAL: ADD PARENT OR SUB-CATEGORY -->
<div id="newCategoryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div id="modalIconContainer" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <h3 id="modalTitle" class="font-bold text-slate-800 text-base">Add Parent Category</h3>
            </div>
            <button onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <!-- TYPE SELECTOR: PARENT CATEGORY VS SUB-CATEGORY -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Category Level</label>
                <div class="grid grid-cols-2 gap-3">
                    <label id="typeParentLabel" class="flex items-center gap-2 p-3 rounded-xl border border-emerald-500 bg-emerald-50/50 cursor-pointer transition">
                        <input type="radio" name="category_type" value="parent" id="typeParent" checked onchange="handleTypeChange()" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Parent Category</span>
                            <span class="block text-[10px] text-slate-500">Top-Level (e.g. Motors, Mobiles)</span>
                        </div>
                    </label>

                    <label id="typeSubLabel" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition">
                        <input type="radio" name="category_type" value="sub" id="typeSub" onchange="handleTypeChange()" class="text-brand-blue focus:ring-brand-blue">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Sub-category</span>
                            <span class="block text-[10px] text-slate-500">Child under a parent</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- PARENT DROPDOWN (ONLY SHOWN FOR SUB-CATEGORY) -->
            <div id="parentSelectContainer" class="hidden">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select Parent Category</label>
                <select name="parent_id" id="modalParentId" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue bg-white">
                    @foreach($parentCategories as $cat)
                        <option value="{{ $cat->id }}" {{ ($selectedCategory && $selectedCategory->id === $cat->id) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">This sub-category will appear under the selected parent category.</p>
            </div>

            <!-- NAME -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1" id="nameLabel">Category Name</label>
                <input type="text" name="name" id="categoryNameInput" required placeholder="e.g. Fashion & Beauty, Books, Sports" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
            </div>

            <!-- ICON & SORT ORDER -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Icon Identifier</label>
                    <input type="text" name="icon" placeholder="e.g. checkroom, sports_cricket" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="1" min="0" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                    Save Parent Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT CATEGORY MODAL -->
<div id="editCategoryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-base">Edit Category</h3>
            <button onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form id="editCategoryForm" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category Name</label>
                <input type="text" name="name" id="editName" required class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Icon Identifier</label>
                    <input type="text" name="icon" id="editIcon" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="editSortOrder" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-brand-blue">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="active" id="editActive" value="1" class="rounded border-slate-300 text-brand-blue focus:ring-brand-blue">
                <label for="editActive" class="text-xs font-semibold text-slate-700">Active & Visible in Marketplace</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold shadow-sm transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleTypeChange() {
        const isParent = document.getElementById('typeParent').checked;
        const parentContainer = document.getElementById('parentSelectContainer');
        const submitBtn = document.getElementById('submitBtn');
        const modalTitle = document.getElementById('modalTitle');
        const nameInput = document.getElementById('categoryNameInput');
        const typeParentLabel = document.getElementById('typeParentLabel');
        const typeSubLabel = document.getElementById('typeSubLabel');

        if (isParent) {
            parentContainer.classList.add('hidden');
            modalTitle.innerText = 'Add Parent Category (Top-Level)';
            submitBtn.innerText = 'Save Parent Category';
            submitBtn.className = 'px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition';
            nameInput.placeholder = 'e.g. Fashion & Beauty, Books, Sports, Agriculture';
            
            typeParentLabel.className = 'flex items-center gap-2 p-3 rounded-xl border border-emerald-500 bg-emerald-50/50 cursor-pointer transition';
            typeSubLabel.className = 'flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition';
        } else {
            parentContainer.classList.remove('hidden');
            modalTitle.innerText = 'Add Sub-category';
            submitBtn.innerText = 'Save Sub-category';
            submitBtn.className = 'px-5 py-2 rounded-xl bg-brand-blue hover:bg-brand-light text-white text-xs font-bold shadow-sm transition';
            nameInput.placeholder = 'e.g. Men Clothing, Fiction Books, Cricket Bats';
            
            typeSubLabel.className = 'flex items-center gap-2 p-3 rounded-xl border border-brand-blue bg-blue-50/50 cursor-pointer transition';
            typeParentLabel.className = 'flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition';
        }
    }

    function openNewParentCategoryModal() {
        document.getElementById('typeParent').checked = true;
        handleTypeChange();
        document.getElementById('newCategoryModal').classList.remove('hidden');
        document.getElementById('categoryNameInput').focus();
    }

    function openSubcategoryModal(parentId) {
        document.getElementById('typeSub').checked = true;
        if (parentId) {
            document.getElementById('modalParentId').value = parentId;
        }
        handleTypeChange();
        document.getElementById('newCategoryModal').classList.remove('hidden');
        document.getElementById('categoryNameInput').focus();
    }

    function closeCategoryModal() {
        document.getElementById('newCategoryModal').classList.add('hidden');
    }

    function openEditCategoryModal(id, name, icon, sortOrder, active) {
        document.getElementById('editCategoryForm').action = '/admin/categories/' + id;
        document.getElementById('editName').value = name;
        document.getElementById('editIcon').value = icon || '';
        document.getElementById('editSortOrder').value = sortOrder || 0;
        document.getElementById('editActive').checked = active;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    // Auto-open modal if requested via URL param
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new_parent') {
            openNewParentCategoryModal();
        }
    });
</script>
@endpush
@endsection
