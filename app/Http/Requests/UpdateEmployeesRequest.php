<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateEmployeesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'position' => ['sometimes', 'string', 'max:255'],
            'payment_type' => ['sometimes', Rule::in(['daily', 'roasting_per_sack', 'commission_per_pack'])],
            'rate' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
