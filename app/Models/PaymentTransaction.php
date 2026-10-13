<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'payment_id',
        'provider_event_id',
        'event_type',
        'payload_digest',
        'processed_at',
        'result',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
