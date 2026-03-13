<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $quantity = (int) data_get($this->resource, 'quantity', 0);
        $unitPrice = (float) data_get($this->resource, 'unit_price', 0);

        return [
            'product_id' => (int) data_get($this->resource, 'product_id'),
            'product_name' => (string) data_get($this->resource, 'product_name'),
            'quantity' => $quantity,
            'unit_price' => number_format($unitPrice, 2, '.', ''),
            'line_total' => number_format($quantity * $unitPrice, 2, '.', ''),
        ];
    }
}
