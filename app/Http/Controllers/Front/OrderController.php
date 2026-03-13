<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function show(Request $request, Order $order): JsonResponse
    {
        $order->load('items');

        return response()->json(OrderResource::make($order));
    }
}
