<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCapitalRecordsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_type' => ['sometimes', 'string', 'max:255'],
            'period_start' => ['required', 'date'],
            'period_end' => [
                'required',
                'date',
                Rule::unique('capital_records', 'period_end')
                    ->where(fn ($query) => $query
                        ->where('report_type', $this->input('report_type', 'monthly'))
                        ->where('period_start', $this->input('period_start'))),
            ],
            'starting_capital' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'total_revenue' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'total_expenses' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'final_profit' => ['required', 'numeric', 'decimal:0,2'],
        ];
    }
}
