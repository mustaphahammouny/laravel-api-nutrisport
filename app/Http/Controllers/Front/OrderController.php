<?php

namespace App\Http\Controllers\Front;

use App\Actions\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        #[CurrentUser('front-api')] protected $currentCustomer,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $currentSite = current_site();

        $orders = Order::query()
            ->where('site_id', $currentSite->id)
            ->where('customer_id', $this->currentCustomer->id)
            ->latest()
            ->paginate(10);

        return response()->json(OrderResource::collection($orders));
    }

    public function store(StoreOrderRequest $request, CreateOrderAction $createOrderAction): JsonResponse
    {
        $data = $request->validated();

        $cartToken = $request->attributes->get('cart_token');

        $order = $createOrderAction->handle($this->currentCustomer, $cartToken, $data);

        $order->load('items');

        return response()->json(OrderResource::make($order), Response::HTTP_CREATED);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $order->load('items');

        return response()->json(OrderResource::make($order));
    }
}
