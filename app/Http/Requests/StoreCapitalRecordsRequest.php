<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCapitalRecordsRequest extends ApiFormRequest
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
                'after_or_equal:period_start',
                Rule::unique('capital_records', 'period_end')
                    ->where(fn ($query) => $query
                        ->where('report_type', $this->input('report_type', 'monthly'))
                        ->where('period_start', $this->input('period_start'))),
            ],
            'starting_capital' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'sales_revenue' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'cacao_costs' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'employee_costs' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'operational_expenses' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_expenses' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'net_profit' => ['sometimes', 'numeric', 'decimal:0,2'],
            'remaining_capital' => ['sometimes', 'numeric', 'decimal:0,2'],
        ];
    }
}
