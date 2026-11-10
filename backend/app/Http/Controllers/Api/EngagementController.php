<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EngagementController extends Controller
{
    use ApiResponse;

    public function favorites(Request $request): JsonResponse
    {
        $favorites = $request->user()->belongsToMany(Listing::class, 'favorites')
            ->where('status', 'active')
            ->with(['seller:id,name', 'media'])
            ->latest('favorites.created_at')
            ->get();

        return $this->successResponse($favorites);
    }

    public function addFavorite(Request $request, int $listingId): JsonResponse
    {
        $listing = Listing::findOrFail($listingId);

        $exists = DB::table('favorites')
            ->where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->exists();

        if (!$exists) {
            DB::table('favorites')->insert([
                'user_id' => $request->user()->id,
                'listing_id' => $listing->id,
                'created_at' => Carbon::now(),
            ]);
            $listing->increment('favorites_count');
        }

        return $this->successResponse(null, 'Added to favorites');
    }

    public function removeFavorite(Request $request, int $listingId): JsonResponse
    {
        $listing = Listing::findOrFail($listingId);

        $deleted = DB::table('favorites')
            ->where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->delete();

        if ($deleted) {
            $listing->decrement('favorites_count');
        }

        return $this->successResponse(null, 'Removed from favorites');
    }

    public function sellerProfile(int $id): JsonResponse
    {
        $seller = User::where('id', $id)
            ->where('status', '!=', 'deleted')
            ->withCount(['followers', 'listings' => fn($q) => $q->where('status', 'active')])
            ->firstOrFail();

        $activeListings = Listing::where('seller_id', $seller->id)
            ->where('status', 'active')
            ->with('media')
            ->latest('id')
            ->limit(20)
            ->get();

        $isFollowed = false;
        if (auth('sanctum')->check()) {
            $isFollowed = DB::table('seller_follows')
                ->where('follower_id', auth('sanctum')->id())
                ->where('seller_id', $seller->id)
                ->exists();
        }

        return $this->successResponse([
            'seller' => [
                'id' => $seller->id,
                'name' => $seller->name,
                'username' => $seller->username,
                'member_since' => $seller->created_at->toIso8601String(),
                'followers_count' => $seller->followers_count,
                'active_listings_count' => $seller->listings_count,
                'is_followed' => $isFollowed,
            ],
            'listings' => $activeListings,
        ]);
    }

    public function followSeller(Request $request, int $sellerId): JsonResponse
    {
        if ($request->user()->id === $sellerId) {
            return $this->errorResponse('You cannot follow yourself', 'BAD_REQUEST', [], 400);
        }

        $seller = User::findOrFail($sellerId);

        DB::table('seller_follows')->updateOrInsert([
            'follower_id' => $request->user()->id,
            'seller_id' => $seller->id,
        ], [
            'created_at' => Carbon::now(),
        ]);

        return $this->successResponse(null, 'Followed seller');
    }

    public function unfollowSeller(Request $request, int $sellerId): JsonResponse
    {
        DB::table('seller_follows')
            ->where('follower_id', $request->user()->id)
            ->where('seller_id', $sellerId)
            ->delete();

        return $this->successResponse(null, 'Unfollowed seller');
    }

    public function recentViews(Request $request): JsonResponse
    {
        $recent = $request->user()->belongsToMany(Listing::class, 'recent_views')
            ->where('status', 'active')
            ->with(['media'])
            ->orderByPivot('last_viewed_at', 'desc')
            ->limit(20)
            ->get();

        return $this->successResponse($recent);
    }
}
