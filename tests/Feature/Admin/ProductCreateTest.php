<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    /** PNG 1x1 valid (bytes asli, tidak butuh ekstensi GD). */
    private const PNG_BYTES = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private Category $category;
    private Size $sizeM;
    private Size $sizeL;
    private Color $colorHitam;
    private Color $colorPutih;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh --seed');

        $this->category = Category::factory()->create(['is_active' => true]);
        $this->sizeM = Size::factory()->create(['name' => 'M-Riz', 'sort_order' => 1, 'is_active' => true]);
        $this->sizeL = Size::factory()->create(['name' => 'L-Riz', 'sort_order' => 2, 'is_active' => true]);
        $this->colorHitam = Color::factory()->create(['name' => 'Hitam Riz', 'is_active' => true]);
        $this->colorPutih = Color::factory()->create(['name' => 'Putih Riz', 'is_active' => true]);

        $this->admin = User::factory()->create(['email' => 'admin-produk-test@rizwear.local']);
        $this->admin->assignRole('admin');
    }

    private function pngFile(string $name = 'produk.png', int $extraBytes = 0): UploadedFile
    {
        $content = base64_decode(self::PNG_BYTES);

        if ($extraBytes > 0) {
            $content .= str_repeat("\0", $extraBytes);
        }

        return UploadedFile::fake()->createWithContent($name, $content);
    }

    /** Kembali jadi tamu setelah actingAs (guard Laravel masih menyimpan user). */
    private function actingAsGuest(): void
    {
        $this->app['auth']->forgetGuards();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->category->id,
            'name' => 'Kaos Oversized Premium',
            'description' => 'Kaos nyaman untuk sehari-hari.',
            'brand' => 'RIZWEAR',
            'material' => 'Katun Combed 30s',
            'fit_style' => 'Oversized',
            'weight_grams' => 300,
            'size_chart' => "M : 55 cm\nL : 58 cm",
            'labels' => ['new', 'promo'],
            'price' => 150000,
            'compare_at_price' => 200000,
            'cost_price' => 80000,
            'is_featured' => '1',
            'primary_image' => $this->pngFile(),
            'images' => [$this->pngFile('galeri.png')],
            'variants' => [
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => '', 'stock' => 10, 'is_active' => '1'],
                ['size_id' => $this->sizeL->id, 'color_id' => $this->colorHitam->id, 'sku' => '', 'stock' => 5, 'price' => 160000, 'is_active' => '1'],
                ['size_id' => $this->sizeL->id, 'color_id' => $this->colorPutih->id, 'sku' => '', 'stock' => 0, 'is_active' => '0'],
            ],
            'intent' => 'active',
        ], $overrides);
    }

    public function test_guest_is_redirected_from_product_admin_pages(): void
    {
        $this->get('/admin/products/create')->assertRedirect(route('login'));
        $this->post('/admin/products', $this->payload())->assertRedirect(route('login'));
    }

    public function test_only_admin_and_owner_can_access_product_pages(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $this->actingAs($customer)->get('/admin/products/create')->assertForbidden();
        $this->actingAs($cashier)->get('/admin/products/create')->assertForbidden();
        $this->actingAs($customer)->post('/admin/products', $this->payload())->assertForbidden();

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)->get('/admin/products/create')->assertOk();
    }

    public function test_create_form_shows_sections_and_reference_data(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/products/create');

        $response->assertOk()
            ->assertSee('Tambah Produk Baru')
            ->assertSee('Informasi Produk')
            ->assertSee('Foto Produk')
            ->assertSee('Harga dan Diskon')
            ->assertSee('Varian, SKU, dan Stok')
            ->assertSee('Informasi Tambahan')
            ->assertSee('Status dan Publikasi')
            ->assertSee('M-Riz')
            ->assertSee('L-Riz')
            ->assertSee('Hitam Riz')
            ->assertSee('Putih Riz');
    }

    public function test_store_creates_product_variants_images_and_initial_stock(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/products', $this->payload());

        $product = Product::where('slug', 'kaos-oversized-premium')->firstOrFail();

        $response->assertRedirect(route('admin.products.edit', $product));

        $this->assertSame('active', $product->status);
        $this->assertSame('Kaos Oversized Premium', $product->name);
        $this->assertSame('RIZWEAR', $product->brand);
        $this->assertSame('Katun Combed 30s', $product->material);
        $this->assertSame('Oversized', $product->fit_style);
        $this->assertSame(300, $product->weight_grams);
        $this->assertSame(['new', 'promo'], $product->labels);
        $this->assertTrue($product->is_featured);
        $this->assertEquals(80000, (float) $product->cost_price);
        $this->assertSame($this->admin->id, $product->created_by);

        $variants = $product->variants()->orderBy('id')->get();
        $this->assertCount(3, $variants);

        $this->assertSame(3, $variants->pluck('sku')->unique()->count());
        foreach ($variants as $variant) {
            $this->assertNotEmpty($variant->sku);
            $this->assertMatchesRegularExpression('/^[A-Z0-9\-_]+$/', $variant->sku);
        }

        $this->assertEquals(150000, (float) $variants[0]->price);
        $this->assertEquals(160000, (float) $variants[1]->price);
        $this->assertEquals(150000, (float) $variants[2]->price);
        $this->assertEquals(200000, (float) $variants[0]->compare_at_price);
        $this->assertEquals(200000, (float) $variants[1]->compare_at_price);
        $this->assertEquals([10, 5, 0], $variants->pluck('stock_on_hand')->all());
        $this->assertTrue($variants[0]->is_active);
        $this->assertTrue($variants[1]->is_active);
        $this->assertFalse($variants[2]->is_active);

        $images = $product->images()->orderBy('sort_order')->get();
        $this->assertCount(2, $images);
        $this->assertTrue($images[0]->is_primary);
        $this->assertFalse($images[1]->is_primary);
        $this->assertCount(2, Storage::disk('public')->allFiles("products/{$product->id}"));
        $this->assertSame([], Storage::disk('public')->allFiles('products/tmp'));

        $movements = StockMovement::whereIn('variant_id', $variants->pluck('id'))->get();
        $this->assertCount(2, $movements);
        $this->assertSame(15, (int) $movements->sum('quantity_delta'));
        $this->assertTrue($movements->every(fn ($m) => $m->movement_type === 'purchase_receive'));

        // Halaman admin terkait ikut dirender
        $this->actingAs($this->admin)->get(route('admin.products.edit', $product))->assertOk()
            ->assertSee('Kaos Oversized Premium');
        $this->actingAs($this->admin)->get(route('admin.products.show', $product))->assertOk()
            ->assertSee('Kaos Oversized Premium');
        $this->actingAs($this->admin)->get('/admin/products')->assertOk()
            ->assertSee('Kaos Oversized Premium');
        // Pencarian SKU di index (tanpa kolom products.sku)
        $this->actingAs($this->admin)->get('/admin/products?search=KAOSOVERS')->assertOk()
            ->assertSee('Kaos Oversized Premium');
    }

    public function test_draft_product_is_hidden_from_storefront_but_previewable_by_admin(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'name' => 'Produk Draft Rahasia Unik-XYZ',
            'images' => [],
            'intent' => 'draft',
        ]));

        $product = Product::where('slug', 'produk-draft-rahasia-unik-xyz')->firstOrFail();
        $this->assertSame('draft', $product->status);

        $this->actingAsGuest();

        $this->get('/produk')->assertOk()->assertDontSee('Produk Draft Rahasia Unik-XYZ');
        $this->get('/produk/'.$product->slug)->assertNotFound();

        $this->actingAs($this->admin)->get('/produk/'.$product->slug)
            ->assertOk()
            ->assertSee('Mode pratinjau admin');
    }

    public function test_sku_is_auto_generated_when_empty_and_manual_sku_is_normalized(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'variants' => [
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => '', 'stock' => 3],
                ['size_id' => $this->sizeL->id, 'color_id' => $this->colorHitam->id, 'sku' => '  manual-sku-001 ', 'stock' => 4],
            ],
        ]));

        $response->assertSessionHasNoErrors();

        $product = Product::where('slug', 'kaos-oversized-premium')->firstOrFail();
        $variants = $product->variants()->orderBy('id')->get();

        $this->assertNotEmpty($variants[0]->sku);
        $this->assertMatchesRegularExpression('/^[A-Z0-9\-_]+$/', $variants[0]->sku);
        $this->assertSame('MANUAL-SKU-001', $variants[1]->sku);
        $this->assertNotSame($variants[0]->sku, $variants[1]->sku);
    }

    public function test_duplicate_variant_combination_is_rejected_without_partial_data(): void
    {
        Storage::fake('public');

        $beforeProducts = Product::count();

        $response = $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'variants' => [
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => 'KOMB-A', 'stock' => 1],
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => 'KOMB-B', 'stock' => 2],
            ],
        ]));

        $response->assertSessionHasErrors('variants');
        $this->assertSame($beforeProducts, Product::count());
        $this->assertSame([], Storage::disk('public')->allFiles('products'));
    }

    public function test_duplicate_sku_within_payload_is_rejected(): void
    {
        Storage::fake('public');

        $beforeProducts = Product::count();

        $response = $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'variants' => [
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => 'SKU-DUP-1', 'stock' => 1],
                ['size_id' => $this->sizeL->id, 'color_id' => $this->colorPutih->id, 'sku' => 'SKU-DUP-1', 'stock' => 2],
            ],
        ]));

        $response->assertSessionHasErrors('variants');
        $this->assertSame($beforeProducts, Product::count());
        $this->assertSame([], Storage::disk('public')->allFiles('products'));
    }

    public function test_sku_already_used_in_database_is_rejected(): void
    {
        Storage::fake('public');

        ProductVariant::factory()->create(['sku' => 'SUDAH-ADA-01']);

        $beforeProducts = Product::count();

        $response = $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'variants' => [
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => 'sudah-ada-01', 'stock' => 1],
            ],
        ]));

        $response->assertSessionHasErrors('variants.0.sku');
        $this->assertSame($beforeProducts, Product::count());
        $this->assertSame([], Storage::disk('public')->allFiles('products'));
    }

    public function test_store_requires_core_fields(): void
    {
        Storage::fake('public');

        $beforeProducts = Product::count();

        $this->actingAs($this->admin)->post('/admin/products', [])
            ->assertSessionHasErrors([
                'category_id',
                'name',
                'price',
                'variants',
                'primary_image',
            ]);

        $this->assertSame($beforeProducts, Product::count());
    }

    public function test_invalid_label_value_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'labels' => ['promo', 'tidak-ada'],
        ]))->assertSessionHasErrors('labels.1');
    }

    public function test_slug_is_auto_generated_unique_and_manual_duplicates_rejected(): void
    {
        Storage::fake('public');

        Product::factory()->create(['slug' => 'kaos-hitam', 'category_id' => $this->category->id]);

        // Slug kosong: dibuat otomatis, disuffix bila bentrok
        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'name' => 'Kaos Hitam',
            'images' => [],
        ]))->assertSessionHasNoErrors();

        $product = Product::where('slug', 'kaos-hitam-2')->firstOrFail();
        $this->assertSame('Kaos Hitam', $product->name);

        // Slug manual yang bentrok: ditolak validasi
        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'name' => 'Kaos Hitam Lain',
            'slug' => 'kaos-hitam',
            'images' => [],
        ]))->assertSessionHasErrors('slug');

        $this->assertSame(2, Product::count());
    }

    public function test_compare_price_lower_than_selling_price_is_rejected(): void
    {
        Storage::fake('public');

        // Level produk
        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'compare_at_price' => 100000,
        ]))->assertSessionHasErrors('compare_at_price');

        // Level varian (harga varian efektif lebih tinggi dari compare)
        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'compare_at_price' => null,
            'variants' => [
                ['size_id' => $this->sizeM->id, 'color_id' => $this->colorHitam->id, 'sku' => 'ROWCMP-1', 'stock' => 1, 'compare_at_price' => 100000],
            ],
        ]))->assertSessionHasErrors('variants.0.compare_at_price');

        $this->assertSame(0, Product::where('slug', 'kaos-oversized-premium')->count());
    }

    public function test_invalid_image_type_and_oversize_image_are_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'primary_image' => UploadedFile::fake()->createWithContent('catatan.txt', 'bukan gambar sama sekali'),
        ]))->assertSessionHasErrors('primary_image');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'primary_image' => $this->pngFile('besar.png', 3 * 1024 * 1024),
        ]))->assertSessionHasErrors('primary_image');

        $this->assertSame(0, Product::where('slug', 'kaos-oversized-premium')->count());
        $this->assertSame([], Storage::disk('public')->allFiles('products'));
    }

    public function test_database_failure_rolls_back_transaction_and_cleans_up_files(): void
    {
        Storage::fake('public');

        $beforeProducts = Product::count();
        $beforeImages = ProductImage::count();
        $beforeVariants = ProductVariant::count();

        ProductVariant::creating(function (): void {
            throw new RuntimeException('Simulasi kegagalan database saat simpan varian');
        });

        $response = $this->actingAs($this->admin)->post('/admin/products', $this->payload());

        $response->assertSessionHasErrors('error');

        // Tidak ada data parsial yang tertinggal
        $this->assertSame($beforeProducts, Product::count());
        $this->assertSame($beforeImages, ProductImage::count());
        $this->assertSame($beforeVariants, ProductVariant::count());
        $this->assertSame([], Storage::disk('public')->allFiles('products'));

        $numericProductDirs = array_filter(
            Storage::disk('public')->allDirectories('products'),
            fn (string $dir) => (bool) preg_match('#^products/\d+$#', $dir)
        );
        $this->assertEmpty($numericProductDirs);
    }

    public function test_active_product_shows_on_catalog_and_detail_without_hpp_leak(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload());

        $product = Product::where('slug', 'kaos-oversized-premium')->firstOrFail();

        $this->actingAsGuest();

        // Katalog: kartu tampil lengkap dengan diskon dan badge unggulan
        $this->get('/produk')
            ->assertOk()
            ->assertSee('Kaos Oversized Premium')
            ->assertSee('-25%')
            ->assertSee('Unggulan')
            ->assertSee('Rp 200.000')
            ->assertDontSee('HPP')
            ->assertDontSee('Harga Modal');

        // Detail: harga, varian, stok — HPP tidak boleh bocor
        $this->get('/produk/'.$product->slug)
            ->assertOk()
            ->assertSee('Kaos Oversized Premium')
            ->assertSee('Rp 150.000')
            ->assertSee('M-Riz')
            ->assertSee('Hitam Riz')
            ->assertSee('Stok: 10')
            ->assertSee('Unggulan')
            ->assertSee('Katun Combed 30s')
            ->assertSee('sedang dalam pengembangan')
            ->assertDontSee('Rp 80.000')
            ->assertDontSee('HPP')
            ->assertDontSee('Harga Modal');
    }

    public function test_update_extended_fields_and_archived_status(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload());
        $product = Product::where('slug', 'kaos-oversized-premium')->firstOrFail();

        $this->actingAs($this->admin)->put('/admin/products/'.$product->id, [
            'category_id' => $this->category->id,
            'name' => 'Kaos Oversized Premium Edition',
            'slug' => 'kaos-oversized-premium',
            'status' => 'archived',
            'brand' => 'RIZWEAR Official',
            'labels' => ['best_seller'],
            'cost_price' => 95000,
            'is_featured' => '0',
        ])->assertRedirect(route('admin.products.edit', $product));

        $product->refresh();
        $this->assertSame('archived', $product->status);
        $this->assertSame('Kaos Oversized Premium Edition', $product->name);
        $this->assertSame('RIZWEAR Official', $product->brand);
        $this->assertSame(['best_seller'], $product->labels);
        $this->assertEquals(95000, (float) $product->cost_price);
        $this->assertFalse($product->is_featured);
        $this->assertSame('kaos-oversized-premium', $product->slug);

        // Produk terarsip: tamu 404, admin tetap bisa pratinjau
        $this->actingAsGuest();
        $this->get('/produk/'.$product->slug)->assertNotFound();
        $this->actingAs($this->admin)->get('/produk/'.$product->slug)
            ->assertOk()
            ->assertSee('Mode pratinjau admin');

        // Checkbox label tidak dicentang: labels dikosongkan
        $this->actingAs($this->admin)->put('/admin/products/'.$product->id, [
            'category_id' => $this->category->id,
            'name' => 'Kaos Oversized Premium Edition',
            'slug' => 'kaos-oversized-premium',
            'status' => 'archived',
        ])->assertRedirect();

        $this->assertNull($product->refresh()->labels);
    }

    public function test_upload_image_endpoint_works_without_image_driver(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload([
            'images' => [],
        ]));

        $product = Product::where('slug', 'kaos-oversized-premium')->firstOrFail();
        $baseline = $product->images()->count();

        $this->actingAs($this->admin)->post("/admin/products/{$product->id}/images", [
            'image' => $this->pngFile('tambahan.png'),
            'alt_text' => 'Foto tambahan dari edit',
        ])->assertSessionHas('success');

        $this->assertSame($baseline + 1, $product->images()->count());

        $path = $product->images()->latest('id')->first()->path;

        if (extension_loaded('gd') || extension_loaded('imagick')) {
            // Dengan driver gambar file hasil konversi ditulis ke disk publik asli
            $this->assertStringEndsWith('.webp', $path);
        } else {
            // Tanpa driver: file original tetap disimpan apa adanya
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_delete_image_removes_file_and_record(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/products', $this->payload());
        $product = Product::where('slug', 'kaos-oversized-premium')->firstOrFail();

        $image = $product->images()->where('is_primary', false)->firstOrFail();
        $path = $image->path;

        $this->actingAs($this->admin)->delete("/admin/products/{$product->id}/images/{$image->id}")
            ->assertSessionHas('success');

        $this->assertNull($product->images()->find($image->id));
        Storage::disk('public')->assertMissing($path);
    }
}
