<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suppliers extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'email',
        'name',
        'phone',
        'address',
    ];

    public function cacaoPurchases(): HasMany
    {
        return $this->hasMany(CacaoPurchases::class, 'supplier_id');
    }
}
