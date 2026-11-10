<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Models\Listing;
use App\Models\SavedSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use ApiResponse;

    public function search(Request $request): JsonResponse
    {
        $query = Listing::query()->where('status', 'active');

        if ($request->filled('q')) {
            $q = trim($request->validate(['q' => 'string|max:100'])['q']);
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', '%' . addcslashes($q, '%_\\') . '%')
                    ->orWhere('description', 'like', '%' . addcslashes($q, '%_\\') . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', (int) $request->input('city_id'));
        }

        if ($request->filled('condition_code')) {
            $query->where('condition_code', $request->input('condition_code'));
        }

        if ($request->filled('price_min')) {
            $query->where('price_minor', '>=', (int) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price_minor', '<=', (int) $request->input('price_max'));
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price_minor', 'asc')->orderBy('id', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_minor', 'desc')->orderBy('id', 'desc');
                break;
            case 'newest':
            default:
                $query->latest('id');
                break;
        }

        $paginator = $query->with(['seller:id,name', 'media'])->cursorPaginate(20);

        return $this->collectionResponse(
            $paginator->items(),
            $paginator->nextCursor()?->encode(),
            200,
            ['per_page' => 20]
        );
    }

    public function suggestions(Request $request): JsonResponse
    {
        $q = trim($request->validate(['q' => 'required|string|min:2|max:60'])['q']);

        $listings = Listing::where('status', 'active')
            ->where('title', 'like', '%' . addcslashes($q, '%_\\') . '%')
            ->limit(5)
            ->pluck('title');

        $categories = Category::where('active', true)
            ->where('name', 'like', '%' . addcslashes($q, '%_\\') . '%')
            ->limit(3)
            ->pluck('name');

        return $this->successResponse([
            'listings' => $listings,
            'categories' => $categories,
        ]);
    }

    public function map(Request $request): JsonResponse
    {
        $query = Listing::where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('city_id')) {
            $query->where('city_id', (int) $request->input('city_id'));
        }

        $items = $query->limit(100)->get()->map(function ($listing) {
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'price_minor' => $listing->price_minor,
                'currency' => $listing->currency,
                // Privacy rounding to ~100m (3 decimal places)
                'latitude' => round($listing->latitude, 3),
                'longitude' => round($listing->longitude, 3),
                'location_label' => $listing->location_label,
            ];
        });

        return $this->successResponse($items);
    }

    public function savedSearches(Request $request): JsonResponse
    {
        $searches = SavedSearch::where('user_id', $request->user()->id)->latest()->get();
        return $this->successResponse($searches);
    }

    public function storeSavedSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'filters' => ['required', 'array'],
            'alerts_enabled' => ['sometimes', 'boolean'],
        ]);

        $saved = SavedSearch::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'filters_json' => $validated['filters'],
            'alerts_enabled' => $validated['alerts_enabled'] ?? true,
        ]);

        return $this->successResponse($saved, 'Search saved successfully', 201);
    }

    public function deleteSavedSearch(Request $request, SavedSearch $savedSearch): JsonResponse
    {
        if ($savedSearch->user_id !== $request->user()->id) {
            abort(403);
        }

        $savedSearch->delete();
        return $this->successResponse(null, 'Saved search deleted');
    }
}
