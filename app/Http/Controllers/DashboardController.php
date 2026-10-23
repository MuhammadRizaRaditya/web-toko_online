<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const INVALID_REVENUE_STATUSES = ['cancelled', 'refunded', 'failed_delivery'];

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        abort_unless($user !== null, 403);

        if ($user->hasRole('owner')) {
            return view('dashboard.owner', $this->ownerData($user));
        }

        if ($user->hasRole('admin')) {
            return view('dashboard.admin', $this->adminData($user));
        }

        if ($user->hasRole('cashier')) {
            return view('dashboard.cashier', $this->cashierData($user));
        }

        return view('dashboard.customer', $this->customerData($user));
    }

    private function ownerData($user): array
    {
        $validRevenue = fn () => Order::query()->whereNotIn('order_status', self::INVALID_REVENUE_STATUSES);

        $stokMenipis = ProductVariant::query()
            ->where('is_active', true)
            ->whereRaw('stock_on_hand - stock_reserved <= low_stock_threshold')
            ->with(['product:id,name,slug'])
            ->orderByRaw('(stock_on_hand - stock_reserved) asc')
            ->limit(8)
            ->get();

        $produkTerlaris = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereNotIn('orders.order_status', self::INVALID_REVENUE_STATUSES)
            ->selectRaw('products.name as product_name, products.slug as product_slug')
            ->selectRaw('SUM(order_items.quantity) as total_qty')
            ->selectRaw('SUM(order_items.line_total) as total_revenue')
            ->groupBy('products.name', 'products.slug')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return [
            'user' => $user,
            'omzetTotal' => $validRevenue()->sum('grand_total'),
            'omzetBulanIni' => $validRevenue()->where('created_at', '>=', now()->startOfMonth())->sum('grand_total'),
            'totalPesanan' => Order::count(),
            'pesananBerjalan' => Order::whereNotIn('order_status', ['completed', 'cancelled', 'refunded', 'failed_delivery'])->count(),
            'totalProduk' => Product::count(),
            'totalVarian' => ProductVariant::count(),
            'stokMenipis' => $stokMenipis,
            'stokHabis' => ProductVariant::query()->whereRaw('stock_on_hand - stock_reserved <= 0')->count(),
            'pesananTerbaru' => Order::with('user:id,name')->latest()->limit(8)->get(),
            'produkTerlaris' => $produkTerlaris,
        ];
    }

    private function adminData($user): array
    {
        $stokMenipis = ProductVariant::query()
            ->where('is_active', true)
            ->whereRaw('stock_on_hand - stock_reserved <= low_stock_threshold')
            ->with(['product:id,name,slug'])
            ->orderByRaw('(stock_on_hand - stock_reserved) asc')
            ->limit(8)
            ->get();

        return [
            'user' => $user,
            'totalProduk' => Product::count(),
            'produkAktif' => Product::where('status', 'active')->count(),
            'totalKategori' => Category::count(),
            'totalVarian' => ProductVariant::count(),
            'varianAktif' => ProductVariant::where('is_active', true)->count(),
            'stokMenipis' => $stokMenipis,
            'stokHabis' => ProductVariant::query()->whereRaw('stock_on_hand - stock_reserved <= 0')->count(),
            'totalPesanan' => Order::count(),
            'pesananTerbaru' => Order::with('user:id,name')->latest()->limit(6)->get(),
        ];
    }

    private function cashierData($user): array
    {
        $posHariIni = Order::where('channel', 'pos')->whereDate('created_at', today());

        $stokMenipis = ProductVariant::query()
            ->where('is_active', true)
            ->whereRaw('stock_on_hand - stock_reserved <= low_stock_threshold')
            ->with(['product:id,name,slug'])
            ->orderByRaw('(stock_on_hand - stock_reserved) asc')
            ->limit(5)
            ->get();

        return [
            'user' => $user,
            'posHariIni' => $posHariIni->count(),
            'omzetHariIni' => (clone $posHariIni)
                ->whereNotIn('order_status', self::INVALID_REVENUE_STATUSES)
                ->sum('grand_total'),
            'transaksiTerbaru' => Order::where('channel', 'pos')
                ->with('user:id,name')
                ->latest()
                ->limit(8)
                ->get(),
            'stokMenipis' => $stokMenipis,
        ];
    }

    private function customerData($user): array
    {
        $kategori = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['children as children_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $produkTerbaru = Product::active()
            ->whereHas('variants', fn ($q) => $q->where('is_active', true)->whereRaw('stock_on_hand - stock_reserved > 0'))
            ->with(['category:id,name,slug', 'images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->latest()
            ->limit(8)
            ->get();

        return [
            'user' => $user,
            'kategori' => $kategori,
            'produkTerbaru' => $produkTerbaru,
            'pesananTerbaru' => Order::where('user_id', $user->id)
                ->with('payment')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
