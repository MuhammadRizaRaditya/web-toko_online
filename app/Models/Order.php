<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'channel',
        'order_status',
        'currency',
        'subtotal',
        'discount_total',
        'shipping_total',
        'grand_total',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'recipient_city',
        'recipient_province',
        'recipient_postal_code',
        'idempotency_key',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function getLatestStatusAttribute(): string
    {
        return $this->statusHistories()->latest('created_at')->first()?->to_status ?? $this->order_status;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            'pending_payment' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['completed', 'failed_delivery', 'returned'],
            'completed' => ['returned'],
            'cancelled' => [],
            'refunded' => [],
            'failed_delivery' => ['returned', 'cancelled'],
            'returned' => ['refunded'],
        ];

        return in_array($newStatus, $transitions[$this->order_status] ?? []);
    }

    public function transitionTo(string $newStatus, ?int $actorId = null, ?string $note = null): void
    {
        if (! $this->canTransitionTo($newStatus)) {
            throw new \Exception("Invalid status transition from {$this->order_status} to {$newStatus}");
        }

        $oldStatus = $this->order_status;
        $this->update(['order_status' => $newStatus]);

        OrderStatusHistory::create([
            'order_id' => $this->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'note' => $note,
            'actor_user_id' => $actorId,
        ]);
    }
}
