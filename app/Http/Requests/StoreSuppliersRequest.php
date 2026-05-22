<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSuppliersRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
