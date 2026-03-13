<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currentSite = current_site();

        $products = Product::query()
            ->withWhereRelation('sitePrice', 'site_id', $currentSite->id)
            ->latest()
            ->paginate(10);

        return response()->json(ProductResource::collection($products));
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $product->load('sitePrice');

        return response()->json(ProductResource::make($product));
    }
}
