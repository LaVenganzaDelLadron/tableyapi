<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreRevenueReportsRequest extends ApiFormRequest
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
                Rule::unique('revenue_reports', 'period_end')
                    ->where(fn ($query) => $query
                        ->where('report_type', $this->input('report_type', 'monthly'))
                        ->where('period_start', $this->input('period_start'))),
            ],
            'gross_revenue' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_expenses' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'net_income' => ['sometimes', 'numeric', 'decimal:0,2'],
        ];
    }
}
