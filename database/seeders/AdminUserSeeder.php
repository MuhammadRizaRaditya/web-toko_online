<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create owner user
        $owner = User::firstOrCreate(
            ['email' => 'owner@rizwear.local'],
            [
                'name' => 'Owner RIZWEAR',
                'password' => bcrypt('password123'),
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $owner->assignRole('owner');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@rizwear.local'],
            [
                'name' => 'Admin RIZWEAR',
                'password' => bcrypt('password123'),
                'phone' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create cashier user
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@rizwear.local'],
            [
                'name' => 'Kasir RIZWEAR',
                'password' => bcrypt('password123'),
                'phone' => '081234567892',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $cashier->assignRole('cashier');

        // Create test customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@rizwear.local'],
            [
                'name' => 'Test Customer',
                'password' => bcrypt('password123'),
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole('customer');
    }
}
