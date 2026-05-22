<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesRouteIds;
use Illuminate\Validation\Rule;

class UpdateSalesReportsRequest extends ApiFormRequest
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
                Rule::unique('sales_reports', 'period_end')
                    ->where(fn ($query) => $query
                        ->where('report_type', $this->input('report_type', 'daily'))
                        ->where('period_start', $this->input('period_start')))
                    ->ignore($this->routeId('sales_report')),
            ],
            'total_sales' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_orders' => ['sometimes', 'integer', 'min:0'],
            'total_revenue' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
