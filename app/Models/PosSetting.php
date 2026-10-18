<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSetting extends Model
{
    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'cod_area',
        'low_stock_threshold',
        'minimum_order_amount',
        'cod_enabled',
        'transfer_enabled',
        'cod_fee',
    ];

    protected $casts = [
        'low_stock_threshold' => 'integer',
        'minimum_order_amount' => 'decimal:2',
        'cod_enabled' => 'boolean',
        'transfer_enabled' => 'boolean',
        'cod_fee' => 'decimal:2',
    ];
}
