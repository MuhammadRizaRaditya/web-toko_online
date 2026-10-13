<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size_id' => Size::factory(),
            'color_id' => Color::factory(),
            'sku' => 'SKU-'.strtoupper($this->faker->unique()->lexify('??????')),
            'barcode' => $this->faker->unique()->ean13,
            'price' => $this->faker->numberBetween(50000, 500000),
            'compare_at_price' => $this->faker->optional()->numberBetween(60000, 600000),
            'stock_on_hand' => $this->faker->numberBetween(0, 100),
            'stock_reserved' => 0,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ];
    }
}
