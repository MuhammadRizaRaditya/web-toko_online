<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PosSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $posSettings = PosSetting::first() or PosSetting::create();
        $products = Product::where('status', 'active')
            ->with(['variants' => fn ($q) => $q->where('is_active', true)])
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
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->whereHas('variants', fn ($q) => $q->where('is_active', true)->whereRaw('stock_on_hand - stock_reserved > 0'))
            ->take(10)
            ->get(['id', 'name', 'slug', 'id']);

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

        $posSetting = PosSetting::first();

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
                'shipping_total' => $posSetting?->cod_fee ?? 0,
                'grand_total' => 0,
            ]);

            $order->save();

            $grandTotal = 0;

            foreach ($request->variant_ids as $index => $variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                $quantity = $request->quantities[$index];

                // Check stock availability
                $available = $variant->available_stock;
                if ($available < $quantity) {
                    throw new \Exception("Stok tidak mencukup untuk varian {$variant->sku}. Tersedia: {$available}");
                }

                // Reserve stock
                $success = $variant->reserve($quantity, auth()->id());
                if (!$success) {
                    // Rollback all reservations
                    foreach ($request->variant_ids as $vid => $vid2) {
                        $v = ProductVariant::find($vid);
                        $v->release($request->quantities[$vid] ?? 1);
                    }
                    throw new \Exception("Gagal mereservasi stok");
                }

                // Create order item
                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $variant->price,
                ]);
                $orderItem->save();

                // Calculate subtotal
                $grandTotal += ($variant->price * $quantity);
            }

            // Set order totals
            $order->subtotal = $grandTotal;
            $order->discount_total = 0;
            $order->shipping_total = $posSetting?->cod_fee ?? 0;
            $order->grand_total = $grandTotal + ($posSetting?->cod_fee ?? 0);
            $order->save();

            // Create payment record
            $payment = new Payment([
                'order_id' => $order->id,
                'provider' => 'manual',
                'method' => $request->payment_method,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'amount' => $order->grand_total,
            ]);
            $payment->save();

            // Update order payment relationship
            $order->payment()->save($payment);

            // If COD, mark as confirmed immediately (for MVP)
            if ($request->payment_method === 'cod') {
                $order->transitionTo('confirmed', auth()->id(), 'Pembayaran COD');
                $payment->markAsPaid();
            }

            return response()->json([
                'order' => $order,
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