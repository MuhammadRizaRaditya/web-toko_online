<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'method',
        'status',
        'amount',
        'external_reference',
        'expires_at',
        'confirmed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'confirmed_at' => now(),
        ]);

        $this->order->transitionTo('confirmed');
    }
}
