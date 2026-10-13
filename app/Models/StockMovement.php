<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'variant_id',
        'movement_type',
        'quantity_delta',
        'reference_type',
        'reference_id',
        'reason',
        'actor_user_id',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function scopeForVariant($query, int $variantId)
    {
        return $query->where('variant_id', $variantId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('movement_type', $type);
    }
}
