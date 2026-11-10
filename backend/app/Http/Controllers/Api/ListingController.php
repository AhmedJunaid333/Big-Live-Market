<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateListingRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            $query->where('category_id', (int) $request->input('category_id'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', (int) $request->input('city_id'));
        }

        $paginator = $query->latest('id')->cursorPaginate(20);

        return $this->collectionResponse(
            $paginator->items(),
            $paginator->nextCursor()?->encode(),
            200,
            ['per_page' => 20]
        );
    }

    public function show(Listing $listing): JsonResponse
    {
        if ($listing->status !== 'active') {
            return $this->errorResponse('Listing not found or unavailable', 'NOT_FOUND', [], 404);
        }

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
}
