<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'subtotal',
        'shipping_fee',
        'total_price',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'shipping_address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Reviews::class, 'order_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLogs::class, 'order_id');
    }
}
