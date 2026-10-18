<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PosSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        $posSettings = PosSetting::firstOrCreate([]);
        $products = Product::where('status', 'active')
            ->with(['category:id,name', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $sizes = Size::where('is_active', true)->get();
        $colors = Color::where('is_active', true)->get();

        return view('storefront.pos.index', compact('posSettings', 'products', 'sizes', 'colors'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        
        $products = Product::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->whereHas('variants', fn ($q) => $q->where('is_active', true)->whereRaw('stock_on_hand - stock_reserved > 0'))
            ->take(10)
            ->get(['id', 'name', 'slug']);

        return response()->json($products);
    }

    public function searchVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
        ]);

        $variant = $product->variants()
            ->where('size_id', $validated['size_id'])
            ->where('color_id', $validated['color_id'])
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Varian tidak tersedia'], 404);
        }

        $available = $variant->available_stock;

        return response()->json([
            'variant' => $variant,
            'available' => $available,
            'price' => $variant->price,
            'sku' => $variant->sku,
        ]);
    }

    public function recordSale(Request $request)
    {
        $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'exists:product_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'required|string',
            'payment_method' => 'required|in:cod,transfer',
            'notes' => 'nullable|string',
        ]);

        $posSetting = PosSetting::firstOrCreate([]);

        return DB::transaction(function () use ($request, $posSetting) {
            $order = new Order([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'channel' => 'pos',
                'order_status' => 'pending_payment',
                'recipient_name' => $request->customer_name,
                'recipient_phone' => $request->customer_phone,
                'recipient_address' => $request->customer_address,
                'currency' => 'IDR',
                'subtotal' => 0,
                'discount_total' => 0,
                'shipping_total' => 0,
                'grand_total' => 0,
            ]);

            $order->save();

            $grandTotal = 0;

            foreach ($request->variant_ids as $index => $variantId) {
                $variant = ProductVariant::with(['product', 'size', 'color'])->findOrFail($variantId);
                $quantity = (int) ($request->quantities[$index] ?? 0);

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'variants' => "Jumlah tidak valid untuk varian {$variant->sku}",
                    ]);
                }

                $available = $variant->available_stock;
                if ($available < $quantity) {
                    throw ValidationException::withMessages([
                        'variants' => "Stok tidak mencukupi untuk varian {$variant->sku}. Tersedia: {$available}",
                    ]);
                }

                $success = $variant->reserve($quantity, auth()->id());
                if (! $success) {
                    throw ValidationException::withMessages([
                        'variants' => "Gagal mereservasi stok untuk varian {$variant->sku}",
                    ]);
                }

                $lineTotal = (float) $variant->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variant->id,
                    'product_name_snapshot' => $variant->product->name,
                    'sku_snapshot' => $variant->sku,
                    'size_snapshot' => $variant->size?->name,
                    'color_snapshot' => $variant->color?->name,
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'discount_amount' => 0,
                    'line_total' => $lineTotal,
                ]);

                $grandTotal += $lineTotal;
            }

            $codFee = $request->payment_method === 'cod'
                ? (float) ($posSetting?->cod_fee ?? 0)
                : 0.0;

            $order->update([
                'subtotal' => $grandTotal,
                'discount_total' => 0,
                'shipping_total' => $codFee,
                'grand_total' => $grandTotal + $codFee,
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => 'manual',
                'method' => $request->payment_method,
                'status' => 'pending',
                'amount' => $order->grand_total,
            ]);

            if ($request->payment_method === 'cod') {
                $order->transitionTo('confirmed', auth()->id(), 'Pembayaran COD');
                $payment->update([
                    'status' => 'paid',
                    'confirmed_at' => now(),
                ]);
            }

            return response()->json([
                'order' => $order->fresh('items'),
                'payment' => $payment,
                'success' => true,
            ]);
        });
    }

    public function getVariantDetail(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
        ]);

        $variant = $product->variants()
            ->where('size_id', $validated['size_id'])
            ->where('color_id', $validated['color_id'])
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Varian tidak ditemukan'], 404);
        }

        return response()->json([
            'variant' => $variant,
            'available' => $variant->available_stock,
            'price' => $variant->price,
            'sku' => $variant->sku,
        ]);
    }
}