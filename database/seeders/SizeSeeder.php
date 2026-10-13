<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['name' => 'One Size', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'XS', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'S', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'M', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'L', 'sort_order' => 5, 'is_active' => true],
            ['name' => 'XL', 'sort_order' => 6, 'is_active' => true],
            ['name' => 'XXL', 'sort_order' => 7, 'is_active' => true],
        ];

        foreach ($sizes as $size) {
            Size::firstOrCreate(['name' => $size['name']], $size);
        }
    }
}
