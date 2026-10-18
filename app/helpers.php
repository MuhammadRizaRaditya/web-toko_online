<?php

use App\Models\Cart;
use App\Models\CartItem;

if (! function_exists('cartItemCount')) {
    /**
     * Jumlah total item di keranjang user yang sedang login (0 untuk tamu).
     */
    function cartItemCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        $cartIds = Cart::where('user_id', auth()->id())->select('id');

        return (int) CartItem::whereIn('cart_id', $cartIds)->sum('quantity');
    }
}
