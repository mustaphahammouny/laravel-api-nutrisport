<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $currentSite = current_site();

        $products = Product::query()
            ->withWhereRelation('sitePrice', 'site_id', $currentSite->id)
            ->latest()
            ->paginate(10);

        return ProductResource::collection($products);
    }

    public function show(Request $request, Product $product)
    {
        $product->load('sitePrice');

        return ProductResource::make($product);
    }
}
