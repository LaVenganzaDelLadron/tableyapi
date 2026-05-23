<?php

namespace App\Http\Requests;

class GenerateMonthlyFinancialReportRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'starting_capital' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
