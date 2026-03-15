<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'payment_method' => $this->payment_method->value,
            'shipping_full_name' => $this->shipping_full_name,
            'shipping_full_address' => $this->shipping_full_address,
            'shipping_city' => $this->shipping_city,
            'shipping_country' => $this->shipping_country,
            'total' => $this->total,
            'paid_amount' => $this->paid_amount,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'site' => SiteResource::make($this->whenLoaded('site')),
            'created_at' => $this->created_at,
        ];
    }
}
