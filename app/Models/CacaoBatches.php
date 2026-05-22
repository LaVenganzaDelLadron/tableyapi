<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CacaoBatches extends Model
{
    use HasFactory;

    protected $table = 'cacao_batches';

    protected $fillable = [
        'cacao_purchase_id',
        'raw_kilogram',
        'roasted_kilogram',
        'sack_count',
        'roasting_payment_per_sack',
        'total_roasting_payment',
        'production_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'cacao_purchase_id' => 'integer',
            'raw_kilogram' => 'decimal:2',
            'roasted_kilogram' => 'decimal:2',
            'sack_count' => 'integer',
            'roasting_payment_per_sack' => 'decimal:2',
            'total_roasting_payment' => 'decimal:2',
            'production_date' => 'date',
        ];
    }

    public function cacaoPurchase(): BelongsTo
    {
        return $this->belongsTo(CacaoPurchases::class, 'cacao_purchase_id');
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatches::class, 'cacao_batch_id');
    }

    public function employeePayRecords(): HasMany
    {
        return $this->hasMany(EmployeePayRecords::class, 'cacao_batch_id');
    }
}
