<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateListingRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Listing;
use App\Models\ListingAttributeValue;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Listing::query()->where('status', 'active');

        if ($request->filled('q')) {
            $q = trim($request->validate(['q' => 'string|max:100'])['q']);
            $query->where('title', 'like', '%' . addcslashes($q, '%_\\') . '%');
        }

        if ($request->filled('category_id')) {
            $catId = (int) $request->input('category_id');
            $childIds = \App\Models\Category::where('parent_id', $catId)->pluck('id')->all();
            $categoryIds = array_merge([$catId], $childIds);
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', (int) $request->input('city_id'));
        }

        $paginator = $query->with(['seller:id,name,username', 'media'])
            ->latest('id')
            ->cursorPaginate(20);

        return $this->collectionResponse(
            $paginator->items(),
            $paginator->nextCursor()?->encode(),
            200,
            ['per_page' => 20]
        );
    }

    public function show(Listing $listing): JsonResponse
    {
        if ($listing->status !== 'active' && auth('sanctum')->id() !== $listing->seller_id) {
            return $this->errorResponse('Listing not found or unavailable', 'NOT_FOUND', [], 404);
        }

        $listing->load(['seller:id,name,username,phone_e164', 'category', 'city', 'attributeValues.attribute', 'media']);
        $listing->increment('views_count');

        return $this->successResponse($listing);
    }

    public function store(CreateListingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $listing = Listing::create([
            ...$validated,
            'seller_id' => $request->user()->id,
            'status' => 'draft',
            'moderation_status' => 'pending',
        ]);

        return $this->successResponse($listing, 'Listing draft created successfully', 201);
    }

    public function myListings(Request $request): JsonResponse
    {
        $listings = Listing::where('seller_id', $request->user()->id)
            ->with(['media'])
            ->latest('id')
            ->get();

        return $this->successResponse($listings);
    }

    public function submit(Request $request, Listing $listing): JsonResponse
    {
        $this->authorizeOwner($request->user(), $listing);

        if (!$listing->transitionTo('pending_review', $request->user(), 'Submitted by seller for review')) {
            return $this->errorResponse('Cannot submit listing in current status', 'INVALID_TRANSITION', [], 422);
        }

        return $this->successResponse($listing, 'Listing submitted for review');
    }

    public function pause(Request $request, Listing $listing): JsonResponse
    {
        $this->authorizeOwner($request->user(), $listing);

        if (!$listing->transitionTo('paused', $request->user(), 'Paused by seller')) {
            return $this->errorResponse('Cannot pause listing in current status', 'INVALID_TRANSITION', [], 422);
        }

        return $this->successResponse($listing, 'Listing has been paused');
    }

    public function resume(Request $request, Listing $listing): JsonResponse
    {
        $this->authorizeOwner($request->user(), $listing);

        if (!$listing->transitionTo('active', $request->user(), 'Resumed by seller')) {
            return $this->errorResponse('Cannot resume listing in current status', 'INVALID_TRANSITION', [], 422);
        }

        return $this->successResponse($listing, 'Listing is now active');
    }

    public function markSold(Request $request, Listing $listing): JsonResponse
    {
        $this->authorizeOwner($request->user(), $listing);

        if (!$listing->transitionTo('sold', $request->user(), 'Marked as sold by seller')) {
            return $this->errorResponse('Cannot mark listing sold in current status', 'INVALID_TRANSITION', [], 422);
        }

        return $this->successResponse($listing, 'Listing marked as sold');
    }

    public function attachMedia(Request $request, Listing $listing): JsonResponse
    {
        $this->authorizeOwner($request->user(), $listing);

        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,webp', 'max:10240'], // 10MB
        ]);

        $file = $request->file('image');
        $path = $file->store('listings/' . $listing->id, 'public');

        $media = Media::create([
            'owner_id' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'bytes' => $file->getSize(),
            'visibility' => 'public',
        ]);

        $currentCount = $listing->media()->count();
        $isCover = $currentCount === 0;

        $listing->media()->attach($media->id, [
            'sort_order' => $currentCount + 1,
            'is_cover' => $isCover,
        ]);

        return $this->successResponse($media, 'Image uploaded and attached to listing', 201);
    }

    public function saveAttributes(Request $request, Listing $listing): JsonResponse
    {
        $this->authorizeOwner($request->user(), $listing);

        $request->validate([
            'attributes' => ['required', 'array'],
            'attributes.*.attribute_id' => ['required', 'integer'],
            'attributes.*.value' => ['nullable'],
        ]);

        foreach ($request->input('attributes') as $item) {
            $attributeId = (int) $item['attribute_id'];
            $val = $item['value'] ?? null;

            ListingAttributeValue::updateOrCreate(
                [
                    'listing_id' => $listing->id,
                    'category_attribute_id' => $attributeId,
                ],
                [
                    'value_text' => is_string($val) ? $val : null,
                    'value_number' => is_numeric($val) ? (float) $val : null,
                    'value_boolean' => is_bool($val) ? $val : null,
                    'value_json' => is_array($val) ? $val : null,
                ]
            );
        }

        return $this->successResponse($listing->fresh('attributeValues.attribute'), 'Attributes saved successfully');
    }

    protected function authorizeOwner($user, Listing $listing): void
    {
        if ($listing->seller_id !== $user->id) {
            abort(403, 'Unauthorized to manage this listing');
        }
    }
}
