<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesRouteIds;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiFormRequest
{
    use ResolvesRouteIds;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->routeId('user'))],
            'email_verified_at' => ['sometimes', 'nullable', 'date'],
            'password' => ['sometimes', 'string', 'min:8', 'max:255'],
            'role' => ['sometimes', Rule::in(['admin', 'customer', 'reseller'])],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
