<?php

namespace App\Http\Requests;

class FinancialSummaryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'period_start' => ['sometimes', 'date'],
            'period_end' => ['sometimes', 'date'],
            'starting_capital' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $periodStart = strtotime((string) $this->input('period_start'));
                $periodEnd = strtotime((string) $this->input('period_end'));

                if ($this->filled('period_start') && $this->filled('period_end') && $periodStart !== false && $periodEnd !== false && $periodEnd < $periodStart) {
                    $validator->errors()->add('period_end', 'The period end date must be on or after the period start date.');
                }
            },
        ];
    }
}
