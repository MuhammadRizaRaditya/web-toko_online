<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_login_redirects_each_role_to_dashboard(): void
    {
        foreach (['owner', 'admin', 'cashier', 'customer'] as $role) {
            $email = $role . '@dashboard-test.local';
            $user = User::factory()->create(['email' => $email]);
            $user->assignRole($role);

            $this->post('/login', [
                'email' => $email,
                'password' => 'password',
            ])->assertRedirect(route('dashboard'));

            $this->get('/dashboard')->assertOk();
            Auth()->logout();
        }
    }

    public function test_owner_dashboard_shows_owner_content_and_real_stats(): void
    {
        $owner = $this->createUserWithRole('owner');
        $order = Order::factory()->create(['grand_total' => 250000]);

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Omzet Total')
            ->assertSee('Produk Terlaris')
            ->assertSee('Panel Admin')
            ->assertSee($order->order_number)
            ->assertSee('Rp 250.000');
    }

    public function test_admin_dashboard_shows_admin_content(): void
    {
        $admin = $this->createUserWithRole('admin');
        Product::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Total Produk')
            ->assertSee('Pengelolaan Produk')
            ->assertSee('Varian & Stok')
            ->assertSee('Panel Admin');
    }

    public function test_cashier_dashboard_shows_pos_content_without_admin_panel(): void
    {
        $cashier = $this->createUserWithRole('cashier');

        $this->actingAs($cashier)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Buka POS')
            ->assertSee('Transaksi POS Hari Ini')
            ->assertSee('Riwayat Transaksi Terbaru')
            ->assertDontSee('Panel Admin');
    }

    public function test_customer_dashboard_shows_shopping_content_without_admin_or_pos(): void
    {
        $customer = $this->createUserWithRole('customer');
        $category = Category::factory()->create(['name' => 'Kaos Polos', 'is_active' => true]);
        $product = Product::factory()->create(['status' => 'active', 'category_id' => $category->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock_on_hand' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Belanja Sekarang')
            ->assertSee('Kategori Produk')
            ->assertSee('Produk Terbaru')
            ->assertSee($category->name)
            ->assertSee('Pesanan Terbaru')
            ->assertSee('Lihat keranjang')
            ->assertDontSee('Panel Admin')
            ->assertDontSee('Kasir (POS)')
            ->assertSee($product->name);
    }

    public function test_customer_cannot_access_admin_pages(): void
    {
        $customer = $this->createUserWithRole('customer');

        $this->actingAs($customer)->get('/admin/products')->assertStatus(403);
        $this->actingAs($customer)->get('/admin/categories')->assertStatus(403);
        $this->actingAs($customer)->get('/pos')->assertStatus(403);
    }

    public function test_cashier_cannot_access_admin_pages(): void
    {
        $cashier = $this->createUserWithRole('cashier');

        $this->actingAs($cashier)->get('/admin/products')->assertStatus(403);
        $this->actingAs($cashier)->get('/admin/variants')->assertStatus(403);
    }

    public function test_owner_and_admin_can_access_admin_pages(): void
    {
        $owner = $this->createUserWithRole('owner');
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($owner)->get('/admin/products')->assertOk();
        $this->actingAs($admin)->get('/admin/products')->assertOk();
    }

    public function test_sidebar_navigation_matches_role_permissions(): void
    {
        $customer = $this->createUserWithRole('customer');
        $cashier = $this->createUserWithRole('cashier');

        $customerPage = $this->actingAs($customer)->get('/dashboard')->assertOk();
        $customerPage->assertSee(route('catalog.index'));

        $cashierPage = $this->actingAs($cashier)->get('/dashboard')->assertOk();
        $cashierPage->assertSee(route('pos.index'));
        $cashierPage->assertDontSee(route('admin.products.index'));
    }
}
