<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Business;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    use ApiResponse;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $business = Business::create([
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $request->user()->assignRole('business_seller');

        // Add owner to members table as role='owner'
        DB::table('business_members')->insert([
            'business_id' => $business->id,
            'user_id' => $request->user()->id,
            'role' => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->successResponse($business, 'Business storefront created successfully', 201);
    }

    public function show(string $slug): JsonResponse
    {
        $business = Business::where('slug', $slug)
            ->where('status', 'active')
            ->with(['owner:id,name,username', 'members:id,name,email'])
            ->firstOrFail();

        $listings = Listing::where('seller_id', $business->owner_id)
            ->where('status', 'active')
            ->with('media')
            ->latest('id')
            ->limit(30)
            ->get();

        return $this->successResponse([
            'storefront' => $business,
            'listings' => $listings,
        ]);
    }

    public function update(Request $request, Business $business): JsonResponse
    {
        $this->authorizeOwnerOrManager($request->user(), $business);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $business->update($validated);

        return $this->successResponse($business->fresh(), 'Business profile updated');
    }

    public function members(Request $request, Business $business): JsonResponse
    {
        $this->authorizeOwnerOrManager($request->user(), $business);

        $members = $business->members()->get();
        return $this->successResponse($members);
    }

    public function addMember(Request $request, Business $business): JsonResponse
    {
        $this->authorizeOwnerOrManager($request->user(), $business);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'in:manager,agent'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        DB::table('business_members')->updateOrInsert(
            [
                'business_id' => $business->id,
                'user_id' => $user->id,
            ],
            [
                'role' => $validated['role'],
                'updated_at' => now(),
            ]
        );

        return $this->successResponse(null, 'Team member added');
    }

    public function removeMember(Request $request, Business $business, int $userId): JsonResponse
    {
        $this->authorizeOwnerOrManager($request->user(), $business);

        if ($business->owner_id === $userId) {
            return $this->errorResponse('Cannot remove the business owner', 'BAD_REQUEST', [], 400);
        }

        DB::table('business_members')
            ->where('business_id', $business->id)
            ->where('user_id', $userId)
            ->delete();

        return $this->successResponse(null, 'Member removed');
    }

    protected function authorizeOwnerOrManager(User $user, Business $business): void
    {
        if ($business->owner_id !== $user->id && !$business->members()->where('user_id', $user->id)->where('role', 'manager')->exists()) {
            abort(403, 'Unauthorized to manage this business');
        }
    }
}
