<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'RZW-'.date('Ymd').'-'.str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'channel' => 'online',
            'order_status' => 'pending_payment',
            'currency' => 'IDR',
            'subtotal' => 100000,
            'discount_total' => 0,
            'shipping_total' => 15000,
            'grand_total' => 115000,
            'recipient_name' => $this->faker->name,
            'recipient_phone' => $this->faker->phoneNumber,
            'recipient_address' => $this->faker->address,
            'recipient_city' => $this->faker->city,
            'recipient_province' => $this->faker->state,
            'recipient_postal_code' => $this->faker->postcode,
            'idempotency_key' => $this->faker->uuid,
        ];
    }
}
