<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'wholesale_price',
        'minimum_wholesale_quantity',
        'stock',
        'image',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'minimum_wholesale_quantity' => 'integer',
            'stock' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItems::class, 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Reviews::class, 'product_id');
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatches::class, 'product_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLogs::class, 'product_id');
    }
}
