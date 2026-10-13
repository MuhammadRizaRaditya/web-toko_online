<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        // Customer can view own payment
        if ($user->hasRole('customer')) {
            return $payment->order->user_id === $user->id;
        }

        return $user->can('payments.view');
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->can('payments.verify');
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->can('payments.refund');
    }
}
