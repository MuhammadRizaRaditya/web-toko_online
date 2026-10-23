<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createPendingTransfer(array $orderAttributes = []): Order
    {
        $order = Order::factory()->create(array_merge([
            'channel' => 'online',
            'order_status' => 'pending_payment',
        ], $orderAttributes));

        Payment::create([
            'order_id' => $order->id,
            'provider' => 'manual',
            'method' => 'transfer',
            'status' => 'pending',
            'amount' => $order->grand_total,
        ]);

        return $order;
    }

    public function test_admin_can_verify_a_pending_online_transfer(): void
    {
        $admin = $this->createUserWithRole('admin');
        $order = $this->createPendingTransfer();

        $this->actingAs($admin)
            ->get(route('admin.payments.transfers.index'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Konfirmasi pembayaran');

        $this->actingAs($admin)
            ->post(route('admin.payments.transfers.verify', $order))
            ->assertRedirect(route('admin.payments.transfers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'confirmed',
        ]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'confirmed',
            'actor_user_id' => $admin->id,
        ]);
    }

    public function test_customer_cannot_access_transfer_verification(): void
    {
        $customer = $this->createUserWithRole('customer');
        $order = $this->createPendingTransfer();

        $this->actingAs($customer)
            ->get(route('admin.payments.transfers.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->post(route('admin.payments.transfers.verify', $order))
            ->assertForbidden();
    }

    public function test_transfer_verification_rejects_non_online_orders(): void
    {
        $admin = $this->createUserWithRole('admin');
        $order = $this->createPendingTransfer(['channel' => 'pos']);

        $this->actingAs($admin)
            ->from(route('admin.payments.transfers.index'))
            ->post(route('admin.payments.transfers.verify', $order))
            ->assertRedirect(route('admin.payments.transfers.index'))
            ->assertSessionHasErrors('payment');

        $this->assertSame('pending', $order->payment()->firstOrFail()->status);
        $this->assertSame('pending_payment', $order->fresh()->order_status);
        $this->assertSame(0, OrderStatusHistory::where('order_id', $order->id)->count());
    }
}
