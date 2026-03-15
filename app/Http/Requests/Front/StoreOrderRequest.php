<?php

namespace App\Http\Requests\Front;

use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'shipping_full_name' => ['required', 'string', 'max:255'],
            'shipping_full_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_country' => ['required', 'string', 'max:100'],
        ];
    }
}
