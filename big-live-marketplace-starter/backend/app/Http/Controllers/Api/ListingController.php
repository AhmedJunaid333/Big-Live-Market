<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class ListingController extends Controller {
    public function index(Request $request) {
        $query = Listing::query()->where('status', 'active');
        if ($request->filled('q')) {
            $q = trim($request->validate(['q'=>'string|max:100'])['q']);
            $query->where('title','like','%'.addcslashes($q, '%_\\').'%');
        }
        return response()->json($query->latest('id')->cursorPaginate(20));
    }
    public function show(Listing $listing) {
        abort_unless($listing->status === 'active', 404);
        return response()->json(['data'=>$listing]);
    }
    public function store(Request $request) {
        $data = $request->validate([
            'title'=>'required|string|min:5|max:180',
            'description'=>'required|string|min:20|max:10000',
            'category_id'=>'required|integer|min:1',
            'city_id'=>'required|integer|min:1',
            'price_minor'=>'nullable|integer|min:0',
            'currency'=>'required|string|size:3',
            'condition_code'=>'required|in:new,used,refurbished',
        ]);
        // Starter only: replace numeric category/city checks with exists rules after taxonomy migrations.
        $listing = Listing::create([...$data, 'seller_id'=>$request->user()->id, 'status'=>'draft']);
        return response()->json(['data'=>$listing], 201);
    }
}
