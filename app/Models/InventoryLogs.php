<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLogs extends Model
{
    use HasFactory;

    protected $table = 'inventory_logs';

    protected $fillable = [
        'product_id',
        'order_id',
        'production_batch_id',
        'type',
        'quantity_change',
        'remaining_stock',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'order_id' => 'integer',
            'production_batch_id' => 'integer',
            'quantity_change' => 'integer',
            'remaining_stock' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatches::class, 'production_batch_id');
    }
}
