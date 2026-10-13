<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('orders.view');
    }

    public function view(User $user, Order $order): bool
    {
        // Customer can view own orders
        if ($user->hasRole('customer')) {
            return $order->user_id === $user->id;
        }

        // Cashier can only view POS orders
        if ($user->hasRole('cashier')) {
            return $order->channel === 'pos';
        }

        // Admin/owner can view all
        return $user->can('orders.view');
    }

    public function create(User $user): bool
    {
        // Customers and guests can create orders via checkout
        return true;
    }

    public function update(User $user, Order $order): bool
    {
        // Admin/owner can update
        return $user->can('orders.manage');
    }

    public function cancel(User $user, Order $order): bool
    {
        // Customer can cancel own order if pending
        if ($user->hasRole('customer')) {
            return $order->user_id === $user->id
                && in_array($order->order_status, ['pending_payment', 'confirmed']);
        }

        // Admin/owner can cancel with permission
        return $user->can('orders.cancel');
    }

    public function refund(User $user, Order $order): bool
    {
        return $user->can('orders.refund');
    }

    public function manageStatus(User $user, Order $order): bool
    {
        return $user->can('orders.manage');
    }

    public function delete(User $user, Order $order): bool
    {
        return false; // Orders are never deleted
    }

    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
