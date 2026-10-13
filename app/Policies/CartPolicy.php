<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;

class CartPolicy
{
    public function view(User $user, Cart $cart): bool
    {
        // User owns the cart
        if ($cart->user_id === $user->id) {
            return true;
        }

        // Guest cart via session
        if ($cart->user_id === null && session()->has('cart_id') && session('cart_id') === $cart->id) {
            return true;
        }

        // Admin can view all
        return $user->can('carts.view');
    }

    public function update(User $user, Cart $cart): bool
    {
        return $this->view($user, $cart);
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $this->view($user, $cart);
    }
}
