<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionBatches extends Model
{
    use HasFactory;

    protected $table = 'production_batches';

    protected $fillable = [
        'cacao_batch_id',
        'product_id',
        'packs_produced',
        'price_per_pack',
        'total_production_value',
        'production_date',
    ];

    protected function casts(): array
    {
        return [
            'cacao_batch_id' => 'integer',
            'product_id' => 'integer',
            'packs_produced' => 'integer',
            'price_per_pack' => 'decimal:2',
            'total_production_value' => 'decimal:2',
            'production_date' => 'date',
        ];
    }

    public function cacaoBatch(): BelongsTo
    {
        return $this->belongsTo(CacaoBatches::class, 'cacao_batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLogs::class, 'production_batch_id');
    }

    public function employeePayRecords(): HasMany
    {
        return $this->hasMany(EmployeePayRecords::class, 'production_batch_id');
    }
}
