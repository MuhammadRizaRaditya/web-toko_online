<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PolicyAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    protected function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    // ORD-001: Customer cannot access other customer's orders
    public function test_customer_cannot_view_other_customer_order(): void
    {
        $customer1 = $this->createUserWithRole('customer');
        $customer2 = $this->createUserWithRole('customer');

        $order = Order::factory()->create(['user_id' => $customer1->id]);

        $this->actingAs($customer2)
            ->getJson("/api/orders/{$order->id}")
            ->assertStatus(403);
    }

    public function test_customer_can_view_own_order(): void
    {
        $customer = $this->createUserWithRole('customer');
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->getJson("/api/orders/{$order->id}")
            ->assertStatus(200);
    }

    public function test_admin_can_view_any_order(): void
    {
        $customer = $this->createUserWithRole('customer');
        $admin = $this->createUserWithRole('admin');

        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($admin)
            ->getJson("/api/orders/{$order->id}")
            ->assertStatus(200);
    }

    public function test_owner_can_view_any_order(): void
    {
        $customer = $this->createUserWithRole('customer');
        $owner = $this->createUserWithRole('owner');

        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($owner)
            ->getJson("/api/orders/{$order->id}")
            ->assertStatus(200);
    }

    public function test_cashier_cannot_view_online_orders(): void
    {
        $customer = $this->createUserWithRole('customer');
        $cashier = $this->createUserWithRole('cashier');

        $order = Order::factory()->create(['user_id' => $customer->id, 'channel' => 'online']);

        $this->actingAs($cashier)
            ->getJson("/api/orders/{$order->id}")
            ->assertStatus(403);
    }

    public function test_customer_can_only_manage_own_addresses(): void
    {
        $customer1 = $this->createUserWithRole('customer');
        $customer2 = $this->createUserWithRole('customer');

        $address = Address::factory()->create(['user_id' => $customer1->id]);

        // Customer 2 cannot update customer 1's address
        $this->actingAs($customer2)
            ->putJson("/api/addresses/{$address->id}", ['address_line' => 'New'])
            ->assertStatus(403);

        // Customer 1 can update own address
        $this->actingAs($customer1)
            ->putJson("/api/addresses/{$address->id}", ['address_line' => 'New'])
            ->assertStatus(200);
    }

    public function test_only_admin_can_adjust_stock(): void
    {
        $customer = $this->createUserWithRole('customer');
        $admin = $this->createUserWithRole('admin');
        $cashier = $this->createUserWithRole('cashier');

        $variant = ProductVariant::factory()->create();

        // Customer cannot adjust
        $this->actingAs($customer)
            ->postJson("/api/stock/{$variant->id}/adjust", ['delta' => 5, 'reason' => 'test'])
            ->assertStatus(403);

        // Cashier cannot adjust
        $this->actingAs($cashier)
            ->postJson("/api/stock/{$variant->id}/adjust", ['delta' => 5, 'reason' => 'test'])
            ->assertStatus(403);

        // Admin can adjust
        $this->actingAs($admin)
            ->postJson("/api/stock/{$variant->id}/adjust", ['delta' => 5, 'reason' => 'test'])
            ->assertStatus(200);
    }

    public function test_only_admin_owner_can_manage_products(): void
    {
        $customer = $this->createUserWithRole('customer');
        $cashier = $this->createUserWithRole('cashier');
        $admin = $this->createUserWithRole('admin');
        $owner = $this->createUserWithRole('owner');

        $product = Product::factory()->create();

        // Customer cannot create
        $this->actingAs($customer)
            ->postJson('/api/products', ['name' => 'Test', 'slug' => 'test', 'category_id' => 1])
            ->assertStatus(403);

        // Cashier cannot create
        $this->actingAs($cashier)
            ->postJson('/api/products', ['name' => 'Test', 'slug' => 'test', 'category_id' => 1])
            ->assertStatus(403);

        // Admin can create
        $this->actingAs($admin)
            ->postJson('/api/products', ['name' => 'Test', 'slug' => 'test', 'category_id' => 1])
            ->assertStatus(201);

        // Owner can create
        $this->actingAs($owner)
            ->postJson('/api/products', ['name' => 'Test', 'slug' => 'test', 'category_id' => 1])
            ->assertStatus(201);
    }

    public function test_customer_can_only_access_own_cart(): void
    {
        $customer1 = $this->createUserWithRole('customer');
        $customer2 = $this->createUserWithRole('customer');

        $cart = Cart::factory()->create(['user_id' => $customer1->id]);

        // Customer 2 cannot access customer 1's cart
        $this->actingAs($customer2)
            ->getJson("/api/cart/{$cart->id}")
            ->assertStatus(403);

        // Customer 1 can access own cart
        $this->actingAs($customer1)
            ->getJson("/api/cart/{$cart->id}")
            ->assertStatus(200);
    }

    public function test_role_middleware_works(): void
    {
        $customer = $this->createUserWithRole('customer');
        $admin = $this->createUserWithRole('admin');

        // Route with role:admin middleware
        $this->actingAs($customer)
            ->get('/admin/test')
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get('/admin/test')
            ->assertStatus(200);
    }

    public function test_permission_middleware_works(): void
    {
        $customer = $this->createUserWithRole('customer');
        $admin = $this->createUserWithRole('admin');

        // Route with permission:orders.view middleware
        $this->actingAs($customer)
            ->get('/admin/orders')
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get('/admin/orders')
            ->assertStatus(200);
    }
}
