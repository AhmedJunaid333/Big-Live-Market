<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\ModerationAction;
use App\Models\Order;
use App\Models\Report;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPortalController extends Controller
{
    private function checkAdminAccess(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin', 'moderator', 'finance']), 403, 'Unauthorized access to admin portal.');
    }

    /**
     * Main Admin Overview Dashboard
     */
    public function dashboard(Request $request): View
    {
        $this->checkAdminAccess($request);

        $metrics = [
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'pending_review' => Listing::where('status', 'pending_review')->count(),
            'total_users' => User::count(),
            'verified_users' => User::whereHas('verifications', fn($q) => $q->where('status', 'approved'))->count(),
            'open_reports' => Report::where('status', 'open')->count(),
            'total_categories' => Category::whereNull('parent_id')->count(),
            'total_subcategories' => Category::whereNotNull('parent_id')->count(),
            'pending_withdrawals' => DB::table('earning_withdrawals')->whereIn('status', ['requested', 'transferred_pending_confirmation'])->count(),
            'active_campaigns' => DB::table('sponsored_campaigns')->where('status', 'active')->count(),
        ];

        $pendingListings = Listing::with(['seller', 'category', 'city'])
            ->where('status', 'pending_review')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentReports = Report::with(['reporter'])
            ->where('status', 'open')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentWithdrawals = DB::table('earning_withdrawals')
            ->whereIn('status', ['requested', 'transferred_pending_confirmation'])
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'pendingListings', 'recentReports', 'recentWithdrawals'));
    }

    /**
     * Categories & Subcategories Tree Explorer
     */
    public function categories(Request $request): View
    {
        $this->checkAdminAccess($request);

        $parentCategories = Category::with(['children' => function ($q) {
            $q->withCount(['attributes']);
        }])
        ->withCount(['children'])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->get();

        // Selected parent category (defaults to first parent category)
        $selectedId = $request->query('parent_id', $parentCategories->first()?->id);
        $selectedCategory = $parentCategories->firstWhere('id', (int) $selectedId) ?? $parentCategories->first();

        // Subcategories of selected parent with dynamic attributes and listing count
        $subcategories = collect();
        if ($selectedCategory) {
            $subcategories = Category::where('parent_id', $selectedCategory->id)
                ->with(['attributes', 'brands'])
                ->orderBy('sort_order')
                ->get();
        }

        return view('admin.categories.index', compact('parentCategories', 'selectedCategory', 'subcategories'));
    }

    /**
     * Store new category or subcategory
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $categoryType = $request->input('category_type', 'parent'); // 'parent' or 'sub'

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => [$categoryType === 'sub' ? 'required' : 'nullable', 'integer', 'exists:categories,id'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'posting_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        $parentId = ($categoryType === 'parent') ? null : ($validated['parent_id'] ?? null);

        $slug = Str::slug($validated['name']);
        // Ensure slug uniqueness
        $count = Category::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        Category::create([
            'parent_id' => $parentId,
            'slug' => $slug,
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'category',
            'sort_order' => $validated['sort_order'] ?? 0,
            'active' => true,
            'posting_fee_minor' => isset($validated['posting_fee']) ? (int) round($validated['posting_fee'] * 100) : 0,
        ]);

        $typeLabel = $parentId ? 'Sub-category' : 'Top-Level Parent Category';
        return back()->with('success', "{$typeLabel} '{$validated['name']}' created successfully!");
    }

    /**
     * Update an existing category or subcategory
     */
    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: $category->icon,
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
            'active' => $request->has('active'),
        ]);

        return back()->with('success', "Category '{$category->name}' updated successfully!");
    }

    /**
     * Delete category
     */
    public function deleteCategory(Request $request, Category $category): RedirectResponse
    {
        $this->checkAdminAccess($request);

        if ($category->children()->count() > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}' because it contains sub-categories. Please delete or reassign them first.");
        }

        if (Listing::where('category_id', $category->id)->count() > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}' because it has active marketplace ads attached.");
        }

        $categoryName = $category->name;
        $category->delete();

        return back()->with('success', "Category '{$categoryName}' deleted successfully.");
    }

    /**
     * Listings Moderation and Management
     */
    public function listings(Request $request): View
    {
        $this->checkAdminAccess($request);

        $status = $request->query('status', 'all');
        $search = $request->query('q');
        $categoryId = $request->query('category_id');
        $sellerId = $request->query('seller_id');

        $query = Listing::with(['seller', 'category', 'city', 'media']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($sellerId)) {
            $query->where('seller_id', $sellerId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('seller', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $listings = $query->latest('id')->paginate(15)->withQueryString();
        $categories = Category::all(['id', 'name', 'parent_id']);

        $counts = [
            'all' => Listing::count(),
            'pending_review' => Listing::where('status', 'pending_review')->count(),
            'active' => Listing::where('status', 'active')->count(),
            'rejected' => Listing::where('status', 'rejected')->count(),
            'paused' => Listing::where('status', 'paused')->count(),
        ];

        return view('admin.listings.index', compact('listings', 'categories', 'counts', 'status', 'search', 'categoryId'));
    }

    public function approveListing(Request $request, Listing $listing): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $listing->transitionTo('active', $request->user(), 'Approved by moderator via Admin Portal');

        ModerationAction::create([
            'moderator_id' => $request->user()->id,
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'action' => 'approve',
            'reason' => 'Approved via Admin Panel',
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "Listing #{$listing->id} approved and published live!");
    }

    public function rejectListing(Request $request, Listing $listing): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $reason = $request->input('reason', 'Policy violation or low quality ad details.');
        $listing->transitionTo('rejected', $request->user(), $reason);

        ModerationAction::create([
            'moderator_id' => $request->user()->id,
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'action' => 'reject',
            'reason' => $reason,
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "Listing #{$listing->id} has been rejected.");
    }

    /**
     * User Management
     */
    public function users(Request $request): View
    {
        $this->checkAdminAccess($request);

        $search = $request->query('q');
        $role = $request->query('role');
        $status = $request->query('status');

        $query = User::with(['roles', 'verifications'])->withCount('listings');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_e164', 'like', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $query->role($role);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $users = $query->latest('id')->paginate(20)->withQueryString();

        $metrics = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'verified' => User::role('verified_seller')->count(),
            'staff' => User::role(['admin', 'super_admin', 'moderator', 'finance'])->count(),
        ];

        $availableRoles = \Spatie\Permission\Models\Role::where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->unique();

        return view('admin.users.index', compact('users', 'search', 'role', 'status', 'metrics', 'availableRoles'));
    }

    /**
     * Store new user from Admin Panel
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone_e164' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_e164' => $validated['phone_e164'] ?: null,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'status' => 'active',
            'locale' => 'en',
            'preferred_currency' => 'PKR',
        ]);

        try {
            $user->assignRole($validated['role']);
        } catch (\Throwable $e) {
            // fallback
        }

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => 'user_created',
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'metadata_json' => ['created_by' => $request->user()->name, 'role' => $validated['role']],
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "User '{$user->name}' created successfully with role '{$validated['role']}'!");
    }

    /**
     * Update User Role(s)
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $validated = $request->validate([
            'role' => ['required', 'string'],
        ]);

        $oldRoles = $user->roles->pluck('name')->toArray();
        $user->syncRoles([$validated['role']]);

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => 'user_role_changed',
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'metadata_json' => [
                'from' => $oldRoles,
                'to' => $validated['role'],
                'by' => $request->user()->name,
            ],
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "Role for '{$user->name}' updated to '{$validated['role']}'!");
    }

    /**
     * Admin Reset User Password
     */
    public function resetUserPassword(Request $request, User $user): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['new_password']),
        ]);

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => 'user_password_reset',
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'metadata_json' => ['by' => $request->user()->name],
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "Password for user '{$user->name}' reset successfully!");
    }

    public function updateUserStatus(Request $request, User $user): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,suspended'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update(['status' => $validated['status']]);

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => 'user_status_' . $validated['status'],
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'metadata_json' => ['reason' => $validated['reason'] ?? 'Admin status update'],
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "User {$user->name} status changed to {$validated['status']}.");
    }

    /**
     * Delete user
     */
    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        $this->checkAdminAccess($request);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => 'user_deleted',
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'metadata_json' => ['by' => $request->user()->name],
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "User '{$userName}' has been deleted.");
    }

    /**
     * KYC / Identity Verifications Queue
     */
    public function verifications(Request $request): View
    {
        $this->checkAdminAccess($request);

        $verifications = UserVerification::with('user')->latest('id')->paginate(20);
        return view('admin.verifications.index', compact('verifications'));
    }

    public function decideVerification(Request $request, int $id): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $verification = UserVerification::findOrFail($id);
        $decision = $request->input('decision');
        $reason = $request->input('rejection_reason');

        if (!in_array($decision, ['approved', 'rejected'])) {
            return back()->with('error', 'Invalid verification decision.');
        }

        $verification->update([
            'status' => $decision,
            'reviewer_id' => $request->user()->id,
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => $decision === 'rejected' ? $reason : null,
        ]);

        if ($decision === 'approved') {
            $verification->user->assignRole('verified_seller');
        }

        return back()->with('success', "Verification #{$id} marked as {$decision}!");
    }

    /**
     * Safety & Abuse Reports
     */
    public function reports(Request $request): View
    {
        $this->checkAdminAccess($request);

        $status = $request->query('status', 'open');
        $query = Report::with(['reporter', 'assignedModerator']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->latest('id')->paginate(20)->withQueryString();
        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function resolveReport(Request $request, Report $report): RedirectResponse
    {
        $this->checkAdminAccess($request);

        $validated = $request->validate([
            'action' => ['required', 'string', 'in:dismiss,warn_user,remove_listing,suspend_user'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $report->update([
            'status' => 'resolved',
            'assigned_moderator_id' => $request->user()->id,
            'resolved_at' => Carbon::now(),
        ]);

        ModerationAction::create([
            'moderator_id' => $request->user()->id,
            'target_type' => $report->target_type,
            'target_id' => $report->target_id,
            'action' => $validated['action'],
            'reason' => $validated['reason'],
            'created_at' => Carbon::now(),
        ]);

        // If action is remove_listing
        if ($validated['action'] === 'remove_listing' && $report->target_type === 'listing') {
            $listing = Listing::find($report->target_id);
            if ($listing) {
                $listing->transitionTo('rejected', $request->user(), 'Removed due to abuse report: ' . $validated['reason']);
            }
        }

        // If action is suspend_user
        if ($validated['action'] === 'suspend_user' && $report->target_type === 'user') {
            $user = User::find($report->target_id);
            if ($user) {
                $user->update(['status' => 'suspended']);
            }
        }

        return back()->with('success', "Report #{$report->id} resolved with action '{$validated['action']}'.");
    }

    /**
     * Audit Logs Trail
     */
    public function auditLogs(Request $request): View
    {
        $this->checkAdminAccess($request);

        $logs = AuditLog::with('actor')->latest('id')->paginate(30);
        return view('admin.audit_logs.index', compact('logs'));
    }

    /**
     * Monetization, Packages & Invoices
     */
    public function monetization(Request $request): View
    {
        $this->checkAdminAccess($request);

        $orders = Order::with('user')->latest('id')->paginate(20);
        $totalRevenueMinor = Order::where('status', 'paid')->sum('amount_minor');

        return view('admin.monetization.index', compact('orders', 'totalRevenueMinor'));
    }
}
