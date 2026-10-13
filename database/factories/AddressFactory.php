<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address_line' => $this->faker->address,
            'province' => $this->faker->state,
            'city' => $this->faker->city,
            'district' => $this->faker->citySuffix,
            'postal_code' => $this->faker->postcode,
            'is_default' => false,
        ];
    }
}
