<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Public catalog
    }

    public function view(User $user, Product $product): bool
    {
        return true; // Public product detail
    }

    public function create(User $user): bool
    {
        return $user->can('products.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can('products.edit');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can('products.delete');
    }

    public function manageVariants(User $user, Product $product): bool
    {
        return $user->can('products.manage_variants');
    }

    public function manageImages(User $user, Product $product): bool
    {
        return $user->can('products.manage_images');
    }
}
