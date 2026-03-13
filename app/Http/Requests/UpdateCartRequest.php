<?php

namespace App\Http\Requests;

use App\Models\ProductSitePrice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                Rule::exists(ProductSitePrice::class, 'product_id')
                    ->where('site_id', $currentSite->id)
            ],
            'items.*.quantity' => ['required', 'integer'],
        ];
    }
}
