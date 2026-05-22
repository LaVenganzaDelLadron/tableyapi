<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreEmployeeAttendancesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'work_date' => [
                'required',
                'date',
                Rule::unique('employee_attendances', 'work_date')->where(fn ($query) => $query->where('employee_id', $this->input('employee_id'))),
            ],
            'hours_worked' => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'days_worked' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'salary_total' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
