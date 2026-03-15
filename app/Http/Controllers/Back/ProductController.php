<?php

namespace App\Http\Controllers\Back;

use App\Actions\CreateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Back\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with('sites')
            ->latest()
            ->paginate(10);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request, CreateProductAction $createProductAction): JsonResponse
    {
        $data = $request->validated();

        $product = $createProductAction->handle($data);

        $product->load('sites');

        return response()->json(ProductResource::make($product), Response::HTTP_CREATED);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $product->load('sites');

        return response()->json(ProductResource::make($product));
    }
}
