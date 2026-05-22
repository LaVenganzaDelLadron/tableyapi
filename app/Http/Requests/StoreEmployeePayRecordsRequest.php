<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeePayRecordsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'employee_attendance_id' => ['nullable', 'integer', 'exists:employee_attendances,id'],
            'cacao_batch_id' => ['nullable', 'integer', 'exists:cacao_batches,id'],
            'production_batch_id' => ['nullable', 'integer', 'exists:production_batches,id'],
            'pay_type' => ['required', 'string', 'max:255'],
            'pay_date' => ['required', 'date'],
            'quantity' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'rate' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_amount' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
