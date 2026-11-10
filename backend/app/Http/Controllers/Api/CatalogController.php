<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Country;
use App\Models\ProductModel;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    use ApiResponse;

    public function countries(): JsonResponse
    {
        $countries = Country::where('enabled', true)->get();
        return $this->successResponse($countries);
    }

    public function regions(Request $request): JsonResponse
    {
        $query = Region::query();
        if ($request->filled('country_id')) {
            $query->where('country_id', (int) $request->input('country_id'));
        }
        return $this->successResponse($query->get());
    }

    public function cities(Request $request): JsonResponse
    {
        $query = City::query();
        if ($request->filled('region_id')) {
            $query->where('region_id', (int) $request->input('region_id'));
        }
        return $this->successResponse($query->get());
    }

    public function categories(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->where('active', true)
            ->with(['children' => fn($q) => $q->where('active', true)])
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse($categories);
    }

    public function categoryDetails(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->with(['children', 'attributes', 'brands'])
            ->first();

        if (!$category) {
            return $this->errorResponse('Category not found', 'NOT_FOUND', [], 404);
        }

        return $this->successResponse($category);
    }

    public function categoryAttributes(int $id): JsonResponse
    {
        $attributes = CategoryAttribute::where('category_id', $id)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse($attributes);
    }

    public function brands(Request $request): JsonResponse
    {
        $query = Brand::query();
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }
        return $this->successResponse($query->get());
    }

    public function models(Request $request): JsonResponse
    {
        $request->validate(['brand_id' => 'required|integer']);
        $models = ProductModel::where('brand_id', (int) $request->input('brand_id'))->get();
        return $this->successResponse($models);
    }
}
