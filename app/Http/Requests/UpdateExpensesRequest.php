<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpensesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_method' => ['sometimes', 'nullable', 'string', 'max:255'],
            'payee' => ['sometimes', 'nullable', 'string', 'max:255'],
            'expense_date' => ['sometimes', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
