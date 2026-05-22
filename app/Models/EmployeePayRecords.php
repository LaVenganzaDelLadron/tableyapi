<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayRecords extends Model
{
    use HasFactory;

    protected $table = 'employee_pay_records';

    protected $fillable = [
        'employee_id',
        'employee_attendance_id',
        'cacao_batch_id',
        'production_batch_id',
        'pay_type',
        'pay_date',
        'quantity',
        'rate',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'employee_attendance_id' => 'integer',
            'cacao_batch_id' => 'integer',
            'production_batch_id' => 'integer',
            'pay_date' => 'date',
            'quantity' => 'decimal:2',
            'rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function employeeAttendance(): BelongsTo
    {
        return $this->belongsTo(EmployeeAttendances::class, 'employee_attendance_id');
    }

    public function cacaoBatch(): BelongsTo
    {
        return $this->belongsTo(CacaoBatches::class, 'cacao_batch_id');
    }

    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatches::class, 'production_batch_id');
    }
}
