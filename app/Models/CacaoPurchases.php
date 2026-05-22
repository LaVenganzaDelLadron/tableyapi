<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CacaoPurchases extends Model
{
    use HasFactory;

    protected $table = 'cacao_purchases';

    protected $fillable = [
        'supplier_id',
        'kilogram',
        'price_per_kilogram',
        'total_amount',
        'payment_status',
        'paid_at',
        'purchase_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'kilogram' => 'decimal:2',
            'price_per_kilogram' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'purchase_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function cacaoBatches(): HasMany
    {
        return $this->hasMany(CacaoBatches::class, 'cacao_purchase_id');
    }
}
