<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesRouteIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeAttendancesRequest extends FormRequest
{
    use ResolvesRouteIds;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'work_date' => [
                'sometimes',
                'date',
                Rule::unique('employee_attendances', 'work_date')
                    ->where(fn ($query) => $query->where('employee_id', $this->input('employee_id')))
                    ->ignore($this->routeId('employee_attendance')),
            ],
            'hours_worked' => ['sometimes', 'nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'days_worked' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'salary_total' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
