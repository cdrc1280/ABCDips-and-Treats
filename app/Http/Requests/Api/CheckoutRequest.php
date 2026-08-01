<?php

namespace App\Http\Requests\Api;

use App\Rules\PhilippinePhone;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_email'   => ['required', 'email:rfc', 'max:255'],
            'customer_phone'   => ['required', new PhilippinePhone],
            'fulfillment_type' => ['required', 'in:delivery,pickup'],
            'delivery_address' => ['required_if:fulfillment_type,delivery', 'nullable', 'string', 'max:1000'],
            'city'             => ['nullable', 'string', 'max:100'],
            'postal_code'      => ['nullable', 'string', 'max:20'],
            'scheduled_time'   => ['nullable', 'date', 'after_or_equal:now'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'payment_method'   => ['required', 'in:gcash,maya,bank_transfer,cod'],
        ];
    }
}
