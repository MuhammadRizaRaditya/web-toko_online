<?php

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;

if (! function_exists('cartSessionKey')) {
    function cartSessionKey(): string
    {
        $key = session()->get('cart_session_key');

        if (! is_string($key) || $key === '') {
            $key = (string) Str::uuid();
            session()->put('cart_session_key', $key);
        }

        return $key;
    }
}

if (! function_exists('cartItemCount')) {
    /**
     * Jumlah total item pada keranjang user atau sesi tamu yang sedang aktif.
     */
    function cartItemCount(): int
    {
        $cart = Cart::where('session_key', cartSessionKey());

        return (int) CartItem::whereIn('cart_id', $cart->select('id'))->sum('quantity');
    }
}
