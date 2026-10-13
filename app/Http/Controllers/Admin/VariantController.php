<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariantController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'size', 'color'])
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->product_id))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('sku', 'like', "%{$request->search}%")
                    ->orWhereHas('product', fn ($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            }))
            ->when($request->filled('low_stock'), fn ($q) => $q->whereRaw('stock_on_hand - stock_reserved <= low_stock_threshold'))
            ->when($request->filled('out_of_stock'), fn ($q) => $q->whereRaw('stock_on_hand - stock_reserved <= 0'))
            ->latest();

        $variants = $query->paginate(20)->withQueryString();
        $products = Product::where('status', 'active')->get();

        return view('admin.variants.index', compact('variants', 'products'));
    }

    public function create(?Product $product = null)
    {
        $products = $product ? collect([$product]) : Product::where('status', 'active')->get();
        $sizes = Size::where('is_active', true)->orderBy('sort_order')->get();
        $colors = Color::where('is_active', true)->get();

        return view('admin.variants.create', compact('products', 'sizes', 'colors', 'product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'barcode' => 'nullable|string|max:100|unique:product_variants,barcode',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_on_hand' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['low_stock_threshold'] = $validated['low_stock_threshold'] ?? 5;
        $validated['stock_reserved'] = 0;

        $variant = ProductVariant::create($validated);

        // Create initial stock movement
        if ($validated['stock_on_hand'] > 0) {
            $variant->stockMovements()->create([
                'movement_type' => 'purchase_receive',
                'quantity_delta' => $validated['stock_on_hand'],
                'reason' => 'Initial stock on creation',
                'actor_user_id' => auth()->id(),
                'reference_type' => 'product_variant',
                'reference_id' => $variant->id,
            ]);
        }

        return redirect()->route('admin.products.edit', $validated['product_id'])
            ->with('success', 'Varian berhasil dibuat.');
    }

    public function show(ProductVariant $variant)
    {
        $variant->load(['product', 'size', 'color', 'stockMovements' => fn ($q) => $q->latest()->take(20)]);

        return view('admin.variants.show', compact('variant'));
    }

    public function edit(ProductVariant $variant)
    {
        $products = Product::where('status', 'active')->get();
        $sizes = Size::where('is_active', true)->orderBy('sort_order')->get();
        $colors = Color::where('is_active', true)->get();

        return view('admin.variants.edit', compact('variant', 'products', 'sizes', 'colors'));
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
            'sku' => 'required|string|max:100|unique:product_variants,sku,'.$variant->id,
            'barcode' => 'nullable|string|max:100|unique:product_variants,barcode,'.$variant->id,
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_on_hand' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['low_stock_threshold'] = $validated['low_stock_threshold'] ?? 5;

        // Handle stock adjustment if changed
        $oldStock = $variant->stock_on_hand;
        $newStock = $validated['stock_on_hand'];

        DB::transaction(function () use ($variant, $validated, $oldStock, $newStock) {
            $variant->update($validated);

            if ($newStock !== $oldStock) {
                $delta = $newStock - $oldStock;
                $variant->adjustStock($delta, 'Manual stock adjustment from admin', auth()->id());
            }
        });

        return redirect()->route('admin.products.edit', $validated['product_id'])
            ->with('success', 'Varian berhasil diperbarui.');
    }

    public function destroy(ProductVariant $variant)
    {
        if ($variant->orderItems()->exists() || $variant->cartItems()->exists()) {
            return back()->withErrors(['error' => 'Varian tidak bisa dihapus karena sudah ada di order/keranjang.']);
        }

        $variant->delete();

        return redirect()->back()->with('success', 'Varian berhasil dihapus.');
    }

    // Stock adjustment
    public function adjustStock(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'delta' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($variant, $validated) {
                $variant->adjustStock(
                    $validated['delta'],
                    $validated['reason'],
                    auth()->id(),
                    'manual_adjustment'
                );
            });

            return redirect()->back()->with('success', 'Stok berhasil disesuaikan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyesuaikan stok: '.$e->getMessage()]);
        }
    }

    public function stockHistory(ProductVariant $variant)
    {
        $movements = $variant->stockMovements()
            ->with('actor')
            ->latest()
            ->paginate(20);

        return view('admin.variants.stock_history', compact('variant', 'movements'));
    }
}
