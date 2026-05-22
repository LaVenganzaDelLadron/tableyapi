<?php

namespace App\Http\Requests;

class PayrollSummaryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $dateFrom = strtotime((string) $this->input('date_from'));
                $dateTo = strtotime((string) $this->input('date_to'));

                if ($this->filled('date_from') && $this->filled('date_to') && $dateFrom !== false && $dateTo !== false && $dateTo < $dateFrom) {
                    $validator->errors()->add('date_to', 'The end date must be on or after the start date.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'date_from' => 'start date',
            'date_to' => 'end date',
        ]);
    }
}
