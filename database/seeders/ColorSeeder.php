<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Default', 'hex_code' => '#000000', 'is_active' => true],
            ['name' => 'Black', 'hex_code' => '#000000', 'is_active' => true],
            ['name' => 'White', 'hex_code' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Navy', 'hex_code' => '#000080', 'is_active' => true],
            ['name' => 'Grey', 'hex_code' => '#808080', 'is_active' => true],
            ['name' => 'Beige', 'hex_code' => '#F5F5DC', 'is_active' => true],
            ['name' => 'Olive', 'hex_code' => '#808000', 'is_active' => true],
            ['name' => 'Maroon', 'hex_code' => '#800000', 'is_active' => true],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(['name' => $color['name']], $color);
        }
    }
}
