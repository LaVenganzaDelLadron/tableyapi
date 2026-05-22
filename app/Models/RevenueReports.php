<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueReports extends Model
{
    use HasFactory;

    protected $table = 'revenue_reports';

    protected $fillable = [
        'report_type',
        'period_start',
        'period_end',
        'gross_revenue',
        'total_expenses',
        'net_income',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'gross_revenue' => 'decimal:2',
            'total_expenses' => 'decimal:2',
            'net_income' => 'decimal:2',
        ];
    }
}
