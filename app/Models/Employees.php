<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employees extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'position',
        'payment_type',
        'rate',
        'phone',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function employeeAttendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendances::class, 'employee_id');
    }

    public function employeePayRecords(): HasMany
    {
        return $this->hasMany(EmployeePayRecords::class, 'employee_id');
    }
}
