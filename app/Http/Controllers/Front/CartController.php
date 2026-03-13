<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $cartToken = $request->attributes->get('cart_token');

        $cart = $this->cartService->get($cartToken);

        return response()->json($cart);
    }

    public function update(UpdateCartRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = Arr::get($data, 'items');

        $cartToken = $request->attributes->get('cart_token');

        $cart = $this->cartService->update($cartToken, $items);

        return response()->json($cart);
    }

    public function destroy(Request $request): JsonResponse
    {
        $cartToken = $request->attributes->get('cart_token');

        $this->cartService->clear($cartToken);

        return response()->json();
    }
}
