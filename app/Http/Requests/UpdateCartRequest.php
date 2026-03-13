<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductSitePrice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentSite = current_site();

        return [
            'items' => ['required', 'array'],
            'items.*.product_id' => [
                'required',
                'integer',
                Rule::exists(ProductSitePrice::class, 'product_id')
                    ->where('site_id', $currentSite->id),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $items = collect($this->input('items'));

            $productIds = $items
                ->pluck('product_id')
                ->unique()
                ->values();

            $stocks = Product::query()
                ->whereIn('id', $productIds)
                ->pluck('stock', 'id');

            foreach ($items as $index => $item) {
                if ($validator->errors()->has("items.$index.product_id")) {
                    continue;
                }

                $productId = Arr::get($item, 'product_id');
                $quantity = Arr::get($item, 'quantity');

                $stock = $stocks->get($productId);

                if ($quantity > $stock) {
                    $validator->errors()->add(
                        "items.$index.quantity",
                        "The items.$index.quantity field must be between 1 and $stock."
                    );
                }
            }
        });
    }
}
