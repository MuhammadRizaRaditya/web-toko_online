<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;

class StockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('stock.view');
    }

    public function view(User $user, ProductVariant $variant): bool
    {
        return $user->can('stock.view');
    }

    public function adjust(User $user, ProductVariant $variant): bool
    {
        return $user->can('stock.adjust');
    }

    public function manageThresholds(User $user, ProductVariant $variant): bool
    {
        return $user->can('stock.manage_thresholds');
    }
}
