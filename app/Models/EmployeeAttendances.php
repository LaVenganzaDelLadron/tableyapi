<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeAttendances extends Model
{
    use HasFactory;

    protected $table = 'employee_attendances';

    protected $fillable = [
        'employee_id',
        'work_date',
        'hours_worked',
        'days_worked',
        'salary_total',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'work_date' => 'date',
            'hours_worked' => 'decimal:2',
            'days_worked' => 'decimal:2',
            'salary_total' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function employeePayRecords(): HasMany
    {
        return $this->hasMany(EmployeePayRecords::class, 'employee_attendance_id');
    }
}
