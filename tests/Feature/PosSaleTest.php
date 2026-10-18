<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSaleTest extends TestCase
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

    protected function createSaleableVariant(int $stock = 10, int $price = 100000): ProductVariant
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);
        $size = Size::factory()->create();
        $color = Color::factory()->create();

        return ProductVariant::factory()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $color->id,
            'price' => $price,
            'stock_on_hand' => $stock,
            'stock_reserved' => 0,
            'is_active' => true,
        ]);
    }

    protected function salePayload(ProductVariant $variant, int $qty = 1, string $method = 'cod'): array
    {
        return [
            'variant_ids' => [$variant->id],
            'quantities' => [$qty],
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'customer_address' => 'Jl. Merdeka No 1, Jakarta',
            'payment_method' => $method,
        ];
    }

    public function test_guest_is_redirected_from_pos(): void
    {
        $this->get('/pos')->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_pos(): void
    {
        $customer = $this->createUserWithRole('customer');

        $this->actingAs($customer)->get('/pos')->assertStatus(403);
    }

    public function test_cashier_can_access_pos(): void
    {
        $cashier = $this->createUserWithRole('cashier');

        $this->actingAs($cashier)->get('/pos')->assertStatus(200);
    }

    public function test_cod_sale_confirms_order_and_marks_payment_paid(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $variant = $this->createSaleableVariant(stock: 10, price: 150000);

        $response = $this->actingAs($cashier)
            ->postJson('/pos/record-sale', $this->salePayload($variant, qty: 2, method: 'cod'));

        $response->assertOk()->assertJsonPath('success', true);

        $order = $response->json('order');
        $this->assertSame('confirmed', $order['order_status']);
        $this->assertSame('pos', $order['channel']);
        $this->assertEquals(300000, (float) $order['subtotal']);
        $this->assertSame('paid', $response->json('payment.status'));
        $this->assertCount(1, $order['items']);
        $this->assertSame($variant->sku, $order['items'][0]['sku_snapshot']);

        $variant->refresh();
        $this->assertSame(2, $variant->stock_reserved);
        $this->assertSame(10, $variant->stock_on_hand);
    }

    public function test_transfer_sale_keeps_order_pending_and_payment_pending(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $variant = $this->createSaleableVariant(stock: 5, price: 100000);

        $response = $this->actingAs($cashier)
            ->postJson('/pos/record-sale', $this->salePayload($variant, qty: 1, method: 'transfer'));

        $response->assertOk()->assertJsonPath('success', true);
        $response->assertJsonPath('order.order_status', 'pending_payment');
        $response->assertJsonPath('payment.status', 'pending');
    }

    public function test_sale_fails_and_rolls_back_when_stock_insufficient(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $variant = $this->createSaleableVariant(stock: 1, price: 100000);

        $this->actingAs($cashier)
            ->postJson('/pos/record-sale', $this->salePayload($variant, qty: 5))
            ->assertStatus(422);

        $variant->refresh();
        $this->assertSame(0, $variant->stock_reserved);
        $this->assertSame(1, $variant->stock_on_hand);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_cod_fee_only_applied_for_cod_payments(): void
    {
        \App\Models\PosSetting::create(['cod_fee' => 5000]);

        $cashier = $this->createUserWithRole('cashier');
        $variant = $this->createSaleableVariant(stock: 5, price: 100000);

        $transfer = $this->actingAs($cashier)
            ->postJson('/pos/record-sale', $this->salePayload($variant, qty: 1, method: 'transfer'));
        $transfer->assertOk();
        $this->assertEquals(100000, (float) $transfer->json('order.grand_total'));
        $this->assertEquals(0, (float) $transfer->json('order.shipping_total'));

        $cod = $this->actingAs($cashier)
            ->postJson('/pos/record-sale', $this->salePayload($variant, qty: 1, method: 'cod'));
        $cod->assertOk();
        $this->assertEquals(105000, (float) $cod->json('order.grand_total'));
        $this->assertEquals(5000, (float) $cod->json('order.shipping_total'));
    }
}
