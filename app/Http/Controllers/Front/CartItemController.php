<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class CartItemController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    public function store(StoreCartItemRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        $quantity = Arr::get($data, 'quantity', 1);

        $cartToken = $request->attributes->get('cart_token');

        $cart = $this->cartService->add($cartToken, $product, $quantity);

        return response()->json($cart, Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $cartToken = $request->attributes->get('cart_token');

        $cart = $this->cartService->remove($cartToken, $product->id);

        return response()->json([]);
    }
}
