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
            'sales_revenue' => ['prohibited'],
            'cacao_costs' => ['prohibited'],
            'employee_costs' => ['prohibited'],
            'operational_expenses' => ['prohibited'],
            'total_expenses' => ['prohibited'],
            'gross_profit' => ['prohibited'],
            'net_profit' => ['prohibited'],
            'remaining_capital' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.prohibited' => 'Capital financial totals are generated automatically from source records and cannot be submitted manually.',
        ];
    }
}
