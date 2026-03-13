<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = collect(data_get($this->resource, 'items', []))->values();

        return [
            'cart_id' => data_get($this->resource, 'cart_id'),
            'items' => CartItemResource::collection($items),
            'items_count' => $items->count(),
            'total_quantity' => $this->totalQuantity($items),
            'total_amount' => number_format($this->totalAmount($items), 2, '.', ''),
        ];
    }

    protected function totalQuantity(Collection $items): int
    {
        return $items->sum(fn(array $item) => (int) data_get($item, 'quantity', 0));
    }

    protected function totalAmount(Collection $items): float
    {
        return (float) $items->sum(
            fn(array $item) => (int) data_get($item, 'quantity', 0) * (float) data_get($item, 'unit_price', 0),
        );
    }
}
