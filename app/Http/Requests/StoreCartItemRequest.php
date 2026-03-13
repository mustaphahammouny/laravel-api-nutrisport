<?php

namespace App\Http\Requests;

use App\Models\ProductSitePrice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $currentSite = current_site();

        $product = $this->route('product');

        return ProductSitePrice::query()
            ->where('product_id', $product->id)
            ->where('site_id', $currentSite->id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'quantity' => [
                'required',
                'integer',
                "between:1,{$product->stock}",
            ],
        ];
    }
}
