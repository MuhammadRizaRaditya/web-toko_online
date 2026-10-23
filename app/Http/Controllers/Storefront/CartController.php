<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PosSetting;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = $this->currentCart();
        $items = $cart?->items()
            ->with(['variant.product.images', 'variant.size', 'variant.color'])
            ->get() ?? collect();

        return view('storefront.cart.index', compact('cart', 'items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);
        abort_unless($variant->is_active && $variant->product?->status === 'active', 404);
        $cart = $this->getOrCreateCart();

        DB::transaction(function () use ($cart, $variant, $data) {
            $lockedVariant = ProductVariant::query()->lockForUpdate()->findOrFail($variant->id);
            $item = $cart->items()->where('variant_id', $lockedVariant->id)->lockForUpdate()->first();
            $quantity = ($item?->quantity ?? 0) + $data['quantity'];

            if ($quantity > 100) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah maksimal untuk satu varian adalah 100.',
                ]);
            }

            if ($quantity > $lockedVariant->available_stock) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tersedia untuk varian ini hanya {$lockedVariant->available_stock}.",
                ]);
            }

            if ($item) {
                $item->update(['quantity' => $quantity]);
            } else {
                $cart->items()->create([
                    'variant_id' => $lockedVariant->id,
                    'quantity' => $quantity,
                ]);
            }
        });

        return redirect()->route('cart.index')->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $cart = $this->currentCart();
        abort_unless($cart && $cartItem->cart_id === $cart->id, 404);

        DB::transaction(function () use ($cartItem, $data) {
            $item = CartItem::query()->lockForUpdate()->findOrFail($cartItem->id);
            $variant = ProductVariant::query()->lockForUpdate()->findOrFail($item->variant_id);

            if (! $variant->is_active || $data['quantity'] > $variant->available_stock) {
                throw ValidationException::withMessages([
                    'quantity' => "Jumlah melebihi stok yang tersedia ({$variant->available_stock}).",
                ]);
            }

            $item->update(['quantity' => $data['quantity']]);
        });

        return redirect()->route('cart.index')->with('status', 'Keranjang berhasil diperbarui.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $cart = $this->currentCart();
        abort_unless($cart && $cartItem->cart_id === $cart->id, 404);

        $cartItem->delete();

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = $this->currentCart();
        if (! $cart || $cart->items()->doesntExist()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang Anda masih kosong.']);
        }

        $items = $cart->items()
            ->with(['variant.product', 'variant.size', 'variant.color'])
            ->get();
        $settings = PosSetting::firstOrCreate([], [
            'cod_enabled' => true,
            'transfer_enabled' => true,
        ]);

        return view('storefront.cart.checkout', compact('cart', 'items', 'settings'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $settings = PosSetting::firstOrCreate([], [
            'cod_enabled' => true,
            'transfer_enabled' => true,
        ]);
        $enabledMethods = array_values(array_filter([
            $settings->cod_enabled ? 'cod' : null,
            $settings->transfer_enabled ? 'transfer' : null,
        ]));

        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:50'],
            'recipient_address' => ['required', 'string', 'max:2000'],
            'recipient_city' => ['required', 'string', 'max:255'],
            'recipient_province' => ['required', 'string', 'max:255'],
            'recipient_postal_code' => ['required', 'string', 'max:20'],
            'payment_method' => ['required', Rule::in($enabledMethods)],
        ]);

        $cart = $this->currentCart();
        if (! $cart || $cart->items()->doesntExist()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang Anda masih kosong.']);
        }

        $order = DB::transaction(function () use ($cart, $data, $settings) {
            $cartItems = $cart->items()->lockForUpdate()->get();
            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Keranjang Anda masih kosong.']);
            }

            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper((string) Str::uuid()),
                'user_id' => auth()->id(),
                'channel' => 'online',
                'order_status' => 'pending_payment',
                'currency' => 'IDR',
                'subtotal' => 0,
                'discount_total' => 0,
                'shipping_total' => 0,
                'grand_total' => 0,
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'recipient_address' => $data['recipient_address'],
                'recipient_city' => $data['recipient_city'],
                'recipient_province' => $data['recipient_province'],
                'recipient_postal_code' => $data['recipient_postal_code'],
            ]);

            $subtotal = 0;
            foreach ($cartItems as $cartItem) {
                $variant = ProductVariant::with(['product', 'size', 'color'])
                    ->lockForUpdate()
                    ->findOrFail($cartItem->variant_id);

                if (! $variant->is_active || $variant->product?->status !== 'active') {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk di keranjang sudah tidak tersedia.',
                    ]);
                }

                if ($cartItem->quantity > $variant->available_stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$variant->product->name} tidak mencukupi. Silakan perbarui keranjang.",
                    ]);
                }

                if (! $variant->reserve($cartItem->quantity, auth()->id(), 'order', $order->id)) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$variant->product->name} baru saja berubah. Silakan coba lagi.",
                    ]);
                }

                $lineTotal = (float) $variant->price * $cartItem->quantity;
                $order->items()->create([
                    'variant_id' => $variant->id,
                    'product_name_snapshot' => $variant->product->name,
                    'sku_snapshot' => $variant->sku,
                    'size_snapshot' => $variant->size?->name,
                    'color_snapshot' => $variant->color?->name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $variant->price,
                    'discount_amount' => 0,
                    'line_total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;
            }

            $codFee = $data['payment_method'] === 'cod' ? (float) $settings->cod_fee : 0;
            $order->update([
                'subtotal' => $subtotal,
                'shipping_total' => $codFee,
                'grand_total' => $subtotal + $codFee,
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => 'manual',
                'method' => $data['payment_method'],
                'status' => 'pending',
                'amount' => $order->grand_total,
            ]);

            if ($data['payment_method'] === 'cod') {
                $order->transitionTo('confirmed', auth()->id(), 'Pesanan COD');
            }

            $cart->items()->delete();

            return $order->load(['items', 'payment']);
        });

        session()->put('last_checkout_order_id', $order->id);

        return redirect()->route('checkout.success');
    }

    public function success(): View|RedirectResponse
    {
        $orderId = session('last_checkout_order_id');
        if (! $orderId) {
            return redirect()->route('catalog.index');
        }

        $order = Order::with(['items', 'payment'])->findOrFail($orderId);
        if ($order->user_id !== auth()->id()) {
            abort(404);
        }

        return view('storefront.orders.show', compact('order'));
    }

    public function showOrder(Order $order): View
    {
        abort_unless(auth()->id() === $order->user_id, 404);

        $order->load(['items', 'payment']);

        return view('storefront.orders.show', compact('order'));
    }

    private function currentCart(): ?Cart
    {
        return Cart::where('session_key', cartSessionKey())->first();
    }

    private function getOrCreateCart(): Cart
    {
        $cart = Cart::firstOrCreate(
            ['session_key' => cartSessionKey()],
            ['user_id' => auth()->id()]
        );

        if (auth()->check() && $cart->user_id !== auth()->id()) {
            $cart->update(['user_id' => auth()->id()]);
        }

        return $cart;
    }
}
