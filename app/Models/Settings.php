<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'site_name',
        'shipping_fee',
        'contact_email',
        'maintenance_mode',
    ];

    protected function casts(): array
    {
        return [
            'shipping_fee' => 'decimal:2',
            'maintenance_mode' => 'boolean',
        ];
    }
}
