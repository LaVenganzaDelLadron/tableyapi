<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapitalRecords extends Model
{
    use HasFactory;

    protected $table = 'capital_records';

    protected $fillable = [
        'report_type',
        'period_start',
        'period_end',
        'starting_capital',
        'sales_revenue',
        'cacao_costs',
        'employee_costs',
        'operational_expenses',
        'total_revenue',
        'total_expenses',
        'gross_profit',
        'net_profit',
        'remaining_capital',
        'final_profit',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'starting_capital' => 'decimal:2',
            'sales_revenue' => 'decimal:2',
            'cacao_costs' => 'decimal:2',
            'employee_costs' => 'decimal:2',
            'operational_expenses' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'total_expenses' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'remaining_capital' => 'decimal:2',
            'final_profit' => 'decimal:2',
        ];
    }
}
