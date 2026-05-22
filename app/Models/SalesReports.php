<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReports extends Model
{
    use HasFactory;

    protected $table = 'sales_reports';

    protected $fillable = [
        'report_type',
        'period_start',
        'period_end',
        'total_sales',
        'total_orders',
        'total_revenue',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'total_sales' => 'decimal:2',
            'total_orders' => 'integer',
            'total_revenue' => 'decimal:2',
        ];
    }
}
