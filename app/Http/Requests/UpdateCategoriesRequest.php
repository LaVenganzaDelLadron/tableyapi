<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesRouteIds;
use Illuminate\Validation\Rule;

class UpdateCategoriesRequest extends ApiFormRequest
{
    use ResolvesRouteIds;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->routeId('category'))],
        ];
    }
}
