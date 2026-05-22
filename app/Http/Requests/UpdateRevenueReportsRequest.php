<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesRouteIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRevenueReportsRequest extends FormRequest
{
    use ResolvesRouteIds;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_type' => ['sometimes', 'string', 'max:255'],
            'period_start' => ['sometimes', 'date'],
            'period_end' => [
                'sometimes',
                'date',
                Rule::unique('revenue_reports', 'period_end')
                    ->where(fn ($query) => $query
                        ->where('report_type', $this->input('report_type', 'monthly'))
                        ->where('period_start', $this->input('period_start')))
                    ->ignore($this->routeId('revenue_report')),
            ],
            'gross_revenue' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_expenses' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'net_income' => ['sometimes', 'numeric', 'decimal:0,2'],
        ];
    }
}
