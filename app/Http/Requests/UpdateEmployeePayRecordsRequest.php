<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeePayRecordsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'employee_attendance_id' => ['sometimes', 'nullable', 'integer', 'exists:employee_attendances,id'],
            'cacao_batch_id' => ['sometimes', 'nullable', 'integer', 'exists:cacao_batches,id'],
            'production_batch_id' => ['sometimes', 'nullable', 'integer', 'exists:production_batches,id'],
            'pay_type' => ['sometimes', 'string', 'max:255'],
            'pay_date' => ['sometimes', 'date'],
            'quantity' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'rate' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_amount' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
