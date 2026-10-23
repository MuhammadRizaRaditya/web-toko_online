<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $demoPassword = env('RIZWEAR_DEMO_PASSWORD');

        if (! is_string($demoPassword) || strlen($demoPassword) < 12) {
            $this->command?->warn('Demo accounts skipped. Set RIZWEAR_DEMO_PASSWORD to a password with at least 12 characters to seed them.');

            return;
        }

        // Create owner user
        $owner = User::firstOrCreate(
            ['email' => 'owner@rizwear.local'],
            [
                'name' => 'Owner RIZWEAR',
                'password' => Hash::make($demoPassword),
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
                'password' => Hash::make($demoPassword),
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
                'password' => Hash::make($demoPassword),
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
                'password' => Hash::make($demoPassword),
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole('customer');
    }
}
