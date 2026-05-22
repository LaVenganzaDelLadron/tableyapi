<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreEmployeesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'payment_type' => ['sometimes', Rule::in(['daily', 'roasting_per_sack', 'commission_per_pack'])],
            'rate' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
