<?php

namespace Tests\Feature\Storefront;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Payment;
use App\Models\PosSetting;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $this->variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock_on_hand' => 10,
            'stock_reserved' => 0,
            'is_active' => true,
        ]);
    }

    private function addVariantToCart(int $quantity = 1): void
    {
        $this->post(route('cart.store'), [
            'variant_id' => $this->variant->id,
            'quantity' => $quantity,
        ])->assertRedirect(route('cart.index'));
    }

    private function checkoutPayload(string $paymentMethod = 'transfer'): array
    {
        return [
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '08123456789',
            'recipient_address' => 'Jl. Merdeka No. 1',
            'recipient_city' => 'Jakarta',
            'recipient_province' => 'DKI Jakarta',
            'recipient_postal_code' => '10110',
            'payment_method' => $paymentMethod,
        ];
    }

    public function test_guest_can_add_product_and_checkout_with_manual_transfer(): void
    {
        $this->addVariantToCart(2);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Keranjang Belanja')
            ->assertSee($this->variant->product->name);

        $this->post(route('checkout.store'), $this->checkoutPayload())
            ->assertRedirect(route('checkout.success'));

        $this->get(route('checkout.success'))
            ->assertOk()
            ->assertSee('Pesanan berhasil dibuat')
            ->assertSee('transfer manual');

        $this->assertDatabaseHas('orders', [
            'channel' => 'online',
            'order_status' => 'pending_payment',
            'recipient_name' => 'Budi Santoso',
        ]);
        $this->assertDatabaseHas('payments', [
            'method' => 'transfer',
            'status' => 'pending',
        ]);
        $this->assertSame(2, $this->variant->fresh()->stock_reserved);
        $this->assertSame(0, Cart::first()->items()->count());
    }

    public function test_product_detail_exposes_add_to_cart_form(): void
    {
        $this->get(route('product.show', $this->variant->product))
            ->assertOk()
            ->assertSee(route('cart.store'), false)
            ->assertSee('Tambahkan ke keranjang');
    }

    public function test_cod_checkout_confirms_order_and_adds_configured_fee(): void
    {
        PosSetting::create(['cod_fee' => 5000]);
        $this->addVariantToCart();

        $this->post(route('checkout.store'), $this->checkoutPayload('cod'))
            ->assertRedirect(route('checkout.success'));

        $order = \App\Models\Order::where('channel', 'online')->firstOrFail();
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        $this->assertSame('confirmed', $order->order_status);
        $this->assertSame('pending', $payment->status);
        $this->assertEquals(5000, (float) $order->shipping_total);
        $this->assertEquals((float) $order->subtotal + 5000, (float) $order->grand_total);
    }

    public function test_checkout_rejects_insufficient_stock_without_creating_order(): void
    {
        $this->addVariantToCart(2);
        $this->variant->update(['stock_on_hand' => 1]);

        $this->from(route('checkout.index'))
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertRedirect(route('checkout.index'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertSame(0, $this->variant->fresh()->stock_reserved);
        $this->assertSame(2, Cart::first()->items()->first()->quantity);
    }

    public function test_checkout_validates_required_shipping_details(): void
    {
        $this->addVariantToCart();

        $this->from(route('checkout.index'))
            ->post(route('checkout.store'), ['payment_method' => 'transfer'])
            ->assertRedirect(route('checkout.index'))
            ->assertSessionHasErrors([
                'recipient_name',
                'recipient_phone',
                'recipient_address',
                'recipient_city',
                'recipient_province',
                'recipient_postal_code',
            ]);
    }
}
