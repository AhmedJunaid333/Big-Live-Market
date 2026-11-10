<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\AuditLog;
use App\Models\Listing;
use App\Models\ModerationAction;
use App\Models\Report;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponse;

    public function dashboard(): JsonResponse
    {
        $metrics = [
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'pending_review_listings' => Listing::where('status', 'pending_review')->count(),
            'total_users' => User::count(),
            'verified_users' => User::whereHas('verifications', fn($q) => $q->where('status', 'approved'))->count(),
            'open_reports' => Report::where('status', 'open')->count(),
        ];

        return $this->successResponse($metrics);
    }

    public function listingsReview(): JsonResponse
    {
        $listings = Listing::where('status', 'pending_review')
            ->with(['seller:id,name,email', 'media', 'attributeValues.attribute'])
            ->latest('id')
            ->cursorPaginate(20);

        return $this->collectionResponse($listings->items(), $listings->nextCursor()?->encode());
    }

    public function approveListing(Request $request, Listing $listing): JsonResponse
    {
        if ($listing->status !== 'pending_review') {
            return $this->errorResponse('Listing is not pending review', 'INVALID_STATE', [], 422);
        }

        $listing->transitionTo('active', $request->user(), 'Approved by moderator');

        ModerationAction::create([
            'moderator_id' => $request->user()->id,
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'action' => 'approve',
            'reason' => 'Approved after review',
            'created_at' => Carbon::now(),
        ]);

        return $this->successResponse($listing, 'Listing approved and activated');
    }

    public function rejectListing(Request $request, Listing $listing): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $listing->transitionTo('rejected', $request->user(), $validated['reason']);

        ModerationAction::create([
            'moderator_id' => $request->user()->id,
            'target_type' => 'listing',
            'target_id' => $listing->id,
            'action' => 'reject',
            'reason' => $validated['reason'],
            'created_at' => Carbon::now(),
        ]);

        return $this->successResponse($listing, 'Listing rejected');
    }

    public function reports(): JsonResponse
    {
        $reports = Report::with(['reporter:id,name', 'assignedModerator:id,name'])
            ->latest('id')
            ->cursorPaginate(20);

        return $this->collectionResponse($reports->items(), $reports->nextCursor()?->encode());
    }

    public function resolveReport(Request $request, Report $report): JsonResponse
    {
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

        return $this->successResponse($report, 'Report resolved');
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::with('roles')->latest('id')->cursorPaginate(20);
        return $this->collectionResponse($users->items(), $users->nextCursor()?->encode());
    }

    public function updateUserStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,suspended,deleted'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $user->update(['status' => $validated['status']]);

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => 'user_status_' . $validated['status'],
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'metadata_json' => ['reason' => $validated['reason']],
            'created_at' => Carbon::now(),
        ]);

        return $this->successResponse($user, 'User status updated');
    }

    public function verifications(): JsonResponse
    {
        $verifications = UserVerification::with('user:id,name,email')
            ->latest('id')
            ->get();

        return $this->successResponse($verifications);
    }

    public function decideVerification(Request $request, int $id): JsonResponse
    {
        $verification = UserVerification::findOrFail($id);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['required_if:decision,rejected', 'nullable', 'string'],
        ]);

        $verification->update([
            'status' => $validated['decision'],
            'reviewer_id' => $request->user()->id,
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        if ($validated['decision'] === 'approved') {
            $verification->user->assignRole('verified_seller');
        }

        return $this->successResponse($verification, 'Verification decision recorded');
    }

    public function auditLogs(): JsonResponse
    {
        $logs = AuditLog::with('actor:id,name')->latest('id')->cursorPaginate(30);
        return $this->collectionResponse($logs->items(), $logs->nextCursor()?->encode());
    }
}
