<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_key',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items()->with('variant')->get()->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });
    }
}
