<?php

namespace App\Http\Requests;


class UpdateSettingsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['sometimes', 'string', 'max:255'],
            'shipping_fee' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'contact_email' => ['sometimes', 'email', 'max:255'],
            'maintenance_mode' => ['sometimes', 'boolean'],
        ];
    }
}
