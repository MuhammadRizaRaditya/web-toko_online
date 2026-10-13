<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('customer') || $user->can('addresses.view');
    }

    public function view(User $user, Address $address): bool
    {
        return $address->user_id === $user->id || $user->can('addresses.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('customer') || $user->can('addresses.create');
    }

    public function update(User $user, Address $address): bool
    {
        return $address->user_id === $user->id || $user->can('addresses.edit');
    }

    public function delete(User $user, Address $address): bool
    {
        return $address->user_id === $user->id || $user->can('addresses.delete');
    }
}
