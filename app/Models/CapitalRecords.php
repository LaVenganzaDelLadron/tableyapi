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
        'total_revenue',
        'total_expenses',
        'final_profit',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'starting_capital' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'total_expenses' => 'decimal:2',
            'final_profit' => 'decimal:2',
        ];
    }
}
