<?php

namespace App\Http\Requests\Back;

use App\Models\Site;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.site_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists(Site::class, 'id'),
            ],
            'prices.*.price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }
}
