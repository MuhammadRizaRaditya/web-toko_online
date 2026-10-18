<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'sku',
        'barcode',
        'price',
        'compare_at_price',
        'stock_on_hand',
        'stock_reserved',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_on_hand' => 'integer',
        'stock_reserved' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->stock_on_hand - $this->stock_reserved;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->available_stock <= $this->low_stock_threshold;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        $compare = $this->compare_at_price !== null ? (float) $this->compare_at_price : null;
        $price = (float) $this->price;

        if ($compare === null || $compare <= 0 || $price >= $compare) {
            return null;
        }

        return (int) round((($compare - $price) / $compare) * 100);
    }

    public function reserve(int $quantity, ?int $actorId = null, ?string $referenceType = null, ?int $referenceId = null): bool
    {
        $success = DB::table('product_variants')
            ->where('id', $this->id)
            ->whereRaw('stock_reserved + ? <= stock_on_hand', [$quantity])
            ->increment('stock_reserved', $quantity);

        if ($success) {
            $this->refresh();
            StockMovement::create([
                'variant_id' => $this->id,
                'movement_type' => 'reservation',
                'quantity_delta' => $quantity,
                'reason' => 'Stock reserved for order',
                'actor_user_id' => $actorId,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        }

        return $success;
    }

    public function release(int $quantity, ?int $actorId = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $this->decrement('stock_reserved', $quantity);

        StockMovement::create([
            'variant_id' => $this->id,
            'movement_type' => 'release',
            'quantity_delta' => -$quantity,
            'reason' => 'Stock reservation released',
            'actor_user_id' => auth()->id(),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function commit(int $quantity, ?int $actorId = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $this->decrement('stock_on_hand', $quantity);
        $this->decrement('stock_reserved', $quantity);

        StockMovement::create([
            'variant_id' => $this->id,
            'movement_type' => 'commit',
            'quantity_delta' => -$quantity,
            'reason' => 'Stock committed after payment',
            'actor_user_id' => auth()->id() ?? $actorId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function adjustStock(int $delta, string $reason, ?int $actorId = null, string $referenceType = 'adjustment', ?int $referenceId = null): void
    {
        $movementType = $delta > 0 ? 'adjustment_in' : 'adjustment_out';

        DB::transaction(function () use ($delta, $movementType, $reason, $actorId, $referenceType, $referenceId) {
            $this->increment('stock_on_hand', $delta);

            StockMovement::create([
                'variant_id' => $this->id,
                'movement_type' => $movementType,
                'quantity_delta' => $delta,
                'reason' => $reason,
                'actor_user_id' => $actorId,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
