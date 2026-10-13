<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StockConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_inventory_no_negative_stock_under_concurrency(): void
    {
        $size = Size::where('name', 'M')->first();
        $color = Color::where('name', 'Black')->first();
        $category = Category::firstOrCreate(['slug' => 'test-concurrency'], ['name' => 'Test Concurrency']);
        $product = Product::firstOrCreate(
            ['slug' => 'concurrency-test-shirt'],
            ['category_id' => $category->id, 'name' => 'Concurrency Test Shirt', 'status' => 'active']
        );

        $variant = ProductVariant::firstOrCreate(
            ['sku' => 'CONC-M-BLK'],
            [
                'product_id' => $product->id,
                'size_id' => $size->id,
                'color_id' => $color->id,
                'price' => 100000,
                'stock_on_hand' => 1,
                'stock_reserved' => 0,
                'low_stock_threshold' => 1,
                'is_active' => true,
            ]
        );

        // Simulate concurrent reserve attempts
        $results = [];
        for ($i = 0; $i < 5; $i++) {
            $results[] = DB::transaction(function () use ($variant) {
                return $variant->fresh()->reserve(1);
            });
        }

        $successCount = count(array_filter($results));
        $this->assertEquals(1, $successCount, 'Only one concurrent request should succeed');

        $variant->refresh();
        $this->assertEquals(1, $variant->stock_on_hand);
        $this->assertEquals(1, $variant->stock_reserved);
        $this->assertEquals(0, $variant->available_stock);

        // Verify stock movement recorded
        $this->assertEquals(1, $variant->stockMovements()->where('movement_type', 'reservation')->count());
    }

    public function test_reserve_commit_release_cycle(): void
    {
        $size = Size::where('name', 'M')->first();
        $color = Color::where('name', 'Black')->first();
        $category = Category::firstOrCreate(['slug' => 'test-cycle'], ['name' => 'Test Cycle']);
        $product = Product::firstOrCreate(
            ['slug' => 'cycle-test-shirt'],
            ['category_id' => $category->id, 'name' => 'Cycle Test Shirt', 'status' => 'active']
        );

        $variant = ProductVariant::firstOrCreate(
            ['sku' => 'CYCLE-M-BLK'],
            [
                'product_id' => $product->id,
                'size_id' => $size->id,
                'color_id' => $color->id,
                'price' => 100000,
                'stock_on_hand' => 10,
                'stock_reserved' => 0,
                'low_stock_threshold' => 2,
                'is_active' => true,
            ]
        );

        // Initial state
        $this->assertEquals(10, $variant->available_stock);

        // Reserve 3
        $this->assertTrue($variant->reserve(3));
        $variant->refresh();
        $this->assertEquals(7, $variant->available_stock);
        $this->assertEquals(3, $variant->stock_reserved);

        // Commit 3 (payment confirmed)
        $variant->commit(3);
        $variant->refresh();
        $this->assertEquals(7, $variant->stock_on_hand);
        $this->assertEquals(0, $variant->stock_reserved);
        $this->assertEquals(7, $variant->available_stock);

        // Release 2 (cancel 2 items)
        $variant->reserve(2); // re-reserve for release test
        $variant->refresh();
        $this->assertEquals(5, $variant->available_stock);
        $variant->release(2);
        $variant->refresh();
        $this->assertEquals(7, $variant->available_stock);
        $this->assertEquals(0, $variant->stock_reserved);

        // Verify stock movements
        $this->assertEquals(4, $variant->stockMovements()->count()); // reserve, commit, reserve, release
    }

    public function test_over_reserve_fails(): void
    {
        $size = Size::where('name', 'M')->first();
        $color = Color::where('name', 'Black')->first();
        $category = Category::firstOrCreate(['slug' => 'test-over'], ['name' => 'Test Over']);
        $product = Product::firstOrCreate(
            ['slug' => 'over-test-shirt'],
            ['category_id' => $category->id, 'name' => 'Over Test Shirt', 'status' => 'active']
        );

        $variant = ProductVariant::firstOrCreate(
            ['sku' => 'OVER-M-BLK'],
            [
                'product_id' => $product->id,
                'size_id' => $size->id,
                'color_id' => $color->id,
                'price' => 100000,
                'stock_on_hand' => 2,
                'stock_reserved' => 0,
                'low_stock_threshold' => 1,
                'is_active' => true,
            ]
        );

        $this->assertTrue($variant->reserve(2));
        $this->assertFalse($variant->reserve(1), 'Should fail when no stock available');
    }
}
