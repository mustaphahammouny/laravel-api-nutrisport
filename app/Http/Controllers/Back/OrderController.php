<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        #[CurrentUser('back-api')] protected $currentUser,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $from = now()->subDays(5)->startOfDay();

        $orders = Order::query()
            ->with('site')
            ->where('created_at', '>=', $from)
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $order->load(['site', 'items']);

        return response()->json(OrderResource::make($order));
    }
}
