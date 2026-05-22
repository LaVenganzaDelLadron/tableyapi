<?php

namespace App\Http\Requests;


class StoreExpensesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'payee' => ['nullable', 'string', 'max:255'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
