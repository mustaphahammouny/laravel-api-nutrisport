<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Response\Factories\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(protected ResponseFactory $responseFactory) {}

    public function index(Request $request): Response
    {
        $currentSite = current_site();

        $products = Product::query()
            ->withWhereRelation('sitePrice', 'site_id', $currentSite->id)
            ->latest()
            ->paginate(10);

        $collection = ProductResource::collection($products)
            ->response()
            ->getData(true);

        return $this->responseFactory
            ->make($request)
            ->toResponse($collection);
    }

    public function show(Request $request, Product $product): Response
    {
        $product->load('sitePrice');

        $resource = ProductResource::make($product)
            ->response()
            ->getData(true);

        return $this->responseFactory
            ->make($request)
            ->toResponse($resource);
    }
}
