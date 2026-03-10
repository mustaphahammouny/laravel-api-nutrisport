<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        $currentSite = current_site();

        $product->load([
            'sitePrice' => fn(HasOne $query) => $query->where('site_id', $currentSite->id),
        ]);

        return ProductResource::make($product);
    }
}
