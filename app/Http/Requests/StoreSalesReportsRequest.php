<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSalesReportsRequest extends ApiFormRequest
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
                Rule::unique('sales_reports', 'period_end')
                    ->where(fn ($query) => $query
                        ->where('report_type', $this->input('report_type', 'daily'))
                        ->where('period_start', $this->input('period_start'))),
            ],
            'total_sales' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'total_orders' => ['required', 'integer', 'min:0'],
            'total_revenue' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
